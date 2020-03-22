<script>

function getsms(amount,sender,trxid){
	timecount = timecount + 10;
	if(timecount >= timeinterval){
		location.href='<?php echo $db->local_return_url; ?>';
	}	
    var amount = amount;
    var sender = sender;
    var trxid = trxid;
   	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		 alert ("Browser does not support HTTP Request");
		 return;
	} 
    var url="bkash_findsms.php";

    url=url+"?amount="+amount;
    url=url+"&sender="+sender;
    url=url+"&trxid="+trxid;
    url=url+"&sid="+Math.random();
	
	xmlHttp.onreadystatechange= getsmsstate
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);	
}
   
function getsmsstate() { 
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
		if(xmlHttp.responseText==1){
			location.href='<?php echo $db->local_return_url; ?>';
		}
		
   	} //end 4
} // 


  function GetXmlHttpObject()
{
var xmlHttp=null;
try
 {
 // Firefox, Opera 8.0+, Safari
 xmlHttp=new XMLHttpRequest();
 }
catch (e)
 {
 //Internet Explorer
 try
  {
  xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
  }
 catch (e)
  {
  xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
 }
 
 
return xmlHttp;
}  
</script>

