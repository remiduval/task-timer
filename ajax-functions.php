<?php

	include './Db.php';
	$db = new Db();


	if($_POST['action'] == "add_task") {
		if ( isset($_POST['name']) ) {
			$name = $_POST['name'];
			add_task($name);
		}
	}

	function add_task($name) {
		global $db;

		$day = date('Y-m-d'); // Today

		$db -> query("
				INSERT INTO task (name, day, timer)
				VALUES ('$name', '$day', '0')
		");

		echo $db->lastInsertId();
	}



	if($_POST['action'] == "delete_task") {
		if ( isset($_POST['id']) && is_numeric($_POST['id']) ) {
			$id = $_POST['id'];
			delete_task($id);
		}
	}

	function delete_task($id) {
		global $db;

		echo $db -> query("
				DELETE FROM task 
				WHERE id = $id
		");
	}


	if($_POST['action'] == "update_timer") {
		if ( 	isset($_POST['id']) 	&& is_numeric($_POST['id']) 	&&
				isset($_POST['timer']) 	&& is_numeric($_POST['timer'])
		) {
			$id = $_POST['id'];
			$timer = $_POST['timer'];
			update_timer($id, $timer);
		}		
	}

	function update_timer($id, $timer) {
		global $db;

		echo $db -> query("
			UPDATE task 
			SET timer = $timer 
			WHERE id = $id
		");
	}

?>