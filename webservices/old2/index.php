<?php
//
//index.php – This file plays role of accepting requests and giving response. This file accepts all GET and POST requests. On each request it will talk to database and will give appropriate response in JSON format.
/**
 * File to handle all API requests
 * Accepts GET and POST
 *
 * Each request will be identified by TAG
 * Response will be JSON data
 
  /**
 * check for POST request
 */
//if (isset($_POST['accountId']) && $_POST['accountId'] != '') {
 
    // include db handler
    require_once 'include/DB_Functions.php';
    $db = new DB_Functions();
 
    // response Array
    $response = array("status" => "", "message"=>"", "DeliveryOPS"=>array());
 
    
        $accountId = 14;
//$_POST['accountId'];
 
        // check for user
        $ops = $db->getDeliveryOPS($accountId);
        if ($ops != false) {
            // user found
            // echo json with status = 0
            $response["status"] = 0;
            $response["message"] = "Success";
            $response["DeliveryOPS"] = $ops;
            //$response["accountId"] = $user["account_id"];
            
            echo json_encode($response);
        } else {
            // user not found
            // echo json with error = 1
            $response["status"] = -1;
            $response["message"] = "Invalid UserId!";
            echo json_encode($response);
        }
    
//} else {
  //  echo "Access Denied";
//}
?>

