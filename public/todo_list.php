<?
	require_once('classes/filestore.php');
	define('FILENAME','/vagrant/sites/todo.dev/public/data/list.txt');

	$errorMsg = '';

	function appendList($existList, $newList)
    {
        foreach ($newList as $listItem => $itemValue) 
        {
            array_push($existList,htmlspecialchars(strip_tags($itemValue)));
        }
        return $existList;
    }   

    $fs = new Filestore(FILENAME);

	$items = $fs->read_lines();
	if (isset($_POST['item']) && $_POST['item']!="")
	{
		if (count($items)!=0) 
		{
			$items = $fs->read_lines();
		}
		array_push($items,htmlspecialchars(strip_tags($_POST['item'])));
		$fs->write_lines($items);
	}
	if (isset($_GET['item']) && $_GET['item']!="")
	{
		unset($items[$_GET['item']]);
		$fs->write_lines($items);
		header('Location: /todo_list.php');
		exit;
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
			$items=$fs->read_lines();
			//retrieve uploaded file contents
			$uf = new Filestore($savedFilename);
			$newList=$uf->read_lines();
			//append file contents to current todo list
			$items=appendList($items,$newList);
			//update todo list file
			$fs->write_lines($items);	
		}
	}
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>TODO List</title>
	<link rel="stylesheet" href="/css/stylesheet.css">
</head>
<header>
	<h1>TODO List</h1>
</header>
<div>
<body>
	<form method="GET" action="todo_list.php">
		<hr>
		<ul>
			<? if (count($items)==0): ?>
				<?= "No items in list"; ?>
				<? else: ?>
					<? foreach ($items as $key => $item): ?>
						<li><button id='marked' name = 'item' value = <?=$key?>>X</button><?= htmlspecialchars(strip_tags($item))?></li> 
					<? endforeach; ?>
			<? endif; ?>
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
	<?= (is_null($errorMsg))?"":$errorMsg; ?>
</body>
<footer>&copy; Andre</footer>
</div>
</html>