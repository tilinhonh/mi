<?php
class Logger extends Zend_Log
{
    
    function __construct($logfile,$message)
    {
        
        $writer =  new Zend_Log_Writer_Stream($logfile);
        
        parent::__construct($writer);
        
        $this->info($message);
        
    }
    
}
?>