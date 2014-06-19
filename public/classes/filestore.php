<?php

class Filestore {

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

    public function write($array)
    {
        if ($this->is_csv) 
        {
            $this->write_csv($array);
        }
        else 
        {
            $this->write_lines($array);
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
     * Writes each element in $array to a new line in $this->filename
     */
    private function write_lines($array)
    {
        $write_handle = fopen($this->filename, "w");
        if (is_writable($this->filename))
        {
            $newString=trim(implode(PHP_EOL, $array));
            fwrite($write_handle, "$newString");
            fclose($write_handle);
        }
        else
        {
            $errorMsg = "Invalid filename.  Please check the file name and path and try again. <br>". PHP_EOL;
        }
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

    /**
     * Writes contents of $array to csv $this->filename
     */
    private function write_csv($array)
    {
        if (is_writable($this->filename)) 
        {   
            $handle = fopen($this->filename, 'w');
            foreach ($array as $key=>$entry) 
            {
                fputcsv($handle, $entry);
            }
            fclose($handle);
        }
        return $array;
    }

}