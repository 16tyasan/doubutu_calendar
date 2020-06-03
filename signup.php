<?php

	require_once("header.php");
	require_once("menu.php");

	require_once("connectvars.php");

	// Connect to the database
	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

	if (isset($_POST['submit'])) {
		// Grab the profile data from the POST
		$user_name = mysqli_real_escape_string($dbc, trim($_POST['user_name']));
		$password1 = mysqli_real_escape_string($dbc, trim($_POST['password1']));
		$password2 = mysqli_real_escape_string($dbc, trim($_POST['password2']));

		if (!empty($user_name) && !empty($password1) && !empty($password2) && ($password1 == $password2)) {
			// Make sure someone isn't already registered using this username
			$query = "SELECT * FROM user_table WHERE user_name = '$user_name'";
			$data = mysqli_query($dbc, $query);
			if (mysqli_num_rows($data) == 0) {
				// The username is unique, so insert the data into the database
				$query = "INSERT INTO user_table (user_name, password) VALUES ('$user_name', SHA('$password1'))";
				mysqli_query($dbc, $query);

				// Confirm success with the user
				echo '<p>アカウントを作成しました。<a href="login.php">ログイン</a>することができます。</p>';

				mysqli_close($dbc);
				exit();
		 	}
			else {
				// An account already exists for this username, so display an error message
				echo '<p class="error">このユーザ名はすでに使われています。別のユーザ名をご利用ください。</p>';
				$user_name = "";
			}
		}
		else {
		  echo '<p class="error">エラー：サインアップには全てのデータを入力する必要があります。パスワードは2回入力してください。</p>';
		}
	}

	mysqli_close($dbc);
	?>



	<p>ユーザ名とパスワードを入力してアカウントを作成してください。</p>	
	<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<fieldset>
		<legend>登録情報</legend>
		<label for="user_name">ユーザ名：</label>
		<input type="text" id="user_name" name="user_name" value="<?php if (!empty($user_name)) echo $user_name; ?>" /><br />
		<label for="password1">パスワード：</label>
		<input type="password" id="password1" name="password1" /><br />
		<label for="password2">パスワード（もう一度）：</label>
		<input type="password" id="password2" name="password2" /><br />
	</fieldset>
	<input type="submit" value="サインアップ" name="submit" />
	</form>

<?php
  // Insert the page footer
  require_once('footer.php');
?>
