<?php 
class QueryLogger
{
    public function __construct()
    {

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

		$profiler = $db->getProfiler();
        
		$profiles = $profiler->getQueryProfiles(
		                        Zend_Db_Profiler::DELETE |
                                Zend_Db_Profiler::INSERT |
                                Zend_Db_Profiler::UPDATE
                              );

                              
        foreach($profiles as $profile){
            
    		$user       = '';

    		$ip         = $_SERVER['REMOTE_ADDR'];
            
            $url        = $_SERVER['REQUEST_URI'];
            
            $query      = $profile->getQuery();

    		$parameters = $profile->getQueryParams();

           // $elapsedTime= $profile->getTotalElapsedSecs();
           
    		
    		$string=$profiler->getLastQueryProfile()->getQuery();
    		//SELECT `cidades`.* FROM `cidades` WHERE (`cidades`.`id` = 12754) LIMIT 11
    		
    		$pattern='/=\s(\w*)\)/';

    		preg_match($pattern,$string,$output);
    		
    		$id=$output[1];

            try{
                /**
                 * Try Write it!
                 */
                
                $msg="\n------------------------------------------------------";
                $msg.="\n";
                $msg.="USER:\t\t $user";
                $msg.="\n";
                $msg.="IP:\t\t $ip";
                $msg.="\n";
                $msg.="URL:\t\t $url";
                $msg.="\n";
                $msg.="QUERY:\t\t $query";
                $msg.="\n";
                $msg.="PARAMETERS:\t ". implode(', ', $parameters);
                $msg.="\n";
             //   $msg.="ELAPSED TIME: $elapsedTime";
                $msg.="REGISTER ID:\t$id";
                $msg.="\n";
                $msg.="\n";
                
                
                
                $logfile='/var/log/zend/queryTesting.log';
                
                new Logger($logfile,$msg);
                
            }
            catch (Exception $e){
                
                $message = 'Query Could not be loggged.';
                
                throw $e;
            }
          
        }
    }
}
?>