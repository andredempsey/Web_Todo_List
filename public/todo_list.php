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
		function readlist($filepathname, $target_array)
		{
		    if (is_readable($filepathname))
		    {
		        $read_handle = fopen($filepathname, "r");
		        $listitems = trim(fread($read_handle, filesize($filepathname)));
		        $listitems_array = explode("\n", $listitems);
		        foreach ($listitems_array as $item) 
		        {
		            array_push($target_array, $item);
		        }
		        fclose($read_handle);
		    }
		    else
		    {
		        echo "File not readable.  Please check the file name and path and try again. \n";
		    }
		        return $target_array;
		}
		
		function savetolist($filepathname, $item)
		{
		    $write_handle = fopen($filepathname, "a");
		    if (is_writable($filepathname))
		    {
		        fwrite($write_handle, "\n");
		        fwrite($write_handle, "$item");
		        fclose($write_handle);
		        $_POST['item']="";
		    }
		    else
		    {
		        echo "Invalid filename.  Please check the file name and path and try again. \n";
		        return false;
		    }
		        return true;
		}
		if (isset($_POST['item']))
		{
			savetolist("/vagrant/sites/todo.dev/public/data/list.txt", $_POST['item']);	
		}
		$items = readlist("/vagrant/sites/todo.dev/public/data/list.txt",[]);
		foreach ($items as $item) 
		{
			echo "<li>$item</li>";
		}
	?>
	</ul>
	<hr>
	<h3>Add a Todo Item</h3>
	<form method="POST">
		<p>
			<label for="item">New Item:</label>
			<input id="item" name = "item" type="text" placeholder="Enter todo list item">
			<input type="submit" value="Add to List">
		</p>
	</form>
</body>
</html>