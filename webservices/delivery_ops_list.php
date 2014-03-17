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
 
    // include db handler
    require_once 'include/DB_Functions.php';
    $db = new DB_Functions();
 
    // response Array
    $response = array("status" => "", "message"=>"", "DeliverOPS"=>array());
 
    	$param = $_POST['json'];
        $value = json_decode($param,true);
	extract($value);
 
        // check for user
        $ops = $db->getDeliveryOPS($accountId);


        if ($ops != false) {
            // user found
            // echo json with status = 0
            $response["status"] = 0;
            $response["message"] = "Success";
            $response["DeliverOPS"] = $ops;
            
            echo json_encode($response);
        } else {
            // user not found
            // echo json with error = -1
            $response["status"] = -1;
            $response["message"] = "Invalid UserId!";
            echo json_encode($response);
        }
    

?>
