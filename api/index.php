<?php
    //die(var_dump($_GET['namespace']));
    error_reporting(E_ALL ^ E_DEPRECATED);
    require_once($_SERVER['DOCUMENT_ROOT']."/LearnAPI/api/REST.api.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/LearnAPI/api/lib/Database.class.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/LearnAPI/api/lib/Signup.class.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/LearnAPI/api/lib/verify.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/LearnAPI/api/lib/login.class.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/LearnAPI/api/lib/user.class.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/LearnAPI/api/lib/Auth.class.php");

    class API extends REST {

        public $data = "";

        private $db = NULL;

        public function __construct()
        {
            parent::__construct();                         // Init parent contructor
            //$this->db = Database::getConnection();           // Initiate Database connection
        }


        /*
         * Public method for access api.
         * This method dynmically call the method based on the query string
         *
         */
        public function processApi(){
            $func = strtolower(trim(str_replace("/","",$_REQUEST['rquest'])));
            if((int)method_exists($this,$func) > 0)
            {
                $this->$func();
            }
            else
            {
            //$this->response($this->json(["msg" => "Bad Request"]),400);  // If the method not exist with in this class, response would be "Page not found".
                if(isset($_GET['namespace'])){
                    $dir = $_SERVER['DOCUMENT_ROOT'].'/LearnAPI/api/apis/'.$_GET['namespace'];
                    $file = $dir.'/'.$func.'.php';
                   //die(print(file_exists($file)));
                   if(file_exists($file)){
                       include $file;
                       $this->current_call = Closure::bind(${$func}, $this, get_class());
                       $this->$func();
                    } else {
                        $this->response($this->json(['error'=>'method_not_found']),404);
                    }
                } else {
                    //we can even process functions without namespace here.
                    $this->response($this->json(['error'=>'method_not_found']),404);
                }
            }
        }

        public function __call($method, $args)
        {
            if(is_callable($this->current_call))
            {
                return call_user_func_array($this->current_call, $args);
            } 
            else 
            {
                $this->response($this->json(['error'=>'methood_not_callable']),404);
            }
        }

        /*************API SPACE START*******************/

        private function about(){

            if($this->get_request_method() != "POST"){
                $error = array('status' => 'WRONG_CALL', "msg" => "The type of call cannot be accepted by our servers.");
                $error = $this->json($error);
                $this->response($error,406);
            }
            $data = array('version' => '0.1', 'desc' => 'This API is created by Blovia Technologies Pvt. Ltd., for the public usage for accessing data about vehicles.');
            $data = $this->json($data);
            $this->response($data,200);

        }
        /*
            function login()
            {
                if("POST" == $this->get_request_method() and isset($this->_request['username']) and isset($this->_request['password']))
                {
                    try
                    {
                        $s = new login($this->_request['username'],$this->_request['password']);
                        $data = [
                            "message" => "login sucessfully"
                        ];
                        $this->response($this->json($data),200);
                    }
                    catch (Exception  $e)
                    {
                        $data = [
                            "error" => $e->getMessage()
                        ];
                        $this->response($this->json($data),200);
                    }
                }
                else
                {
                    $data = [
                        "error" => "Bad request"
                    ];
                    $this->response($this->json($data),404);
                }
            }
            */

        private function test(){
                $data = $this->json(getallheaders());
                $this->response($data,101);
        }

        private function get_current_user(){
            $username = $this->is_logged_in();
            if($username){
                $data = [
                    "username"=> $username
                ];
                $this->response($this->json($data), 200);
            } else {
                $data = [
                    "error"=> "unauthorized"
                ];
                $this->response($this->json($data), 403);
            }
        }

        private function logout(){
            $username = $this->is_logged_in();
            if($username){
                $headers = getallheaders();
                $auth_token = $headers["Authorization"];
                $auth_token = explode(" ", $auth_token)[1];
                $query = "DELETE FROM session WHERE session_token='$auth_token'";
                $db = $this->dbConnect();
                if(mysqli_query($db, $query)){
                    $data = [
                        "message"=> "success"
                    ];
                    $this->response($this->json($data), 200);
                } else {
                    $data = [
                        "user"=> $this->is_logged_in()
                    ];
                    $this->response($this->json($data), 200);
                }
            } else {
                $data = [
                    "user"=> $this->is_logged_in()
                ];
                $this->response($this->json($data), 200);
            }
        }

        private function user_exists(){
            if(isset($this->_request['data'])){
                $data = $this->_request['data'];
                $db = $this->dbConnect();
                $result = mysqli_query($db, "SELECT id, username, mobile FROM users WHERE id='$data' OR username='$data' OR mobile='$data'");
                if($result){
                    $result = mysqli_fetch_array($result, MYSQLI_ASSOC);
                    $this->response($this->json($result), 200);
                } else {
                    $data = [
                        "error"=>"user_not_found"
                    ];
                    $this->response($this->json($data), 404);
                }

            } else {
                $data = [
                    "error"=>"expectation_failed"
                ];
                $this->response($this->json($data), 417);
            }
        }

        function generate_hash(){
            $bytes = random_bytes(16);
            return bin2hex($bytes);
        }

        function is_logged_in(){
            $headers = getallheaders();
            if(isset($headers["Authorization"])){
                $auth_token = $headers["Authorization"];
                $auth_token = explode(" ", $auth_token)[1];

                $query = "SELECT * FROM session WHERE session_token='$auth_token'";
                $db = $this->dbConnect();
                $_result = mysqli_query($db, $query);
                $d = mysqli_fetch_assoc($_result);
                if($d){
                    $data = $d['user_id'];
                    $result = mysqli_query($db, "SELECT id, username, mobile FROM users WHERE id='$data' OR username='$data' OR mobile='$data'");
                    if($result){
                        $result = mysqli_fetch_array($result, MYSQLI_ASSOC);
                        return $result["username"];
                    } else {
                        return false;
                    }

                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        private function genhash()
        {
            if(isset($this->_request['pass']))
            {
                $s = new signup("",$this->_request['pass'],"");
                $data = array(
                    "hash" => $s->hashPassword($this->_request['pass']),
                    "val" => $this->password
                );
                $data = $this->json($data,true);
                $this->response($data,200);
             }
        }




        /*************API SPACE END*********************/

        /*
            Encode array into JSON
        */
        private function json($data){
            if(is_array($data)){
                return json_encode($data, JSON_PRETTY_PRINT);
            } else {
                return "{}";
            }
        }

    }


    // Initiiate Library

    $api = new API;
    $api->processApi();

?>