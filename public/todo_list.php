<?
	// Get new instance of PDO object
	$dbc = new PDO('mysql:host=127.0.0.1;dbname=todo_list', 'andre', 'password');

	// Tell PDO to throw exceptions on error
	$dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	// echo $dbc->getAttribute(PDO::ATTR_CONNECTION_STATUS) . "\n";
	//initialize/declare variables
	$errorMsg='';
	$numRecords = 10;
	$offsetValue = 0;
	$pageNumber= 1;
	class InvalidInputException extends Exception {}

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

	//check if a value has been POSTED and it is not null; add item to table
	if (isset($_POST['item']) && $_POST['item']!="")
	{
			try {
				$stmt = $dbc->prepare('INSERT INTO todos (item) VALUES (:item)');
			    $stmt->bindValue(':item', $_POST['item'], PDO::PARAM_STR);
			    $stmt->execute();
			 	$errorMessage = "Inserted ID: " . $dbc->lastInsertId();
				$_POST=[];	
			} 
			catch (Exception $e) 
			{
				$errorMessage=$e->getMessage();
			}
	}

	//delete item from list and database
	if (isset($_GET['id']) && $_GET['id']!="")
	{
		//delete respective todo item
		$query='DELETE FROM todos WHERE id=:id';
		$stmt = $dbc->prepare($query);
		$stmt->bindValue(':id',  $_GET['id'],  PDO::PARAM_INT);
		$stmt->execute();
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
			<h3>Add a Todo Item</h3>
			<form method="POST" action="todo_list.php">
				<p>
					<label for="item">New Item:</label>
					<input id="item" name = "item" type="text" placeholder="Enter todo list item">
					<input type="submit" value="Add to List">
				</p>
			</form>
		</div>

		<form method="GET" action="/todo_list.php">
		<? if ($results!=0): ?>
			<?foreach ($todoItems as $key =>$todo) :?>
				<ul>
					<?foreach ($todo as $key =>$item) :?>
						<li><button id='marked' name = 'id' value = <?=$todo['id']?>>X</button><?= htmlspecialchars(strip_tags($todo['item']))?></li> 
					<!-- <?endforeach;?> -->
				</ul>
			<?endforeach;?>
		</form>
		<? endif; ?> 

		</table>
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

		<!-- script references -->
		<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/scripts.js"></script>
	</body>
</html>



