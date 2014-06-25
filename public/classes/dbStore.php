<?php

class DBstore {

    public $dbConnection = '';
    public $filename = '';
    public $stmt = null;
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
    function addItem(&$errorMsg, $userInput)
    {
        try 
        {
            $this->stmt = $this->dbConnection->prepare('INSERT INTO todos (item) VALUES (:item)');
            $this->stmt->bindValue(':item', $userInput, PDO::PARAM_STR);
            $this->stmt->execute();
            $errorMsg = "Inserted new item with ID: " . $this->dbConnection->lastInsertId();
            $_POST=[];  
        } 
        catch (Exception $e) 
        {
            $errorMsg=$e->getMessage();
        }
    }
    //delete item from database
    function deleteItem(&$errorMsg, $userInput)
    {
        $query='DELETE FROM todos WHERE id=:id';
        $this->stmt = $this->dbConnection->prepare($query);
        $this->stmt->bindValue(':id', $userInput, PDO::PARAM_INT);
        $this->stmt->execute();
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
        $this->stmt = $this->dbConnection->prepare($query);
        $this->stmt->bindValue(':numRecs', $this->numRecords, PDO::PARAM_INT);
        $this->stmt->bindValue(':offsetVal', $this->offsetValue, PDO::PARAM_INT);
        $this->stmt->execute();
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}