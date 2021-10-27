<?php
    require_once($_SERVER['DOCUMENT_ROOT']."/learnAPI/api/lib/Database.class.php");

class verifyAccount
{

         public function __construct($otp,$username)
        {
            $db = Database::getConnection();
            $query = "SELECT * FROM apis.auth WHERE (`userName` = '$username');";
            $result = mysqli_query($db,$query);
            if(1 == mysqli_num_rows($result))
            {
                $data = mysqli_fetch_row($result)[6];
                if($otp == $data)
                {
                    $query = "UPDATE `apis`.`auth` SET `active` = '1' WHERE (`userName` = $username );";
                    mysqli_query($db,$qurey);

                    //it's not working fix it later
                    if(mysqli_affected_rows($db))
                    {
                        return 1;
                    }
                    else
                        throw new exception("Cannot verify");
                    
                }
                else
                {
                    throw new exception("OTP IS WRONG");
                }
            }
            else
            {
                throw new exception("User not found please signup");
            }
        }
}
?>