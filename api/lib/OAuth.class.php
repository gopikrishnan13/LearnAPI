<?php
    require_once($_SERVER['DOCUMENT_ROOT'].'/LearnAPI/api/lib/Database.class.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/LearnAPI/api/lib/user.class.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/LearnAPI/api/lib/Auth.class.php');

    class OAuth
    {
        private $db = NULL;
        private $refresh_token = NULL;
        private $access_token = NULL;
        private $valid_for = 7200;
        private $userN = NULL;
        private $user = NULL;

        public function __construct($username,$refresh_token = NULL)
        {
            $this->refresh_token = $refresh_token;
            $this->db = Database::getConnection();
            $this->user = new User($username);
            $this->userN = $this->user->getUserName();
        }

        public function setUsername($username)
        {
            // $this->username = $username;
            // $this->user = new user($this->username);
            // $this->userN = $this->user->getUserName();
        }


        public function newSession($valid_for = 7200)
        {
            
            $this->valid_for = $valid_for;
            $this->access_token = Auth::generateRandomHash(32);
            $this->refresh_token = Auth::generateRandomHash(32);
            $query = "INSERT INTO `apis`.`session` (`username`,`auth_token`,`refresh_token`,`valid_for`,`reference_token`) VALUES ('$this->userN', '$this->access_token', '$this->refresh_token', '$this->valid_for','auth_grant');";
            if(mysqli_query($this->db,$query))
            {
                return array(
                    "access_toke" => $this->access_token,
                    "valid_for" => $this->valid_for,
                    "refresh_token" => $this->refresh_token,
                    "type" => "api"
                );
            }
            else
            {
                throw new exception ("Unable to create a session");
            }
        }

        public function refreshAccess()
        {
            if($this->refresh_token)
            {
                $query = "SELECT * FROM `apis`.`sesssion` WHERE (`refresh_token` = '$this->refresh_token');";
                $result = myslqi_query($this->db, $query);
                if($result)
                {
                    $data = mysqli_fetch_assoc($result);
                    if($data['valid'] == 1)
                    {

                    }
                    else
                    {
                        throw new Exception(myslqi_error($this->db));
                    }

                }
                else
                {
                    throw new Exception("Invalid request ");
                }
            }
        }
    }

?>