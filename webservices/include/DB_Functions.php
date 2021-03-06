<?php
 
class DB_Functions {
 
    private $db;
 
    //put your code here
    // constructor


    function __construct() {
        require_once 'DB_Connect.php';
        // connecting to database
        $this->db = new DB_Connect();
        $this->db->connect();
    }
 
    // destructor
    function __destruct() {
         
    }
 
    /**
     * Storing new user
     * returns user details
     */
    public function storeUser($name, $email, $password) {
        $uuid = uniqid('', true);
        $hash = $this->hashSSHA($password);
        $encrypted_password = $hash["encrypted"]; // encrypted password
        $salt = $hash["salt"]; // salt
        $result = mysql_query("INSERT INTO users(unique_id, name, email, encrypted_password, salt, created_at) VALUES('$uuid', '$name', '$email', '$encrypted_password', '$salt', NOW())");
        // check for successful store
        if ($result) {
            // get user details
            $uid = mysql_insert_id(); // last inserted id
            $result = mysql_query("SELECT * FROM users WHERE uid = $uid");
            // return user details
            return mysql_fetch_array($result);
        } else {
            return false;
        }
    }
    
    /**
     * updating delivery status
     */
    public function deliveryStatusUpdate($accountId, $invoiceNo, $status, $notes) {
    	$qry="UPDATE `shipping_log` set `status`='".$status."',`notes`='".$notes."' where `method`='".$accountId."' and `ref_id`='".$invoiceNo."'";
    	$result = mysql_query($qry);
    	$aff_rows=mysql_affected_rows();
    	// check for successful update
    	if ($aff_rows>0) {
    		// rows are affected
    		return true;
    	} else {
    		return false;
    	}
    }
    
    /**
     * creating order
     */
    public function order_create($userId,$customerId,$addressId, $date, $company, $address1, $address2, $address3, $address4, $city, $state, $zipCode, $country, $TelephoneNo, $email, $productId, $productName, $quantity, $amount, $unitPrice, $subtotal, $gst, $discount, $discountPer, $invoiceTotal) {


    	$status=mysql_query("SELECT `next_so_num` FROM `current_status`") or die(mysql_error());


	$resstatus=mysql_fetch_assoc($status);

        //journal main gl account id
    	$config=mysql_query("SELECT `configuration_value` FROM `configuration` where `configuration_key`='AR_DEFAULT_GL_ACCT'") or die(mysql_error());

	$resconfig=mysql_fetch_assoc($config);//will return default AR_DEFAULT_GL_ACCT which is 1100
        
        //journal item
        $config2=mysql_query("SELECT `configuration_value` FROM `configuration` where `configuration_key`='AR_DEF_GL_SALES_ACCT'") or die(mysql_error());

	$resconfig2=mysql_fetch_assoc($config2);//will return default AR_DEF_GL_SALES_ACCT which is 4000
        
        $config3=mysql_query("SELECT `configuration_value` FROM `configuration` where `configuration_key`='AR_DISCOUNT_SALES_ACCOUNT'") or die(mysql_error());

	$resconfig3=mysql_fetch_assoc($config3);//will return default discount account which is 4900
        
        
//not getting the gl account value 2314 from configuration, so considering it as static in Sales Draft-Discount Entry

	$contact=mysql_query("SELECT `special_terms` FROM `contacts` where `id`='".$customerId."'") or die(mysql_error());

	$rescontact=mysql_fetch_assoc($contact);
        
        

    	$result = mysql_query("INSERT INTO `journal_main` (`period`,`journal_id`,`post_date`,`store_id`,`description`,`discount`,`terms`,`sales_tax`,`total_amount`,`currencies_code`,`currencies_value`,`so_po_ref_id`,`purchase_invoice_id`,`admin_id`,`gl_acct_id`,`bill_acct_id`,`bill_address_id`,`bill_primary_name`,`bill_address1`,`bill_address2`,`bill_city_town`,`bill_state_province`,
`bill_postal_code`,`bill_country_code`,`bill_telephone1`,`bill_email`,`ship_acct_id`,`ship_address_id`,`ship_primary_name`,`ship_address1`,`ship_address2`,`ship_city_town`,`ship_state_province`,`ship_postal_code`,`ship_country_code`,`ship_telephone1`,`ship_email`,`terminal_date`)values ('".date("n")."','10','".$date."','0','Sales Draft','".$discount."','".$rescontact['special_terms']."','".$gst."','".$amount."','SGD','1','0','".$resstatus['next_so_num']."','".$userId."','".$resconfig['configuration_value']."','".$customerId."','".$addressId."','".$company."','".$address1."','".$address2."','".$city."','".$state."','".$zipCode."','".$country."','".$TelephoneNo."','".$email."','".$customerId."','".$addressId."','".$company."','".$address3."','".$address4."','".$city."','".$state."','".$zipCode."','".$country."','".$TelephoneNo."','".$email."','".$date."')") or die(mysql_error());

    	$ins_id = mysql_insert_id();

		if(isset($productId)){
			foreach($productId as $key=>$product){	
                            
                            
                            $product=mysql_query("SELECT * FROM `inventory` where `id`='".$productId[$key]."'") or die(mysql_error());

                            $resproduct=mysql_fetch_array($product);
                            
				$ins1=mysql_query("INSERT INTO `journal_item`(`ref_id`, `item_cnt`, `gl_type`, `sku`, `qty`, `description`, `debit_amount`, `credit_amount`, `gl_account`, `full_price`, `post_date`) VALUES ('".$ins_id."','1','sos','".$productName[$key]."','".$quantity[$key]."','".$product['description_sales']."','0','".$subtotal."','".$resconfig2['configuration_value']."','".$subtotal."','".$date."')") or die(mysql_error());

				$ins2=mysql_query("INSERT INTO `journal_item`(`ref_id`, `item_cnt`, `gl_type`, `qty`, `description`, `debit_amount`, `credit_amount`, `gl_account`, `full_price`, `post_date`) VALUES ('".$ins_id."','0','tax','1','GST','0','".$gst."','".$resconfig['configuration_value']."','0','".$date."')") or die(mysql_error());

				$ins3=mysql_query("INSERT INTO `journal_item`(`ref_id`, `item_cnt`, `gl_type`, `qty`, `description`, `debit_amount`, `credit_amount`, `gl_account`, `full_price`, `post_date`) VALUES ('".$ins_id."','0','dsc','1','Sales Draft-Discount','".$discount."','0','2314','0','".$date."')") or die(mysql_error());

				$ins4=mysql_query("INSERT INTO `journal_item`(`ref_id`, `item_cnt`, `gl_type`, `qty`, `description`, `debit_amount`, `credit_amount`, `gl_account`, `full_price`, `post_date`) VALUES ('".$ins_id."','0','ttl','1','Sales Draft-Total','". $invoiceTotal."','0','".$resconfig['configuration_value']."','0','".$date."')") or die(mysql_error());

    		$aff_rows=mysql_affected_rows();
			}//student_id
		} //attendance



    	// check for successful update
    	if ($aff_rows>0) {
    		// rows are affected
    		return true;
    	} else {
    		return false;
    	}
    }
    
    
    
    public function payment($userId,$customerId, $invoiceNo, $paymentType, $bankNumber, $chequeNumber, $referenceNumber, $amount, $srNo) {

        $purchase_invoice_id="DP".date("Ymd");
        
        //journal main gl account id
    	$defaultconfig=mysql_query("SELECT `configuration_value` FROM `configuration` where `configuration_key`='AR_DEFAULT_GL_ACCT'") or die(mysql_error());

	$defaultresconfig=mysql_fetch_assoc($defaultconfig);//will return default AR_DEFAULT_GL_ACCT which is 1100
        
        if($paymentType=="CHEQUE")
        {
          $type_of_payment="moneyorder";    
          $desc="Cash Receipts Journal (18)-Total:".$referenceNumber.":".$bankNumber.":".$chequeNumber;
          $config=mysql_query("SELECT `configuration_value` FROM `configuration` where `configuration_key`='MODULE_PAYMENT_MONEYORDER_POS_GL_ACCT'") or die(mysql_error());
        }
        else if($paymentType=="CASH")
        {
          $type_of_payment="cod";
          $desc="Cash Receipts Journal (18)-Total:CASH";
          $config=mysql_query("SELECT `configuration_value` FROM `configuration` where `configuration_key`='MODULE_PAYMENT_COD_POS_GL_ACCT'") or die(mysql_error()); 
        }
        else if($paymentType=="SR")
        {
          $type_of_payment="SR";
          $desc="Cash Receipts Journal (18)-Total:Against SR";
          $config=mysql_query("SELECT `configuration_value` FROM `configuration` where `configuration_key`='MODULE_PAYMENT_SR_POS_GL_ACCT'") or die(mysql_error()); 
        }
        
         //
    	
	$resconfig=mysql_fetch_array($config);

        $contact=mysql_query("SELECT * FROM `address_book` where `ref_id`='".$customerId."' and `type`='cm'") or die(mysql_error());

	$rescontact=mysql_fetch_array($contact);


    	$insqry="INSERT INTO `journal_main` (`period`,`journal_id`,`post_date`,`shipper_code`,`description`,`total_amount`,`currencies_code`,`currencies_value`,`purchase_invoice_id`,`admin_id`,`gl_acct_id`,`bill_acct_id`,`bill_primary_name`,`bill_address1`,`bill_address2`,`bill_city_town`,`bill_state_province`,`bill_postal_code`,`bill_country_code`,`bill_telephone1`,`bill_email`) values ('".date("n")."','18','".date("Y-m-d h:i:s")."','".$type_of_payment."','Customer Payments Entry','".$amount."','SGD','1','".$purchase_invoice_id."','".$userId."','".$resconfig['configuration_value']."','".$customerId."','".$rescontact['primary_name']."','".$rescontact['address1']."','".$rescontact['address2']."','".$rescontact['city_town']."','".$rescontact['state_province']."','".$rescontact['postal_code']."','".$rescontact['country_code']."','".$rescontact['telephone1']."','".$rescontact['email']."')";
    	$result = mysql_query($insqry);
    	$ins_id = mysql_insert_id();
                    
			$ins1=mysql_query("INSERT INTO `journal_item`(`ref_id`, `so_po_item_ref_id`,`gl_type`, `debit_amount`, `credit_amount`, `gl_account`, `serialize_number`, `post_date`) VALUES ('".$ins_id."','REF',pmt','0','".$amount."','".$defaultresconfig['configuration_value']."','".$invoiceNo."','".date("Y-m-d h:i:s")."')") or die(mysql_error());

                        $ins2=mysql_query("INSERT INTO `journal_item`(`ref_id`, `gl_type`,`description`, `debit_amount`, `credit_amount`, `gl_account`) VALUES ('".$ins_id."','ttl','".$desc."','".$amount."','0',".$resconfig['configuration_value']."'") or die(mysql_error());

			$aff_rows=mysql_affected_rows();



    	// check for successful update
    	if ($aff_rows>0) {
    		// rows are affected
    		return true;
    	} else {
    		return false;
    	}
    }
    
 
        /**
     * Get user by username, password and usertype
     */
    public function getUserByUsernameAndPassword($username, $password, $userType) {
    	
    	if($userType=="1")//salesman
    	{
    		
    		$result = mysql_query("SELECT * FROM users where admin_name='".$username."' and inactive='0' and account_id='0'") or die(mysql_error());
    	}
    	else if($userType=="2")//delivery boy
    	{
    		//contacts.type d is used for delivery boy
    		$result = mysql_query("SELECT * FROM users INNER JOIN contacts ON users.account_id = contacts.id where contacts.type='d' and users.admin_name='".$username."' and users.inactive='0'") or die(mysql_error());	
    	}
    	
    	// check for result
        $no_of_rows = mysql_num_rows($result);
        if ($no_of_rows > 0) {
            $result = mysql_fetch_array($result);
            //$salt = $result['salt'];
            $encrypted_password = $result['admin_pass'];
            $password_match=$this->pw_validate_password($password,$encrypted_password);

            // check for password equality
            if ($password_match == true) {
            	
                // user authentication details are correct
                return $result;
            }
        } else {
            // user not found
            return false;
        }
    }
        
    
    /**
     * Get customer list
     */
    public function getCustomerList() {
    	//following query will give response when type contacts.type is delivery boy
    	$qry = mysql_query("SELECT DISTINCT * FROM address_book INNER JOIN contacts where address_book.ref_id=contacts.id and contacts.type='c' and address_book.type='cm'") or die(mysql_error());
    	// check for result
    	$no_of_rows = mysql_num_rows($qry);
    	if ($no_of_rows > 0) {
    		//$result = mysql_fetch_array($result);
    		$i=0;
    		while($ResSel = mysql_fetch_assoc($qry)){
    			$disc=explode(":",$ResSel['special_terms']);
    			$result[$i] = array("id"=>$ResSel['id'],"address_id"=>$ResSel['address_id'],'customerName'=>$ResSel['short_name'],"company"=>$ResSel['primary_name'],"address1"=>$ResSel['address1'],"address2"=>$ResSel['address2'],"city"=>$ResSel['city_town'],"state"=>$ResSel['state_province'],"zipCode"=>$ResSel['postal_code'],"country"=>$ResSel['country_code'],"telephone1"=>$ResSel['telephone1'],"telephone2"=>$ResSel['telephone2'],"telephone3"=>$ResSel['telephone3'],"email"=>$ResSel['email'],"discount"=>$disc[1]);
    			$i++;
    		}
    		return $result;
    		
    	} else {
    		// no records found
    		return false;
    	}
    }
    
    /**
     * Get order list
     */
    public function getOrderList($date,$customerId,$invoiceNo) {
    	//journal id 10 is used for order and 12 is used for invoice
		
		 $whr ="`journal_id`='10'";

   			if($customerId!="")
			 {
				$whr .= " AND `bill_acct_id` = '".$customerId."'";
			 }
			 if($date!="")
			 {
				$whr .= " AND `post_date` = '".$date."'";
			 }
			 if($invoiceNo!="")
			 {
				$whr .= " AND `purchase_invoice_id` = '".$invoiceNo."'";
			 }
			 
    	$qry = mysql_query("SELECT * FROM  `journal_main` where ".$whr) or die(mysql_error());
    	// check for result
    	$no_of_rows = mysql_num_rows($qry);
    	if ($no_of_rows > 0) {
    		//$result = mysql_fetch_array($result);
    		$i=0;
    		while($ResSel = mysql_fetch_assoc($qry)){
    			$result[$i] = array("id"=>$ResSel['id'],'customerName'=>$ResSel['bill_primary_name'],"date"=>$ResSel['post_date'],"soNumber"=>$ResSel['purchase_invoice_id'],"amount"=>$ResSel['total_amount']);
    			$i++;
    		}
    		return $result;
    
    	} else {
    		// no records found
    		return false;
    	}
    }

    
         /**
     * Get order details
     */
    public function getOrderDetails($customerId,$invoiceId) {
    	//journal id 10 is used for order and 12 is used for invoice

			 $whr ="`journal_id`='12'";

			if($customerId!="")
			 {
				$whr .= " AND `bill_acct_id` = '$customerId'";
			 }
			 if($invoiceId!="")
			 {
				$whr .= " AND `purchase_invoice_id` = '$invoiceId'";
			 }
    	
    	$qry = mysql_query("SELECT `id`,`discount`,`sales_tax`,`usd_totalvalue` FROM  journal_main where ".$whr) or die(mysql_error());
    	
    	$res = mysql_fetch_array($qry);

    	$subtotal=0;
    	$ref_id=array(); 
    	$disc=array(); //discount corresponding to the reference id
    	$gst=array();//gst from journal_main
    	$usdTotalValue=array();
    	
    	for($i=0;$i<count($res)-1;$i++) //finding id and disc whose customer id and invoice id  match the posted request
    	{
 			array_push($ref_id, $res[0]);
 			array_push($disc, $res[1]);
 			array_push($gst, $res[2]);
 			array_push($usdTotalValue,$res[3]);
    	}
    	

    	// check for result
    	$no_of_rows = mysql_num_rows($qry);
    	if ($no_of_rows > 0) {
    		for($j=0;$j<=count($ref_id)-1;$j++)
    		{
    		$qry1 = mysql_query("SELECT * FROM  journal_item where `ref_id`='".$ref_id[$j]."' and `gl_type`='sos'") or die(mysql_error());
    
    		$k=0;
    		$subtotal=0;
    		while($ResSel = mysql_fetch_assoc($qry1)){
    			$result[$k] = array("productName"=>$ResSel['sku'],'quantity'=>$ResSel['qty'],"unitPrice"=>$ResSel['full_price'],"amount"=>$ResSel['qty']*$ResSel['full_price']);
    			$subtotal+=$result[$k]['amount'];
    			$k++;
    		}
    	}
    	
    	
    	
    	
    	$subT=array("subTotal"=>$subtotal);//for getting the subtotal
    	$disC=array("discount"=>$disc[0]);
    	$gsT=array("gst"=>$gst[0]); 
    	$invT=array("invoiceTotal"=>$usdTotalValue[0]); //invoice total


    		return array_merge($result, $subT, $disC, $gsT, $invT);

    } else {
    		// no records found
    		return false;
    	}
    }
    
    //
    /**
     * Get invoice list
     */
    public function getInvoiceList() {
    	//journal id 10 is used for order and 12 is used for invoice
    	$qry = mysql_query("SELECT * FROM  journal_main where `journal_id`='12'") or die(mysql_error());
    	// check for result
    	$no_of_rows = mysql_num_rows($qry);
    	if ($no_of_rows > 0) {
    		//$result = mysql_fetch_array($result);
    		$i=0;
    		while($ResSel = mysql_fetch_assoc($qry)){

    			$result[$i] = array("invoiceNo"=>$ResSel['purchase_invoice_id']);
    			$i++;
    		}
    		return $result;
    
    	} else {
    		// no records found
    		return false;
    	}
    }

    /*
     * invoice list search
     */
    public function getInvoiceListSearch($customerId,$date,$invoiceNo) {
    	//journal id 10 is used for order and 12 is used for invoice 
			
			 $whr ="`journal_id`='12'";

			if($customerId!="")
			 {
				$whr .= "  AND `bill_acct_id` = '$customerId'";
			 }
			 if($date!="")
			 {
				$whr .= "  AND `post_date` = '$date'";
			 }
			 if($invoiceNo!="")
			 {
				$whr .= "  AND `purchase_invoice_id` = '$invoiceNo'";
			 }
    	$qry = mysql_query("SELECT * FROM  journal_main where ".$whr) or die(mysql_error());
	
    	// check for result
    	$no_of_rows = mysql_num_rows($qry);
    	if ($no_of_rows > 0) {
    		//$result = mysql_fetch_array($result);
    		$i=0;
    		while($ResSel = mysql_fetch_assoc($qry)){
    			if($ResSel['closed']==1)
    			{
    				$paid="Yes";
    			}
    			else
    			{
    				$paid="No";
    			}
    			$result[$i] = array("id"=>$ResSel['id'],'customerName'=>$ResSel['bill_primary_name'],"date"=>$ResSel['post_date'],"pnvoiceNo"=>$ResSel['purchase_invoice_id'],"invoiceAmount"=>$ResSel['total_amount'],"balanceAmount"=>$ResSel['total_amount'],"paid"=>$paid);
    			$i++;
    		}
    		return $result;
    
    	} else {
    		// no records found
    		return false;
    	}
    }
    
    /**
     * Get product list
     */
    public function getProductList() {
    	//following query will give response when type contacts.type is delivery boy
    	$qry = mysql_query("SELECT DISTINCT * FROM inventory") or die(mysql_error());
    	// check for result
    	$no_of_rows = mysql_num_rows($qry);
    	if ($no_of_rows > 0) {
    		//$result = mysql_fetch_array($result); 
    		$i=0;
    		while($ResSel = mysql_fetch_assoc($qry)){
    			$qry1 = mysql_query("SELECT `rate_accounts` FROM tax_rates where `tax_rate_id`='".$ResSel["item_taxable"]."'") or die(mysql_error());
    			$res1 = mysql_fetch_array($qry1);
    			$result[$i] = array("id"=>$ResSel['id'],'productName'=>$ResSel['sku'],"price"=>$ResSel['full_price'],"stock"=>$ResSel["quantity_on_hand"],"GST"=>$res1['rate_accounts']);
    			$i++;
    		}
    		return $result;
    
    	} else {
    		// no records found
    		return false;
    	}
    }
    
    /**
     * Get SR list
     */
    public function getSrList() {
    	//following query will give response when type contacts.type is delivery boy
    	$qry = mysql_query("SELECT DISTINCT * FROM journal_main where `journal_id`='13'") or die(mysql_error());
    	// check for result
    	$no_of_rows = mysql_num_rows($qry);
    	if ($no_of_rows > 0) {
    		//$result = mysql_fetch_array($result);
    		$i=0;
    		while($ResSel = mysql_fetch_assoc($qry)){
    			$result[$i] = array("id"=>$ResSel['id'],'srName'=>$ResSel['bill_primary_name'],"srNumber"=>$ResSel['purchase_invoice_id'],"amount"=>$ResSel['total_amount']);
    			$i++;
    		}
    		return $result;
     
    	} else {
    		// no records found
    		return false;
    	}
    }
    
    /**
     * Get Delivery Operator List
     */
    
	public function getDeliveryOPS($accountId) {
    	//following query will give response when account id is valid
    	
		
    	$qry = mysql_query("SELECT shipping_log.shipment_id as shipment_id, journal_main.bill_primary_name as cus_name, shipping_log.ship_date as ship_date, shipping_log.ref_id as ref_id, journal_main.ship_address1 as ship_address1, journal_main.ship_address2 as ship_address2,shipping_log.actual_date as actual_date, shipping_log.tracking_id as tracking_id, shipping_log.cost as cost, shipping_log.status as status1  FROM shipping_log INNER JOIN journal_main ON shipping_log.ref_id = journal_main.purchase_invoice_id where shipping_log.method='".$accountId."'") or die(mysql_error());
    	// check for result
    	
    	$no_of_rows = mysql_num_rows($qry);
    	if ($no_of_rows > 0) {
    		//$result = mysql_fetch_array($result);
    		$i=0;
    		while($ResSel = mysql_fetch_assoc($qry)){
    		$result[$i] = array("id"=>$ResSel['shipment_id'],'date'=>$ResSel['ship_date'],"invoiceNo"=>$ResSel['ref_id'],"Address"=>$ResSel['ship_address1'],"customerName"=>$ResSel['cus_name'],"actualDeliveryDate"=>$ResSel['actual_date'],"trackingNumber"=>$ResSel['tracking_id'],"cost"=>$ResSel['cost'],"OPSstatus"=>$ResSel['status1']);
    			$i++;
    												}
            return $result;
    		
    						 } else {
    		// account id not valid
    		return false;
    	}
    } 
    
 
    /**
     * Check whether user exist or not
     */
    public function isUserExisted($email) {
        $result = mysql_query("SELECT email from users WHERE email = '$email'");
        $no_of_rows = mysql_num_rows($result);
        if ($no_of_rows > 0) {
            // user existed
            return true;
        } else {
            // user not existed
            return false;
        }
    }
 
    /**
     * Decrypting password
     * @param plain, encrypted
     * returns true or false
     */
    
    public function pw_validate_password($plain, $encrypted) {
    	if ($plain!="" && $encrypted!="") 
    	{
    		// split apart the hash / salt
    		$stack = explode(':', $encrypted);
    		if (sizeof($stack) != 2) return false;
    		if (md5($stack[1] . $plain) == $stack[0]) {
    			return true;
    	}
    	}
    	return false;
    }
    
}
 
?>




