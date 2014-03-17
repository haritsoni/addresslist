<?php
/**
 Getting the list of all invoices 
 */
// (isset($_POST['invoiceList']) && $_POST['invoiceList'] != '') {
 
    // include db handler
    require_once 'include/DB_Functions.php';
    $db = new DB_Functions();
 
    // response Array
    $response = array("status" => "", "message"=>"", "invoiceList"=>array());

    
       	$param = $_POST['json'];
        $value = json_decode($param,true);
	extract($value);    

        //$accountId = $_POST['accountId'];
          
        // check for user
        $inv = $db->getInvoiceList();
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
