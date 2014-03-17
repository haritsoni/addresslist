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
if (isset($_POST['accountId']) && $_POST['accountId'] != '' && isset($_POST['invoiceNo']) && $_POST['invoiceNo'] != '' && isset($_POST['status']) && $_POST['status'] != '') {
 
    // include db handler
    require_once 'include/DB_Functions.php';
    $db = new DB_Functions();
 
    // response Array
    $response = array("status" => "", "message"=>"");
 
    
        $accountId = $_POST['accountId'];
        $invoiceNo = $_POST['invoiceNo'];
        $status = $_POST['status'];
 
        // check for user
        $result1 = $db->deliveryStatusUpdate($accountId,$invoiceNo,$status);
        if ($result1 == true) {
            // user found
            // echo json with status = 0
            $response["status"] = 0;
            $response["message"] = "Success";
            
            echo json_encode($response);
        } else {
            // user not found
            // echo json with error = -1
            $response["status"] = -1;
            $response["message"] = "Invalid UserId or InvoiceNo";
            echo json_encode($response);
        }
    
} else {
    echo "Access Denied";
}
?>
