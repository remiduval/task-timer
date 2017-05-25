window.onload = function () {

	String.prototype.toHHMMSS = function () {
			var sec_num = parseInt(this, 10); // don't forget the second param
			var hours   = Math.floor(sec_num / 3600);
			var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
			var seconds = sec_num - (hours * 3600) - (minutes * 60);

			if (hours   < 10) {hours   = "0"+hours;}
			if (minutes < 10) {minutes = "0"+minutes;}
			if (seconds < 10) {seconds = "0"+seconds;}
			return hours+':'+minutes+':'+seconds;
	}
	
	// Initialize page
	init();


	// Every second, check if active tasks. If so, call increment
	window.setInterval(function(){
		if ( $(".task.active")[0] ) {
			incrementActiveTasks();
		}
	}, 1000);


	function init() {

		// Format initial timers
		$(".task").each(function() {
			$timer = $(".timer", this);
			$timer.text( String( $timer.data('seconds') ).toHHMMSS() );
		});

		// Set totals
		updateTotals();

	}


	// Update day totals
	function updateTotals() {
		$(".day").each(function() {
			$sum = 0;
			$('.timer', $(this)).each(function(){
				$sum += $(this).data('seconds');
			});
			$('.total', $(this)).text(String($sum).toHHMMSS());
		});
	}


	// Increment active tasks
	function incrementActiveTasks() {
		$(".task.active").each(function() {
			$timer = $(".timer", this);

			$newSeconds = parseInt($timer.data('seconds')) +1;

			$timer.data('seconds', $newSeconds);
			$timer.text( String($newSeconds).toHHMMSS() );
		});

		updateTotals();
	}


	// Click on action
	$(document).on('click', '.task .action', function() {
		$task = $(this).closest(".task");

		// Action = Add -> Add task to db, retrieve inserted ID, prepare new, blank task
		if ( $task.hasClass("new") ) {
			$name = $(".name", $task).val();

			$.ajax({
				url: 'ajax-functions.php',
				type: 'post',
				data: {'action': 'add_task', 'name': $name},
				success: function(lastInsertId, status) {
					console.log("Task added");
					$task.clone().insertBefore( $task );
					$task.attr('data-id', lastInsertId);
					$task.removeClass("new");
					$('.task.new .name').val('');
				},
				error: function(xhr, desc, err) {
					console.log(xhr);
					console.log("Details: " + desc + "\nError:" + err);
				}
			});

		// Action = Stop -> remove .active and update timer in db
		} else if ($task.hasClass("active")) {
			$id = $task.data('id');
			$timer = $(".timer", $task).data('seconds');

			$task.removeClass('active');

			$.ajax({
				url: 'ajax-functions.php',
				type: 'post',
				data: {'action': 'update_timer', 'id': $id, 'timer': $timer},
				success: function(data, status) {
					if(data == "1") {
						console.log("Timer updated");
					}
				},
				error: function(xhr, desc, err) {
					console.log(xhr);
					console.log("Details: " + desc + "\nError:" + err);
				}
			});

		// Action = Start -> set task as active
		} else {
			$task.addClass('active');
		}
		
	});


	// Click on x -> Delete task from db, remove from dom, update totals
	$(document).on('click', '.task .delete', function() {
		$task = $(this).closest(".task");
		$id = $task.data('id');

		$.ajax({
			url: 'ajax-functions.php',
			type: 'post',
			data: {'action': 'delete_task', 'id': $id},
			success: function(data, status) {
				if(data == '1') {
					console.log("Task removed");
					$task.remove();
					updateTotals();
				}
			},
			error: function(xhr, desc, err) {
				console.log(xhr);
				console.log("Details: " + desc + "\nError:" + err);
			}
		});

	});

}