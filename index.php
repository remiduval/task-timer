<!doctype html>

<html lang="en">
<head>
	<meta charset="utf-8">

	<title>Task timer</title>
	<meta name="description" content="Task timer">
	<meta name="author" content="Remi Duval">
	<meta name="viewport" content="width=device-width">

	<link rel="stylesheet" href="normalize.css">
	<link rel="stylesheet" href="style.css">

	<!--[if lt IE 9]>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.js"></script>
	<![endif]-->
</head>

<body>

	<?php

		include './Db.php';

		$db = new Db();

		function get_days() {
			global $db;
			$days = array();

			$results = $db -> select("
							SELECT DISTINCT day
							FROM task
							ORDER BY day DESC
			");

			foreach ($results as $day) {
				array_push($days, $day["day"]);
			}

			return $days;
		}


		function get_tasks($day) {
			global $db;

			return $db -> select("
							SELECT id, name, timer
							FROM task
							WHERE day = '$day'
							ORDER BY id DESC
			");
		}

		$days = get_days();
		$today = date('Y-m-d', time());
		$todayInDays = in_array( $today, $days );

		// Making sure there'll always be a Today task list
		if (!$todayInDays) { array_unshift($days, $today); }


		// Looping on days
		foreach ($days as $day) {
			echo "<div class='day'>";
			echo "<div class='total'></div>";

			$isToday = ($day == $today);

			if ($isToday) {
				echo "<h2>Today</h2>";
				// Adding the Add task line on top
				echo "<div class='task new'>";
					echo "<div class='action'></div>";
					echo '<input class="name" type="text" name="name" placeholder="Type new task" />';
					echo "<div class='timer' data-seconds=0></div>";
					echo "<div class='delete'>x</div>";
				echo '</div>';
			} else {
				$day_formatted = date_format( date_create($day), 'D j F y' );
				echo "<h2>".$day_formatted."</h2>";
			}
			
			// Looping on tasks for this day
			foreach(get_tasks($day) as $task) {
				echo "<div class='task' data-id=".$task['id'].">";
					echo "<div class='action'></div>";
					echo '<input class="name" type="text" name="name" value="'.$task['name'].'" disabled />';
					echo "<div class='timer' data-seconds=".$task['timer']."></div>";
					echo "<div class='delete'>x</div>";
				echo "</div>";
			}

			echo "</div>";
		}

	?>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script src="script.js"></script>
</body>
</html>