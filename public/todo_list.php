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
				        $listitems_array = explode(PHP_EOL, $listitems);
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
			        echo "File not readable.  Please check the file name and path and try again. ". PHP_EOL;
			    }
			        return $listitems_array;
			}
			function append_list($existlist, $newlist)
			{
				foreach ($newlist as $listitem => $itemvalue) 
				{
					array_push($existlist,$itemvalue);
				}
				return $existlist;
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
			        $new_string=trim(implode(PHP_EOL, $newarray));
			        fwrite($write_handle, "$new_string");
			        fclose($write_handle);
					$_GET['item']='';
			    }
			    else
			    {
			        echo "Invalid filename.  Please check the file name and path and try again. ". PHP_EOL;
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
			if (isset($_GET['uploadlist']) && $_GET['uploadlist']!="")
			{
				//retrieve current todo list
				$items=read_list(FILENAME);
				//retrieve uploaded file contents
				$newlist=read_list($_GET['uploadlist']);
				//append file contents to current todo list
				$items=append_list($items,$newlist);
				//update todo list file
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
	<h3>Upload File</h3>
	<form method="POST" enctype="multipart/form-data" action="/todo_list.php">
	    <p>
	        <label for="file1">File to upload: </label>
	        <input type="file" id="file1" name="file1">
	    </p>
	    <p>
	        <input type="submit" value="Upload">
	    </p>
	</form>
	<?php
		// Verify there were uploaded files and no errors
		if (count($_FILES) > 0 && $_FILES['file1']['error'] == 0) 
		{
		    // Set the destination directory for uploads
		    $upload_dir = '/vagrant/sites/todo.dev/public/uploads/';
		    // Grab the filename from the uploaded file by using basename
		    $filename = basename($_FILES['file1']['name']);
		    // Create the saved filename using the file's original name and our upload directory
		    $saved_filename = $upload_dir . $filename;
		    // Move the file from the temp location to our uploads directory
		    move_uploaded_file($_FILES['file1']['tmp_name'], $saved_filename);
			// Check if we saved a file
			if ($_FILES['file1']['type']!='text/plain') //incorrect file type
			{
				echo "<p><strong>The file type cannot be processed.  Please try again with a text file.</strong></p>";
			}
			else
			{	
			    // If we did, show a link to the uploaded file
				echo "<p><a href='todo_list.php?uploadlist={$saved_filename}'><img HEIGHT='40' WIDTH='40' src='img/clickhere.jpeg' alt='click here'></a>
			    to add contents from <strong>" . $_FILES['file1']['name'] . "</strong> to your todo list<b></p>"; 
			}
		}
	?>
<img src="" alt="">
</body>
</html>