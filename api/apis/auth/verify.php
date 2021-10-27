<?php

/* function verify to verify the user otp or email*/

${basename(__FILE__,'.php')} = function ()
    {
        if($this->get_request_method() == "POST" and isset($this->_request['otp'] ) and isset($this->_request['username']) )
        {
            $otp = $this->_request['otp'];
            $username = $this->_request['username'];
            try
            {
                $verifyAC = new verifyAccount($otp,$username);
                
                $data =[
                    "msg" => "Verify Sucessfully"
                ];

                $this->response($this->json($data),200);
            }
            catch (Exception $e)
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
            $this->response($this->json($data),200);
        }
    };
        
/* funciton verify end */
?>