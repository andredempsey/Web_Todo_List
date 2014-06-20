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
    
    function inputValidation($todoItem)
    {
    	if (strlen(trim($todoItem)) == '')
    	{

    		throw new Exception('To Do items cannot be null.');
    		
    	}
    	elseif (strlen($todoItem) > 239) 
    	{
    		throw new Exception('Length of To Do items cannot exceed 239 characters.');
    	}
    	else
    	{
    		return $todoItem;
    	}
    }

    $fs = new Filestore(FILENAME);

	$items = $fs->read();
	//check if a value has been POSTED and it is not null
	if (isset($_POST['item']) && $_POST['item']!="")
	{
		if (count($items)!=0) 
		{
			$items = $fs->read();
		}
		//add todo item to list
		array_push($items,htmlspecialchars(strip_tags(inputValidation($_POST['item']))));
		$fs->write($items);
	}
	if (isset($_GET['item']) && $_GET['item']!="")
	{
		//delete respective todo item
		unset($items[$_GET['item']]);
		$fs->write($items);
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
			$items=$fs->read();
			//retrieve uploaded file contents
			$uf = new Filestore($savedFilename);
			$newList=$uf->read();
			//append file contents to current todo list
			$items=appendList($items,$newList);
			//update todo list file
			$fs->write($items);	
		}
	}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<meta charset="utf-8">
		<title>TODO List</title>
		<meta name="generator" content="Bootply" />
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<link href="css/bootstrap.min.css" rel="stylesheet">
		<!--[if lt IE 9]>
			<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		<link href="css/stylesheet.css" rel="stylesheet">
	</head>
	<body>
<div class="navbar navbar-default navbar-static-top">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#">To Do List</a>
    </div>
    <div class="collapse navbar-collapse">
      <ul class="nav navbar-nav">
        <li class="active"><a href="#">Home</a></li>
        <li><a href="#fileUpload">Upload File</a></li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="#about">About</a></li>
      </ul>
    </div><!--/.nav-collapse -->
  </div>
</div>

<div class="container">
  
</div><!-- /.container -->
	<form method="GET" action="todo_list.php">
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
	<h3 id="fileUpload">Upload File</h3>
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

	<!-- script references -->
		<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/scripts.js"></script>
	</body>
</html>



