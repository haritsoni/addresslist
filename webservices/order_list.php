<?php
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
    $response = array("status" => "", "message"=>"", "orderList"=>array());


       	$param = $_POST['json'];
        $value = json_decode($param,true);
    	extract($value);    

        // check for user
        $order = $db->getOrderList($date,$customerId,$invoiceNo);
        if ($order != false) {
            // user found
            // echo json with status = 0
            $response["status"] = 0;
            $response["message"] = "Success";
            $response["orderList"] = $order; 
            
            echo json_encode($response);
        } else {
            // records not found
            // echo json with error = -1
            $response["status"] = -1;
            $response["message"] = "No Records Found!";
            echo json_encode($response);
        }
    
?>
