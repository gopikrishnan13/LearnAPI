<?php
    require_once($_SERVER['DOCUMENT_ROOT'].'/LearnAPI/api/lib/Database.class.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/LearnAPI/api/lib/user.class.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/LearnAPI/api/lib/OAuth.class.php');
    require($_SERVER['DOCUMENT_ROOT'].'/LearnAPI/vendor/autoload.php');
        
    class Auth
    {
        private $username = NUll;
        private $user = NULL;
        private $loginTokens = NULL;
        private $db = NULL;

        public function __construct($username,$password=NULL)
        {
            $this->db = Database::getConnection();

            if(NULL == $password)
            {
                //token based auth
                $this->token = $username;
                $this->isTokenAuth = true;
                //we have to validate the token
            }
            else
            {
                //password based auth
                $this->username = $username;
                $this->password = $password;

            }

            if($this->isTokenAuth)
            {
                throw new Exception("Not Implemented");
            }
            else
            {
                //$query = "SELECT * FROM `apis`.`auth` WHERE (`userName` = '$this->username');";
                $user = new User($this->username);
                $hash = $user->getPasswordHash();
                if(password_verify($this->password,$hash))
                {
                    //generate token here
                    if(!$user->isActive())
                    {
                        throw new exception("Please check your email and activate your account.");
                    }
                    else
                    {
                        $this->loginTokens = $this->addSession();
                        //die('done');
                    }
                }
                else
                {
                    throw new Exception("password missmatch");
                }
                
            }
        }

        public function getAuthTokens()
        {
            return $this->loginTokens;
        }

        private function addSession()
        {
            $oauth = new OAuth($this->username);
            $session = $oauth->newSession();
            return $session;
        }

        public static function generateRandomHash($len)
        {
            $bytes = openssl_random_pseudo_bytes($len,$cstrong);
            return bin2hex($bytes);
        }
    }

?>