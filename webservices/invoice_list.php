<?php
/**
 Getting the list of all invoices according to the passed parameters 
 */
//if (isset($_POST['customerId']) && $_POST['customerId'] != '' && isset($_POST['date']) && $_POST['date'] != '' && isset($_POST['invoiceNo']) && $_POST['invoiceNo'] != '') {
 
    // include db handler
    require_once 'include/DB_Functions.php';
    $db = new DB_Functions();
 
    // response Array
    $response = array("status" => "", "message"=>"", "invoiceList"=>array());

    
       	$param = $_POST['json'];
        $value = json_decode($param,true);
	extract($value);    
          
        // check for user
        $inv = $db->getInvoiceListSearch($customerId, $date, $invoiceNo);
        if ($inv != false) {
            // user found
            // echo json with status = 0
            $response["status"] = 0;
            $response["message"] = "Success";
            $response["invoiceList"] = $inv; 
            
            echo json_encode($response);
        } else {
            // user not found
            // echo json with error = -1
            $response["status"] = -1;
            $response["message"] = "No Records Found!";
            echo json_encode($response);
        }
    
//} else {
  //  echo "Access Denied";
//}
?>
