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
    public function deliveryStatusUpdate($accountId, $invoiceNo, $status) {
    	$qry="UPDATE `shipping_log` set `status`='".$status."' where `method`='".$accountId."' and `ref_id`='".$invoiceNo."'";
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
     * Get user by username and password
     */
    public function getUserByUsernameAndPassword($username, $password) {
    	//following query will give response when type contacts.type is delivery boy 
        $result = mysql_query("SELECT * FROM users INNER JOIN contacts ON users.account_id = contacts.id where contacts.type='d' and users.admin_name='".$username."'") or die(mysql_error());
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
    	$qry = mysql_query("SELECT DISTINCT `ref_id`,`short_name`,`primary_name`,`address1`,`address2`,`city_town`,`state_province`,`postal_code`,`country_code`,`telephone1`,`telephone2`,`telephone3`,`email` FROM address_book INNER JOIN contacts where address_book.ref_id=contacts.id and contacts.type='c' and contacts.type='c'") or die(mysql_error());
    	// check for result
    	$no_of_rows = mysql_num_rows($qry);
    	if ($no_of_rows > 0) {
    		//$result = mysql_fetch_array($result);
    		$i=0;
    		while($ResSel = mysql_fetch_assoc($qry)){
    			$result[$i] = array("id"=>$ResSel['ref_id'],'customerName'=>$ResSel['short_name'],"company"=>$ResSel['primary_name'],"address1"=>"address1","address2"=>"address2","city"=>$ResSel['city_town'],"state"=>$ResSel['state_province'],"zipCode"=>$ResSel['postal_code'],"country"=>$ResSel['country_code'],"telephone1"=>$ResSel['telephone1'],"telephone2"=>$ResSel['telephone2'],"telephone3"=>$ResSel['telephone3'],"email"=>$ResSel['email']);
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
    			$result[$i] = array("id"=>$ResSel['id'],'productName'=>$ResSel['sku'],"price"=>$ResSel['item_cost'],"stock"=>"quantity_on_hand");
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
    	
		//SELECT DISTINCT `shipment_id`,`ship_date`,`ref_id`,`ship_address1`,`ship_address2`,`actual_date`,`tracking_id`,`cost`,`status` FROM shipping_log INNER JOIN journal_main ON shipping_log.ref_id = journal_main.purchase_invoice_id where shipping_log.method='".$accountId."'
    	$qry = mysql_query("SELECT DISTINCT * FROM shipping_log INNER JOIN journal_main ON shipping_log.ref_id = journal_main.purchase_invoice_id where shipping_log.method='".$accountId."'") or die(mysql_error());
    	// check for result
    	
    	$no_of_rows = mysql_num_rows($qry);
    	if ($no_of_rows > 0) {
    		//$result = mysql_fetch_array($result);
    		$i=0;
    		while($ResSel = mysql_fetch_assoc($qry)){
    		$result[$i] = array("id"=>$ResSel['shipment_id'],'date'=>$ResSel['ship_date'],"invoiceNo"=>$ResSel['ref_id'],"address"=>"Shipping Address1: ".$ResSel['ship_address1'].",Shipping Address2: ".$ResSel['ship_address2'],"actualDeliveryDate"=>$ResSel['actual_date'],"trackingNo"=>$ResSel['tracking_id'],"cost"=>$ResSel['cost'],"OPSStatus"=>$ResSel['status']);
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



