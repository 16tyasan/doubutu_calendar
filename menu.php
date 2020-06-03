<?php
	// Start the session
	session_start();
	
	// ログインしている場合のみ情報表示
	if (isset($_SESSION['user_id'])) {
		echo '<p>' . $_SESSION['user_name'] . ' の島の情報</p>';
	    echo '<a href="logout.php">&diams;ログアウト</a>';
	}
	else {
		echo '<a href="login.php">ログイン</a>　/　';
		echo '<a href="signup.php">&diams;アカウント作成</a></p>';
	}
    echo '<hr>';
?>