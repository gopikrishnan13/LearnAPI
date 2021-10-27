<?php
    require_once("/var/www/html/learnAPI/api/lib/Database.class.php");

    $db = Database::getConnection();
    $query = "UPDATE `apis`.`auth` SET `active` = '1' WHERE (`userName` = 'gopikr');";
    $result = mysqli_query($db,$query);
    print_r(mysqli_affected_rows($db));
    //print(var_dump(mysqli_num_rows($result)));
    //print_r(mysqli_fetch_field_direct($result,2));
    // $s = mysqli_fetch_object($result);
    // print_r($s->otp);
    //if(mysqli_fetch_row($result)[6] == 6348) echo "pass"; else echo "poda";
   // echo mysqli_fetch_row($result)[6] == 6340 ? "pass" : "poda";
    //print_r(mysqli_field_count($db));

    
?>