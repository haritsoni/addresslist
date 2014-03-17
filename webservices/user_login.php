<?php
//
//This file plays role of accepting requests and giving response. This file accepts all GET and POST requests. On each request it will talk to database and will give appropriate response in JSON format.
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
    $response = array("status" => "", "message"=>"", "userId" => "", "accountId" => "");
//	$parameters = file_get_contents('php://input');
//	$parm=json_decode($parameters,TRUE);
	$param = $_POST['json'];
        $value = json_decode($param,true);
	extract($value);

//		file_put_contents("amaan.txt",$userName);

        $user = $db->getUserByUsernameAndPassword($userName, $password, $userType);
        if ($user != false) {
        	$_SESSION['userId'] = $user["admin_id"];
        	$_SESSION['accountId'] = $user["account_id"];
            // user found
            // echo json with status = 0
            $response["status"] = 0;
            $response["message"] = "Success";
            $response["userId"] = $user["admin_id"];
            $response["accountId"] = $user["account_id"];
            
            echo json_encode($response);
        } else {
            // user not found
            // echo json with error = -3
            $response["status"] = -3;
            $response["message"] = "Invalid Login!";
            echo json_encode($response);
        }
    
?>
