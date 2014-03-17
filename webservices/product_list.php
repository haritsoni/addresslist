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
//if (isset($_POST['productList']) && $_POST['productList'] != '') {
 
    // include db handler
    require_once 'include/DB_Functions.php';
    $db = new DB_Functions();
 
    // response Array
    $response = array("status" => "", "message"=>"", "productList"=>array());

       	$param = $_POST['json'];
        $value = json_decode($param,true);
	extract($value);
 
        //$accountId = $_POST['accountId'];
          
        // check for user
        $prod = $db->getProductList();
        if ($prod != false) {
            // user found
            // echo json with status = 0
            $response["status"] = 0;
            $response["message"] = "Success";
            $response["productList"] = $prod; 
            
            echo json_encode($response);
        } else {
            // user not found
            // echo json with error = -1
            $response["status"] = -1;
            $response["message"] = "No Details!";
            echo json_encode($response);
        }
    
//} else {
  //  echo "Access Denied";
//}
?>
