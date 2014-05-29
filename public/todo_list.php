<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>TODO List</title>
</head>
<body>
	<h1>TODO List</h1>
	<hr>
	<ul>
	<?php 
		$sample_array = ['Sample Item 1', 'Sample Item 2', 'Sample Item 3', 'Sample Item 4'];
		foreach ($sample_array as $item) 
		{
			echo "<li>$item</li>";
		}
	?>
	</ul>
	<form method="POST">
	<!-- potential options for future implementation -->
	<!-- <p>
			<button id="remove">Remove Item</button>
			<button id="open">Open List</button>
			<button id="save">Save List</button>
			<button id="sort">Sort List</button>
			<button id="quit">Quit</button>
		</p> -->
	<hr>
	</form>
	<h3>Add a Todo Item</h3>
	<form method="POST" >
		<p>
			<label for="item">New Item:</label>
			<input id="item" name = "item" type="text" placeholder="Enter todo list item">
			<button id="add">Add Item</button>
		</p>
	</form>
</body>
</html>