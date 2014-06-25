<?php

class DBstore {

    public $dbConnection = '';
    public $filename = '';
    public $numRecords = 10;
    public $offsetValue = 0;

    function __construct($filename = '') 
    {
        //establish DB connection
        // Get new instance of PDO object
        $this->dbConnection = new PDO('mysql:host=127.0.0.1;dbname=' . $filename, 'andre', 'password');

        // Tell PDO to throw exceptions on error
        $this->dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // echo $dbConnection->getAttribute(PDO::ATTR_CONNECTION_STATUS) . "\n";
    
    }

    //adds new item to the database
    function addItem($userInput)
    {
            $stmt = $this->dbConnection->prepare('INSERT INTO todos (item) VALUES (:item)');
            $stmt->bindValue(':item', $userInput, PDO::PARAM_STR);
            $stmt->execute();
            $errorMsg = "Inserted new item with ID: " . $this->dbConnection->lastInsertId();
            $_POST=[];  
    }
    //delete item from database
    function deleteItem(&$errorMsg, $userInput)
    {
        $query='DELETE FROM todos WHERE id=:id';
        $stmt = $this->dbConnection->prepare($query);
        $stmt->bindValue(':id', $userInput, PDO::PARAM_INT);
        $stmt->execute();
    }
    //determine total pages for entire data set
    public function pageCount()
    {
        return ($this->dbConnection->query('SELECT * FROM todos')->rowCount()/$this->numRecords);

    }
    //load list of todos from database
    public function showList()
    {
        $query = "SELECT * FROM todos LIMIT :numRecs OFFSET :offsetVal";
        $stmt = $this->dbConnection->prepare($query);
        $stmt->bindValue(':numRecs', $this->numRecords, PDO::PARAM_INT);
        $stmt->bindValue(':offsetVal', $this->offsetValue, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
   

}