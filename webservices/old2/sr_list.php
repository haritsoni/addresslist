<?php
/**
 Getting the list of all invoices 
 */
if (isset($_POST['srList']) && $_POST['srList'] != '') {
 
    // include db handler
    require_once 'include/DB_Functions.php';
    $db = new DB_Functions();
 
    // response Array
    $response = array("status" => "", "message"=>"", "srList"=>array());

          
        // check for list
        $sr = $db->getSrList();
        if ($sr != false) {
            // records found
            // echo json with status = 0
            $response["status"] = 0;
            $response["message"] = "Success";
            $response["srList"] = $sr; 
            
            echo json_encode($response);
        } else {
            // user not found
            // echo json with error = -1
            $response["status"] = -1;
            $response["message"] = "No Records Found!";
            echo json_encode($response);
        }
    
} else {
   echo "Access Denied";
}
?>
