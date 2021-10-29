<?php
    require_once($_SERVER['DOCUMENT_ROOT'].'/LearnAPI/api/lib/Database.class.php');
    require ($_SERVER['DOCUMENT_ROOT'].'/LearnAPI/vendor/autoload.php');

    class User
    {
        private $db = NULL;
        private $user = NULL;

        public function __construct($username)
        {
            //die($username);
            $db = Database::getConnection();
            $query = "SELECT * FROM `apis`.`auth` WHERE (`userName` = '$username' OR `email` = '$username');";
            $result = mysqli_query($db,$query);
            if(1 == mysqli_num_rows($result))
            {
                $this->user = mysqli_fetch_array($result);
                
            }
            else
            {
                throw new Exception("User not found");
            }
        }

        public function getUserName()
        {
            return $this->user['userName'];
        }

        public function getPasswordHash()
        {
            return $this->user['password'];
        }
        public function getEmail()
        {
            return $this->user['email'];
        }
        public function isActive()
        {
            return $this->user['active'];
        }
    }

?>