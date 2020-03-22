<?php
session_start();

?>
<center><h3>Please wait...<br/>
Forwarding to Mutual Trust Bank LTD....</h3></center>
<form id="testForm" method="post" action="https://mbank.mutualtrustbank.com/MTBEcom/Ecommerce/LoginEcommerce.aspx" style="display:none">

                <input name="merchantUserName" type="hidden" value="Shurjomukhi" />

                <input name="merchantKey" type="hidden" value="sm@20!5d459C#" />                

                <input name="transactionNo" id="id_transactionNo" type="hidden" value="<?=$_SESSION['ORDER_DETAILS']['order_id'].$spayid = 'spay'.rand(100000,9999999);?>" />

                <input name="forwardURL" type="hidden"  value="https://shurjopay.com/mtb_return.php" />

                <input name="productName" type="text" value="SMProduct-<?=$_SESSION['ORDER_DETAILS']['order_id']?>" />

                <input name="transactionAmount" type="text" value="<?=$_SESSION['ORDER_DETAILS']['txnAmount']?>" />

                <input id="Submit1" type="submit"   />

     </form>
<Script>
document.getElementById("testForm").submit();
</script>
