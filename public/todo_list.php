<?php
	define('FILENAME','/vagrant/sites/todo.dev/public/data/list.txt');
	$errorMsg = '';
	function readList($filePathName)
	{
	    if (is_readable($filePathName))
	    {
	        $readHandle = fopen($filePathName, "r");
	        if (filesize($filePathName)>0) 
	        {
		        $listItems = trim(fread($readHandle, filesize($filePathName)));
		        $listItemsArray = explode(PHP_EOL, $listItems);
		        fclose($readHandle);
	        }
	        else
	        {
		        fclose($readHandle);
	        	$listItemsArray=array();
	        }
	    }
	    else
	    {
	        $errorMsg = "File not readable.  Please check the file name and path and try again. ". PHP_EOL;
	    }
	        return $listItemsArray;
	}
	function appendList($existList, $newList)
	{
		foreach ($newList as $listItem => $itemValue) 
		{
			array_push($existList,$itemValue);
		}
		return $existList;
	}	

	function updateList($filePathName, $newArray)
	{
	    $write_handle = fopen($filePathName, "w");
	    if (is_writable($filePathName))
	    {
	        $newString=trim(implode(PHP_EOL, $newArray));
	        fwrite($write_handle, "$newString");
	        fclose($write_handle);
	    }
	    else
	    {
	        $errorMsg = "Invalid filename.  Please check the file name and path and try again. <br>". PHP_EOL;
	        return false;
	    }
	        return true;
	}
	$items = readList(FILENAME);
	if (isset($_POST['item']) && $_POST['item']!="")
	{
		if (count($items)!=0) 
		{
			$items = readList(FILENAME);
		}
		array_push($items,$_POST['item']);
		updateList(FILENAME, $items);
	}
	if (isset($_GET['item']) && $_GET['item']!="")
	{
		unset($items[$_GET['item']]);
		updateList(FILENAME, $items);
	}
	// Verify there were uploaded files and no errors
	if (count($_FILES) > 0 && $_FILES['file1']['error'] == 0) 
	{
	    // Set the destination directory for uploads
	    $uploadDir = '/vagrant/sites/todo.dev/public/uploads/';
	    // Grab the filename from the uploaded file by using basename
	    $filename = basename($_FILES['file1']['name']);
	    // Create the saved filename using the file's original name and our upload directory
	    $savedFilename = $uploadDir . $filename;
	    // Move the file from the temp location to our uploads directory
	    move_uploaded_file($_FILES['file1']['tmp_name'], $savedFilename);
		// Check if we saved a file
		if ($_FILES['file1']['type']!='text/plain') //incorrect file type
		{
			$errorMsg = "<p><strong>The file type cannot be processed.  Please try again with a text file.</strong></p>";
		} 
		else
		{
			//retrieve current todo list
			$items=readList(FILENAME);
			//retrieve uploaded file contents
			$newList=readList($savedFilename);
			//append file contents to current todo list
			$items=appendList($items,$newList);
			//update todo list file
			updateList(FILENAME, $items);	
		}
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
				if (count($items)==0) 
				{
					echo "<p>No items in list</p>";
				}
				else
				{
		
					foreach ($items as $key => $item) 
						{
							echo "<li><button id='marked' name = 'item' value = $key>Mark Complete</button>$item</li>";
						}
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
	<?php echo (is_null($errorMsg))?"":$errorMsg;?>
</body>
</html>