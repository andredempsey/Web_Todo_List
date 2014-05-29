		<?php
			define('FILENAME','/vagrant/sites/todo.dev/public/data/list.txt');
			function read_list($filepathname)
			{
			    if (is_readable($filepathname))
			    {
			        $read_handle = fopen($filepathname, "r");
			        if (filesize($filepathname)>0) 
			        {
				        $listitems = trim(fread($read_handle, filesize($filepathname)));
				        $listitems_array = explode("\n", $listitems);
				        fclose($read_handle);
			        }
			        else
			        {
				        fclose($read_handle);
			        	$listitems_array=array();
			        }
			    }
			    else
			    {
			        echo "File not readable.  Please check the file name and path and try again. \n";
			    }
			        return $listitems_array;
			}

			function show_list($items)
			{
				foreach ($items as $key => $item) 
				{
					echo "<li><button id='marked' name = 'item' value = $key>Mark Complete</button>$item</li>";
				}
			}
			function update_list($filepathname, $newarray)
			{
			    $write_handle = fopen($filepathname, "w");
			    if (is_writable($filepathname))
			    {
			        $new_string=trim(implode("\n", $newarray));
			        fwrite($write_handle, "$new_string");
			        fclose($write_handle);
					$_GET['item']='';
			    }
			    else
			    {
			        echo "Invalid filename.  Please check the file name and path and try again. \n";
			        return false;
			    }
			        return true;
			}
			?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>TODO List</title>
</head>
<body>
	<form method="GET" action="todo_list.php">
		<h1>TODO List</h1>
		<hr>
		<ul>
			<?php
			$items = read_list(FILENAME);
			if (isset($_POST['item']) && $_POST['item']!="")
			{
				if (count($items)!=0) 
				{
					$items = read_list(FILENAME);
				}
				array_push($items,$_POST['item']);
				update_list(FILENAME, $items);
			}
			if (isset($_GET['item']) && $_GET['item']!="")
			{
				unset($items[$_GET['item']]);
				update_list(FILENAME, $items);
			}
			if (count($items)==0) 
			{
				echo "<p>No items in list</p>";
			}
			else
			{
				show_list($items);
			}	
		?>
		</ul>
	</form>
	<hr>
	<h3>Add a Todo Item</h3>
	<form method="POST" action="todo_list.php">
		<p>
			<label for="item">New Item:</label>
			<input id="item" name = "item" type="text" placeholder="Enter todo list item">
			<input type="submit" value="Add to List">

		</p>
	</form>
</body>
</html>