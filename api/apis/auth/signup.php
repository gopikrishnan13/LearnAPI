<?php

${basename(__FILE__,'.php')} = function ()
{  
    
    if($this->get_request_method() == "POST" and isset($this->_request['username'] ) and isset($this->_request['email'] ) and isset($this->_request['password'] ) )
        {
            $username = $this->_request['username'];
            $password = $this->_request['password'];
            $email = $this->_request["email"];

            try
            {
                $s = new signup($username,$password,$email);
                $data = [
                    "message" => "Sigup success",
                    "userid" => $s->getInsertID()
                ];
                return $this->response($this->json($data),200);
            }
            catch (Exception $e)
            {
                $data = [
                    "error" => $e->getMessage(),

                ];
                return $this->response($this->json($data),200);
            }

        }
        else
        {
        }
    };
?>