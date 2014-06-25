<?
	require_once('classes/dbStore.php');
	//initialize/declare variables
	$errorMsg='';
	$numRecords = 10;
	$offsetValue = 0;
	$pageNumber= 1;
	class InvalidInputException extends Exception {}

	//establish DB connection
	// Get new instance of PDO object
	$dbc = new PDO('mysql:host=127.0.0.1;dbname=todo_list', 'andre', 'password');

	// Tell PDO to throw exceptions on error
	$dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	// echo $dbc->getAttribute(PDO::ATTR_CONNECTION_STATUS) . "\n";
	//validate inputs to check for invalid spaces or excessive length
    function inputValidation($todoItem)
    {
    	if (strlen(trim($todoItem)) == '')
    	{

    		throw new InvalidInputException('To Do items cannot be null.');
    		
    	}
    	elseif (strlen($todoItem) > 239) 
    	{
    		throw new InvalidInputException('Length of To Do items cannot exceed 239 characters.');
    	}
    	else
    	{
    		return trim($todoItem);
    	}
    }
    //adds new item to the database
    function addItem(&$errorMessage, $dbc, $userInput)
    {
		try 
		{
			$stmt = $dbc->prepare('INSERT INTO todos (item) VALUES (:item)');
		    $stmt->bindValue(':item', $userInput, PDO::PARAM_STR);
		    $stmt->execute();
		 	$errorMessage = "Inserted ID: " . $dbc->lastInsertId();
			$_POST=[];	
		} 
		catch (Exception $e) 
		{
			$errorMessage=$e->getMessage();
		}
		echo "item add";
    }
    //delete item from database
    function deleteItem(&$errorMessage, $dbc, $userInput)
    {
    	$query='DELETE FROM todos WHERE id=:id';
		$stmt = $dbc->prepare($query);
		$stmt->bindValue(':id', $userInput, PDO::PARAM_INT);
		$stmt->execute();
    }
	//check if a value has been POSTED and it is not null; call add function
	if (isset($_POST['item']) && $_POST['item']!="")
	{
		addItem($errorMessage, $dbc, $_POST['item']);
	}

	//delete item from list and database
	if (isset($_POST['removeId']) && $_POST['removeId']!="")
	{
		//delete respective todo item
		deleteItem($errorMessage, $dbc, $_POST['removeId']);
		header("Location: todo_list.php");
		exit();
	}

	//determine total pages for entire data set
	$totalPages = ($dbc->query('SELECT * FROM todos')->rowCount()/$numRecords);

	//if page was changed, update data set using prepare statements and SQL SELECT query
	if (isset($_GET['Page']))
	{
		if ($_GET['Page'] > ceil($totalPages))
		{
			$pageNumber = ceil($totalPages);
			header("Location: todo_list.php?Page=$pageNumber");
			exit();
			$offsetValue = $numRecords * $pageNumber - $numRecords;	
		} 
		elseif ($_GET['Page'] >= 1)
		{
			$pageNumber = $_GET['Page'];
			$offsetValue = $numRecords * $pageNumber - $numRecords;
		} 
		else
		{
			$pageNumber = 1;
			header("Location: todo_list.php?Page=$pageNumber");
			exit();
			$offsetValue = $numRecords * $pageNumber - $numRecords;	
		}
	}

	//load list of todos from database
	$query = "SELECT * FROM todos LIMIT :numRecs OFFSET :offsetVal";
	$stmt = $dbc->prepare($query);
	$stmt->bindValue(':numRecs', $numRecords, PDO::PARAM_INT);
	$stmt->bindValue(':offsetVal', $offsetValue, PDO::PARAM_INT);
	$stmt->execute();
	$todoItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

	//determine count of records returned
	$results = $stmt->rowCount();


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
  			//retrieve uploaded file contents
  			$uf = new DBstore($savedFilename);
  			$newList=$uf->read();

  			//append file contents to current todo list
  			foreach ($newList as $key => $value) 
  			{
  				addItem($errorMessage, $dbc, $value);
  				$countInserts++;
  			}
			$totalPages = ceil($dbc->query('SELECT * FROM todos')->rowCount()/$numRecords);
  			header("Location: todo_list.php?Page={$totalPages}");
			exit();
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
		    </div><!--/.nav-collapse -->
		  </div>
		</div>		
		<div class="container">
			<h3 style= "margin-top: 0">Add a Todo Item</h3>
			<form method="POST" action="todo_list.php">
				<p>
					<input id="item" name = "item" type="text" placeholder="Enter todo list item">
					<input type="submit" value="Add to List">
				</p>
			</form>
		</div>

		<hr>

		<div class="container">		
			<? if ($results!=0): ?>
				<table class = "table table-striped">
					<?foreach ($todoItems as $key =>$todo) :?>
						<tr>
							<td><?= htmlspecialchars(strip_tags($todo['item']))?></td>
							<td><button class="btn btn-danger btn-sm pull-right btnRemove" data-todo="<?= $todo['id']; ?>">Remove</button></td>
						</tr> 
					<?endforeach;?>
				</table>
			<? endif; ?>
		</div>

		<p style="text-align: center">Page <?=$pageNumber?> of <?=ceil($totalPages)?> pages.</p>
		<p style="text-align: center">You are viewing <?=$results?> of <?=$totalPages*$numRecords?> total results.</p>
		<?= (is_null($errorMsg))?"":$errorMsg; ?>

		<form method="GET" action="/todo_list.php">
			<div style ="text-align: center"><ul class="pagination">
			  <li><a href="todo_list.php?Page=<?=$pageNumber-1?>">&laquo;</a></li>
			  	<?for ($i = 1; $i <= ceil($totalPages); $i++) : ?>
			  	<li><a href="todo_list.php?Page=<?=$i?>"><?=$i?></a></li>
				<?endfor;?>
			  <li><a href="todo_list.php?Page=<?=$pageNumber+1?>">&raquo;</a></li>
			</ul>
			</div>
		</form>

		<hr>
		
		<div class="container">
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
		</div>

		<form id="removeForm" method="POST" action="/todo_list.php"> 
			<input id="removeId" type="hidden" name="removeId" value="">
		</form>

		<script src="js/jquery.min.js"></script>
	    <script>
			$('.btnRemove').click(function () {
			    var todoId = $(this).data('todo');
			    if (confirm('Are you sure you want to remove item ' + todoId + '?')) {
			        $('#removeId').val(todoId);
			        $('#removeForm').submit();
			    }
			});
		</script>
		<script src="js/bootstrap.js"></script>
	</body>
</html>



