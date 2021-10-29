<?php
    require($_SERVER['DOCUMENT_ROOT'].'/LearnAPI/vendor/autoload.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/LearnAPI/api/lib/user.class.php');

    try
    {
    $user = new User('sugu');
    echo $user->getEmail();
    }
    catch(exception $e)
    {
        echo $e;
    }

?>