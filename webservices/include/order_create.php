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
//if (isset($_POST['customerList']) && $_POST['customerList'] != '') {
 
    // include db handler
    require_once 'include/DB_Functions.php';
    $db = new DB_Functions();
 
    // response Array
    $response = array("status" => "", "message"=>"");
 
    
       	$param ="{
				'customerId':'5',
				'date':'2013-02-22',
				'company':'raj',
				'address1':'fahbdhj',
				'address2':'fdsfsf',
				'city':'gnager',
				'state':'gujarat',
				'zipCode':'123456789',
				'country':'India',
				'telephoneNo':'1234565',
				'email':'hhsdh@sh.com',
				'subTotal':'5200',
				'gst':'5',
				'discount':'5',
				'discountPer':'5',
				'invoiceTotal':'50000',
				'product_details':[
				{
					'productName':'mobile',
					'quantity':'5',
					'amount':'5000',
					'unitPrice':'50'
				},
				{
					'productName':'mobile',
					'quantity':'5',
					'amount':'5000',
					'unitPrice':'50'
				}
				]
				}";
		//$_POST['json'];
        $value = json_decode($param,true);
		print_r($value);
		exit;
		extract($value);
 
        // check for user
        $cus = $db->order_create($userId,$customerId,$addressId, $date, $company, $address1, $address2, $address3, $address4, $city, $state, $zipCode, $country, $TelephoneNo, $email, $productId, $productName, $quantity, $amount, $unitPrice, $subtotal, $gst, $discount, $discountPer, $invoiceTotal);
        if ($cus != false) {
            // user found
            // echo json with status = 0
            $response["status"] = 0;
            $response["message"] = "Success";
            $response["customerList"] = $cus;
            
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
