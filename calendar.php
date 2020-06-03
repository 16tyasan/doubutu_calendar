<?php
	require_once("header.php");
	require_once("menu.php");
	require_once("connectvars.php");
	
	// Start the session
	session_start();

	// Connect to the database
	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	$query = "SELECT * FROM event_table";
	$event_data = mysqli_query($dbc, $query);
	if (mysqli_num_rows($event_data)) {
		$event_list = '';
		// リストボックスに表示するイベント情報を詰めた配列を作成
		foreach($event_data as $data) {
			$event_list .= "<option value=event'" . $data['event_id'] . "'>" . $data['event_name'] . "</option>";
		}
	}
	else {
		die('内部エラー：イベントテーブルの読み出しに失敗しました。');
	}
	
	// ログインしている場合のみ情報表示
	if (isset($_SESSION['user_id'])) {
		$user_id = $_SESSION['user_id'];
		
		// ＜または＞をクリックした場合、その月を表示する
		if ( isset($_GET['year']) && isset($_GET['month']) && $_GET['year'] !== '' && $_GET['month'] !== '' ) {
			$now_year = $_GET['year'];
			$now_month = $_GET['month'];
			
			if ($now_month == 13) {	// 12月の次の月を指定された場合は年を1つ増やして1月にする
				$now_year += 1;
				$now_month = 1;
			}
			else if ($now_month == 0) {	// 1月の前の月を指定された場合は年を1つ減らして1月にする
				$now_year -= 1;
				$now_month = 12;			
			}
			
			if ( ($now_year === date("Y")) && ($now_month === date("n")) ) {	// 今年の今月の場合
				$now_day = date("j"); // 現在の日を取得	
			}
			else {
				$now_day = 0; // 次が違うので現在の日を使わない	
			}
		}
		else {
			$now_year = date("Y"); //現在の年を取得
			$now_month = date("n"); //現在の月を取得
			$now_day = date("j"); // 現在の日を取得
		}
		
		// カレンダー作成(参考：https://php-beginner.com/sample/date_time/calendar2.html)
		$weekday = array("日","月","火","水","木","金","土"); //曜日の配列作成
		// 1日の曜日を数値で取得
		$fir_weekday = date( "w", mktime( 0, 0, 0, $now_month, 1, $now_year ) );
		$before_month = $now_month - 1;
		$after_month = $now_month + 1;

		$table = '<table border="1" style="text-align:center;">';
		// カレンダーのキャプションに年月を表示
		$table .= '<caption><a href="' . $_SERVER['PHP_SELF'] . '?year=' . $now_year . '&amp;month=' . $before_month . '">＜</a>　' . $now_year . "年" . $now_month . "月　" . 
					'<a href="' . $_SERVER['PHP_SELF'] . '?year=' . $now_year . '&amp;month=' . $after_month . '">＞</a></caption><br />';
		
		$table .= '<tr>';

		// 曜日セル<th>タグ設定
		$i = 0;
		while ($i <= 6) {
		    if( $i == 0 ){ // 日曜日の文字色
		        $style = "#C30";
		    }
		    else if( $i == 6 ){ // 土曜日の文字色
		        $style = "#03C";
		    }
		    else{ // 月曜～金曜日の文字色
		        $style = "black";
		    }
			$table .= "<th style=\"color:" . $style . "\">" . $weekday[$i] . "</th>";
			$i++;
		}
		
		$table .= '</tr><br /><tr>';

		$i = 0;
		while ( $i != $fir_weekday) { //１日の曜日まで空白（&nbsp;）で埋める
			$table .= '<td>&nbsp;</td><br />';
			$i++;
		}

		// カレンダーの日付を詰めた配列を作成
		for ($day = 1; checkdate( $now_month, $day, $now_year ); $day++) {

		    //曜日の最後まできたらカウント値（曜日カウンター）を戻して行を変える
		    if( $i > 6 ){
		        $i = 0;
		        $table .=  "</tr>";
		        $table .=  "<tr>";
		    }
		 
		//-------------スタイルシート設定-----------------------------------
		    if( $i == 0 ){ //日曜日の文字色
		        $style = "#C30";
		    }
		    else if( $i == 6 ){ //土曜日の文字色
		        $style = "#03C";
		    }
		    else{ //月曜～金曜日の文字色
		        $style = "black";
		    }
		 
		    // 今日の日付の場合、背景色追加
		    if( $day == $now_day ){
		        $style .= "; background:silver";
		    }
		//-------------スタイルシート設定終わり-----------------------------
		 
		    // 日付セル作成とスタイルシートの挿入
		    $table .=  "<td style=\"color:" . $style . ";\">" . $day . "</td>";
		 
		    $i++; //カウント値（曜日カウンター）+1
		}
		$table .= '</tr></table>';
		
		
		

$input_date = "2020-06-01";	// 仮

		// tableの表示
		echo $table; 
?>
		<hr>
			<p>追加する情報を選択して追加をクリックするか、削除をクリックしてください</p>
			<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
				<label for="date"><?php echo $input_date ?></label><br />
				<label for="event">イベント：</label>
				<select name="event" />
				<?php echo $event_list; ?>
				</select><br />
				<input type="submit" value="追加" name="submit" />
				<input type="submit" value="削除" name="delete" /><br />
			</form>

<hr>
<?php
	}
	else {	// 未ログインの場合、ログインを促す
		echo '<p><a href="login.php">ログイン</a>してください。</p>';
	}

	require_once("footer.php");
?>