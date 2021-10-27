<?php

class Database
{
    static $db = NULL;

    // Database connection

    public function getconnection()
    {

            $config_json = file_get_contents("/var/www/html/env.json");
            $config = json_decode($config_json,true);
            
            if (Database::$db != NULL) 
            {
				return Database::$db;
			} 
            else 
            {
				Database::$db = mysqli_connect($config['server'],$config['username'],$config['password'], $config['database']);
				if (!Database::$db) 
                {
					die("Connection failed: ".mysqli_connect_error());
				} 
                else 
                {
					return Database::$db;
				}
		    }

    }

}

?>