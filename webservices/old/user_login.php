<?php
//if (isset($_POST['userName']) && $_POST['userName'] != '' && isset($_POST['password']) && $_POST['password'] != '') {
 
    // include db handler
    require_once 'include/DB_Functions.php';
    $db = new DB_Functions();
 
    // response Array
    $response = array("status" => "", "message"=>"", "userId" => "");
 
    
        $username = "test";
//$_POST['userName'];
        $password = "test123";
//$_POST['password'];
 
        // check for user
        $user = $db->getUserByUsernameAndPassword($username, $password);
        if ($user != false) {
            // user found
            // echo json with status = 0
            $response["status"] = 0;
            $response["message"] = "Success";
            $response["userId"] = $user["admin_id"];
            
            echo json_encode($response);
        } else {
            // user not found
            // echo json with error = 1
            $response["status"] = -3;
            $response["message"] = "Invalid Login!";
            echo json_encode($response);
        }
    
//} else {
  //  echo "Access Denied";
//}
?>
