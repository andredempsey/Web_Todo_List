<?php

class Filestore {

    public $filename = '';

    function __construct($filename = '') 
    {
        // Sets $this->filename
        $this->filename = $filename;
    }

    /**
     * Returns array of lines in $this->filename
     */
    function read_lines()
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
    function write_lines($array)
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
            return false;
        }
            return true;
    }

    /**
     * Reads contents of csv $this->filename, returns an array
     */
    function read_csv()
    {

    }

    /**
     * Writes contents of $array to csv $this->filename
     */
    function write_csv($array)
    {

    }

}








