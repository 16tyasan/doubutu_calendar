<?php
	require_once("header.php");
	require_once("menu.php");

	require_once("connectvars.php");
	// Start the session
	session_start();

	// Clear the error message
	$error_msg = "";

	// If the user isn't logged in, try to log them in
	if (!isset($_SESSION['user_id'])) {
		if (isset($_POST['submit'])) {
			// Connect to the database
			$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

			// Grab the user-entered log-in data
			$user_name = mysqli_real_escape_string($dbc, trim($_POST['user_name']));
			$password = mysqli_real_escape_string($dbc, trim($_POST['password']));

			if (!empty($user_name) && !empty($password)) {
				// Look up the username and password in the database
				$query = "SELECT user_id, user_name FROM user_table WHERE user_name = '$user_name' AND password = SHA('$password')";
				$data = mysqli_query($dbc, $query);

				if (mysqli_num_rows($data) == 1) {
					// The log-in is OK so set the user ID and username session vars (and cookies), and redirect to the home page
					$row = mysqli_fetch_array($data);
					$_SESSION['user_id'] = $row['user_id'];
					$_SESSION['user_name'] = $row['user_name'];
					setcookie('user_id', $row['user_id'], time() + (60 * 60 * 24 * 30));    // expires in 30 days
					setcookie('user_name', $row['user_name'], time() + (60 * 60 * 24 * 30));  // expires in 30 days
					$home_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/calendar.php';
					header('Location: ' . $home_url);
				}
			    else {
					// The username/password are incorrect so set an error message
					$error_msg = 'エラー：正しいユーザ名とパスワードを入力してください。';
			    }
			}
			else {
				// The username/password weren't entered so set an error message
				$error_msg = 'エラー：ユーザ名とパスワードが入力されていません。';
			}
		}
	}

	// Insert the page header
	$page_title = 'ログイン';
	require_once('header.php');

	// If the session var is empty, show any error message and the log-in form; otherwise confirm the log-in
	if (empty($_SESSION['user_id'])) {
		echo '<p class="error">' . $error_msg . '</p>';
		?>

		<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<fieldset>
				<legend>ログイン</legend>
				<label for="user_name">ユーザ名：</label>
				<input type="text" name="user_name" value="<?php if (!empty($user_name)) echo $user_name; ?>" /><br />
				<label for="password">パスワード：</label>
				<input type="password" name="password" />
			</fieldset>
			<input type="submit" value="ログイン" name="submit" />
		</form>

	<?php
	}
	else {
		echo('<p class="login">' . $_SESSION['user_id'] . ' ' . $_SESSION['user_name'] . 'としてログイン中です。3秒後に元の画面に戻ります。</p>');
		$home_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/index.php';
		header('Refresh:3; url=' . $home_url);

	}
?>

<?php
	// Insert the page footer
	require_once('footer.php');
?>
