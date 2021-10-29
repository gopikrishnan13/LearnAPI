<?php
    
    ${basename(__FILE__,'.php')} = function()
    {
        if($this->get_request_method() == 'POST' and isset($this->_request['username']) and isset($this->_request['password']))
        {
            $username =$this->_request['username'];
            $password = $this->_request['password'];
            try
            {
                $auth = new Auth($username,$password);
                //die(var_dump($auth));
                $data = [
                    "msg" => "Login sucess",
                    "tokens" => $auth->getAuthTokens()
                ];

                $this->response($this->json($data),200);
            }
            catch (Exception $e)
            {
                $data = [
                    "error" => $e->getMessage()
                ];
                $data = $this->json($data);
                $this->response($data,406);
            }
        }
        else
        {
            $data = [
                "error" => "Bad Request"
            ];
            $data = $this->json($data);
            return $this->response($data,404);
        }
    }

?>