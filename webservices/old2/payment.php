<?php
//
include_once ($_SERVER['DOCUMENT_ROOT'].'/kss/includes/configure.php');
$url=HTTP_SERVER."kss/index.php";
$customerId=$_POST['customerId'];
$invoiceNo=$_POST['invoiceNo'];
$paymentType=$_POST['paymentType'];
?>
<script src="include/jquery-1.7.1.js">
</script>
<script>
$(document).ready(function(){
alert("<?=$url?>");
$.post("http://192.168.1.37/kss/index.php",{'action':"save",'bill_address_id':"<?=$customerId?>",'purchase_invoice_id':"<?=$invoiceNo?>"},function(data){
	
});
}
});
</script>