<?php
    require_once($_SERVER['DOCUMENT_ROOT']."/learnAPI/api/lib/Database.class.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/learnAPI/api/lib/Signup.class.php");


    class login
    {
        public function __construct($username,$password)
        {
            $db = Database::getConnection();
            $query = "SELECT * FROM `apis`.`auth` WHERE (`userName` = '$username');";
            $result= mysqli_query($db,$query);
            //die(print_r(mysqli_num_rows($result)));
            if(1 == mysqli_num_rows($result))
            {
                $data = mysqli_fetch_assoc($result);
                if(password_verify($password,$data['password']))
                {
                    return 1;
                }
                else
                {
                    throw new exception("Password Wrong");
                }
            }
            else
            {
                throw new exception("User not found please signup");
            }

        }
    }

?>