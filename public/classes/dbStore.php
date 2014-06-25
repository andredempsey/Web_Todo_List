<?php

class DBstore {

    public $filename = '';
    private $is_csv = false;

    function __construct($filename = '') 
    {
        // Sets $this->filename
        $this->is_csv = (substr($filename, -3) == 'csv');
        $this->filename = $filename;
    }

    public function read()
    {
        if ($this->is_csv)
        {
            return $this->read_csv();
        }
        else
        {
            return $this->read_lines();
        }
    }

    /**
     * Returns array of lines in $this->filename
     */
    private function read_lines()
    {
        if (is_readable($this->filename))
        {
            $readHandle = fopen($this->filename, "r");
            if (filesize($this->filename)>0) 
            {
                $listItems = trim(fread($readHandle, filesize($this->filename)));
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

    /**
     * Reads contents of csv $this->filename, returns an array
     */
    private function read_csv()
    {
        $addressBook=[];
        // Code to read file $this->filename
        $handle = fopen($this->filename, 'r');
        while(!feof($handle)) 
        {
            $row=fgetcsv($handle);      
            if (is_array($row)) 
            {
                $addressBook[] = $row;
            }
        }
        fclose($handle);
        return $addressBook;
    }

}