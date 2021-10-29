<?php
    require_once($_SERVER['DOCUMENT_ROOT']."/LearnAPI/api/lib/Database.class.php");
    require($_SERVER['DOCUMENT_ROOT']."/LearnAPI/vendor/autoload.php");

class signup
{   
    static $username = NULL;
    static $password = NULL;
    static $email = NULL;

    static $db = NULL;

    public function __construct($username,$password,$email)
    {
        $this->db = Database::getConnection();
        $this->username = $username;
        $this->email = $email;
        $this->password = self::hashPassword($password);
        if($this->userExists())
        {
            throw exception("User alreay exists");
        }
        $bytes = random_bytes(16);
        $this->token = $token = bin2hex($bytes);
        $this->otp = rand(1111,9999);
        $query = "INSERT INTO `apis`.`auth` ( `userName`, `password`, `email`, `active`, `token`,`otp`) VALUES ('$this->username', '$this->password', '$this->email', '0', 'this->$token', '$this->otp');";

        if(!mysqli_query($this->db, $query))
        {   
            //print_r($this->db);
            throw new exception("Unable to signup");
        }
        else
        {
            $this->id = mysqli_insert_id($this->db);
            $this->sendVerificationMail();
        }

    }

    public function userExists()
    {
        
        return false;
    }

    public function sendVerificationMail()
    {

        $config_json = file_get_contents("/var/www/html/env.json");
        $config = json_decode($config_json,true);
        $token = $this->token;
        $otp = $this->otp;
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom("gopikrishnan8015@gmail.com", "LearnAPI");
        $email->setSubject("Verify Your Account");
        $email->addTo($this->email,$this->username);
        //$email->addContent("text/plain","please verify your acoount at: http://127.0.0.1/learnAPI/api/verify?token=$token");
        $email->addContent(
            "text/html", "<strong>OTP : $otp <br> please verify your account at: http//www.google.com or <a href=\"http://www.google.com\" > CLICKING HERE </a> , to verify your account.  </strong>"
        );
        $sendgrid = new \SendGrid($config['apiKey']);
        try
        {
            $response = $sendgrid->send($email);
        }
        catch (Expection $e)
        {
            echo 'Caught exception:'.$e->getMessage()."\n";
        }
    }
    
    public function getInsertID()
    {
        return $this->id;
    }

    public function hashPassword($password)
    {
        $options = [
            "cost" => 12,
        ];

        return password_hash($password,PASSWORD_BCRYPT,$options);
    }

    
}

?>