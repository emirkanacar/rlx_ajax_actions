<?php
session_start();

try {
	$db = new PDO('mysql:host=localhost;dbname=rlx;charset=utf8', 'root', '');
} catch (PDOException $e) {
	echo 'Error: ' . $e->getMessage();
}

if(!isset($_GET['page']))
{
	$_GET['page'] = 1;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<link rel="stylesheet" href="assets/semantic.min.css">
	<link rel="stylesheet" href="assets/app.css">
	<script src="assets/jquery.min.js"></script>
	<script src="assets/semantic.min.js"></script>
	<script src="assets/app.js"></script>
	<title>rlx ajax page</title>
</head>

<body>

	<div class="ui container grid">
		<h1 class="ui center aligned container">Actions</h1>
		<div class="three wide column"></div>
		<div class="eight wide column rlx_column">
			<form class="ui form rlxForm" method="post" accept-charset="utf-8" action="">
				<div class="field">
					<label>Name</label>
					<input class="first-name" type="text" name="first-name" placeholder="Name">
				</div>
				<div class="field">
					<label>Lastname</label>
					<input class="last-name" type="text" name="last-name" placeholder="Lastname">
				</div>
				<div class="field">
					<label>Email</label>
					<input class="email" type="email" name="email" placeholder="Email">
				</div>
				<input type="hidden" value="is_form" name="type">
				<div class="field">
					<label>Username</label>
					<input class="username" type="text" name="username" placeholder="Username">
				</div>
				<button class="ui button rlxSubmit" type="button">Submit</button>
			</form>
		</div>
		<div class="one wide column"></div>
		<?php

		$limit = 10; 

		if($_GET['page'] == 1)
		{
			$calcLimit = 0;
		}
		else {
			$calcLimit = (($_GET['page'] - 1) * 10) ;
		}

		$data = $db->query("SELECT * FROM form_data ORDER BY id ASC LIMIT $calcLimit,$limit ", PDO::FETCH_ASSOC);
		$totalDataQuery = $db->query("SELECT * FROM form_data", PDO::FETCH_ASSOC);
		$dataCount = $totalDataQuery->rowCount();

		echo
			'
				<table class="ui celled table" style="padding:0px!important;">
				<thead>
					<tr>
						<th>#</th>
						<th>Name</th>
						<th>Lastname</th>
						<th>Username</th>
						<th>Email</th>
					</tr>
				</thead>
				<tbody class="rlxTbody">
				';
		foreach ($data as $row) {
			echo '
					<tr>
						<td data-label="#">' . $row['id'] . '</td>
						<td data-label="Name">' . $row['first_name'] . '</td>
						<td data-label="Lastname">' . $row['last_name'] . '</td>
						<td data-label="Username">' . $row['username'] . '</td>
						<td data-label="Email">' . $row['email'] . '</td>
					</tr>
					';
		}
		echo '
				</tbody>
			</table>
				';

		$base = 1;


		if(is_float($dataCount / 10))
		{
			$totalPages = floor($dataCount / 10) + 1;
		}
		else {
			$totalPages = $dataCount / 10;
		}
		

		if($totalPages <= 0)
		{
			if($dataCount <= 9)
			{
				$totalPages = 1;
			}
		}
		

		if($dataCount > 0)
		{
			echo '<div class="ui pagination menu">';

			for($base; $totalPages >= $base; $base++)
			{

				if($_GET['page'] == $base)
				{
					$type = 'active';
				}
				else 
				{
					$type = '';
				}
				echo '
				<a class="item '.$type.' " href="/?page='.$base.'">
					'.$base.'
				</a>
				';
			}
	
			echo '</div>';
		}

		?>
	</div>
	<br>
		<br>
		<br>
</body>

</html>