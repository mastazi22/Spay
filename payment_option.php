<?php
include ("includes/session_handler.php");
include("includes/configure.php");

if(count($_POST)==0)
{
	echo "<div style='width:100%;text-align:center;'><h1>This page requires parameters to access.</h1></div>";
	die();
}

	$_SESSION['ORDER_DETAILS']=$_POST;
	$gpspl = substr(($_SESSION['ORDER_DETAILS']['uniqID']),0,10);
	$display_none = '';
	//include("includes/header.php");
	include("payment_engine/paymentEngine.php");

      	$hasLogo = false;
	if(isset($_SESSION['ORDER_DETAILS']['userID']))
	{
		$sql = mysqli_query($GLOBALS["___mysqli_sm"],"select merchant_logo, merchant_domain from sp_merchants where id='{$_SESSION['ORDER_DETAILS']['userID']}'");
				
		if(mysqli_num_rows($sql)>0) 
		{
			$logo=mysqli_fetch_object($sql);
			$hasLogo = true;
		}
	}


	// get user credentials for merchant ids
	$cityBankOk = FALSE;
	$eblBankOk  = FALSE; 
	$dbblBankOk = FALSE;
	$nagadOk = FALSE;
	$cityBank = isset($_SESSION['ORDER_DETAILS']['mxID'])?$_SESSION['ORDER_DETAILS']['mxID']:'';
	$eblBank = isset($_SESSION['ORDER_DETAILS']['eblID'])?$_SESSION['ORDER_DETAILS']['eblID']:'';
	$dbblBank = isset($_SESSION['ORDER_DETAILS']['dbblID'])?$_SESSION['ORDER_DETAILS']['dbblID']:'';
	$bKashWallet = isset($_SESSION['ORDER_DETAILS']['bkashJson'])?$_SESSION['ORDER_DETAILS']['bkashJson']:'';
	
	$teletalkPayment = isset($_SESSION['ORDER_DETAILS']['otherOption'])?$_SESSION['ORDER_DETAILS']['otherOption']:'';
	/*
	if( isset($_SESSION['ORDER_DETAILS']['uniqID']) 
			&& substr($_SESSION['ORDER_DETAILS']['uniqID'],0,3) == 'PPT' ) 
	{
		$nagadOk = TRUE;	
	} else {
		$nagadOk = FALSE;	
	}
	*/

	if($cityBank !="" and $cityBank !="NULL" ) { $cityBankOk = TRUE;}
	if($eblBank !="" and $eblBank !="NULL" )   { $eblBankOk  = TRUE; }
	if($dbblBank !="" and $dbblBank !="NULL" ) { $dbblBankOk = TRUE; }
	if($bKashWallet !="" and $bKashWallet !="NULL" ) { $bKashWallet = TRUE; }   
	if($teletalkPayment !="" and $teletalkPayment !="NULL" ) { $teletalkPayment = TRUE; }    


	
?>
<!DOCTYPE html>
<html>
<head>
<title>shurjoPay</title>
<!-- custom-theme -->
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false);
		function hideURLbar(){ window.scrollTo(0,1); } </script>
<!-- //fevicon -->        
 <link rel="icon" href="option-images/sP.ico" type="image/png" sizes="16x16">         
<!-- //custom-theme -->
<link href="css/optionpage-style.css" rel="stylesheet" type="text/css" media="all" />
<!-- js -->
<script src="js/jquery.min.js"></script>
<!-- //js --> 
<link rel="stylesheet" href="css/easy-responsive-tabs.css">
<link href="//fonts.googleapis.com/css?family=Lato:100,100i,300,300i,400,400i,700,700i,900,900i&amp; subset=latin-ext" rel="stylesheet">
</head>
<body>
	<div class="main">
		<div class="w3_main_grids">
			<div class="w3layouts_profile_grid1">
				<div class="w3l_profile_grid1_padd">
					<div class="w3ls_menu_grids">
						<div class="w3ls_menu_grid">
							<img src="option-images/shurjopay.png" alt=" " class="img-responsive" />
						</div>
						<div class="w3ls_menu_grid">
							<h2>Select Your Payment Method</h2>
						</div>
						<div class="w3ls_menu_grid">
							<!-- <img src="option-images/paypoint.png" alt=" " class="img-responsive" /> -->
							<?php  if ($hasLogo):   ?>
					                <img class="img-responsive"  src="./img/merchant_logo/<?php echo $logo->merchant_logo; ?>"/>
					        <?php  else:  ?>
					                <img class="img-responsive"  src="images/payPoint_logo.png"/>
					        <?php  endif;  ?>
						</div>
						<div class="clear"> </div>
					</div>
				</div>
			</div>
<form action="./payment_process.php" method="POST" id="frmMethod">
    <input type="hidden" name="paymentOption" id="paymentOption"/>			
			<div class="agileinfo_profile_grid2">
				<div class="w3_agile_profile_grid2_left">
					<img src="option-images/1.png" alt=" " class="img-responsive" />
				</div>
				<div class="agile_profile_grid2_left">
					<h4>Transaction ID: <?=$_SESSION['ORDER_DETAILS']['uniqID']?></h4>
					<ul>
						<li class="agileits_w3layouts_list"><a href="#">Total amount <?php echo $_SESSION['ORDER_DETAILS']['txnAmount']; ?>/=  BDT</a></li>
						<li></li>																	
						<li class="agileits_w3layouts_list_cancel"><a href="payment_cancel.php?cancel=ok" >Cancel Payment</a></li>
					</ul>
				</div>
				<div class="clear"> </div>
			</div>
			<div class="wthree_tabs">
				<div id="horizontalTab">
					<ul class="resp-tabs-list">
                       		<li>
                       			<img src="option-images/any_bank_icon.png" class="bank-icon"  alt=" "/> All Gateway
                       		</li>
                        
							<li
							<?php if(!$cityBankOk):?> 	
                       		 	class="disabled"
                       		 	onClick="this.disabled=true;"
                       		<?php endif; ?> 	 
							><img src="option-images/city_bank_icon.png"  class="bank-icon" alt=" "/> City</li>
							<li 
							<?php if(!$eblBankOk):?>
								class="disabled"
								onClick="this.disabled=true;"
                       		<?php endif; ?> 	
							style="font-size:12px;">
								<img src="option-images/ebl_bank_icon.png"  class="bank-icon" alt=" "/> EBL SKYPAY</li>
						
							<li
							<?php if(!$dbblBankOk):?>
							class="disabled"
							onClick="this.disabled=true;"
                       		<?php endif; ?> 
							><img src="option-images/dbbl_bank_icon.png"  class="bank-icon" alt=" "/> DBBL</li>
							<li><img src="option-images/trust_bank_icon.png" class="bank-icon" alt=" "/> Trust Bank</li>
                        	<li  style="font-size:12px;"><img src="option-images/mobile_wallet_icon.png" class="bank-icon"  alt=" "/> Mobile Wallet</li>
                        	<li  style="font-size:12px;"><img src="option-images/online_banking_icon.png" class="bank-icon"  alt=" "/> Online Banking</li>
					</ul>
					<div class="resp-tabs-container">
						<div class="agileinfo_tab1">
							<ul>
								<li>
									<div class="agileinfo_tab1_img_grids">
									<!-- Default block //start-->	
									<?php if($bKashWallet):?>
                                            <div class="agileinfo_tab1_img_grid">
                                                <a href="javascript:void(0)" onclick="selectMethod('bkash_api')">                                                    
                                                    <img src="option-images/bkash-api.png" alt="bKash API" class="img-responsive" />
                                                </a>
                                            </div>      
                                        <?php else:?>  
                                        	<?php if ( isset($_SESSION['ORDER_DETAILS']['uniqID']) && substr($_SESSION['ORDER_DETAILS']['uniqID'],0,3) != 'VDP' ):?>
		                                        <div class="agileinfo_tab1_img_grid">
		                                            <a href="javascript:void(0)" onclick="selectMethod('bkash')">
		                                                <img src="option-images/bkash.png" alt=" " class="img-responsive" />
		                                            </a>
		                                        </div>  
		                                    <?php endif;?>    
                                        <?php endif;?>										
										<?php if($cityBankOk):?>
										<div class="agileinfo_tab1_img_grid">
											<a href="javascript:void(0)" onclick="selectMethod('mx')">
												<img src="option-images/american-express.png" alt=" " class="img-responsive" />
											</a>
										</div>	
										<div class="agileinfo_tab1_img_grid">											
											<a href="javascript:void(0)" onclick="selectMethod('mx_visa')">
												<img src="option-images/visa.png" alt=" " class="img-responsive" />
											</a>
										</div>
										<div class="agileinfo_tab1_img_grid">
											<a href="javascript:void(0)" onclick="selectMethod('mx_master_card')">
												<img src="option-images/master.png" alt=" " class="img-responsive" />
											</a>
										</div>	

										<div class="agileinfo_tab1_img_grid">
                                            <a href="javascript:void(0)" onclick="selectMethod('unionpay')">
                                                <img src="option-images/unionpay.png" alt=" " class="img-responsive" />
                                            </a>
                                        </div> 									
										<?php endif; ?>	
										
										<?php if($dbblBankOk):?>
											<div class="agileinfo_tab1_img_grid">
												<a href="javascript:void(0)" onclick="selectMethod('dbbl_nexus')">
													<img src="option-images/dbbl-nexus.png" alt=" " class="img-responsive" />
												</a>
											</div>		
											<div class="agileinfo_tab1_img_grid">
												<a href="javascript:void(0)" onclick="selectMethod('dbbl_mobile')">
													<img src="option-images/dbbl-mobile-banking-rocket.png" alt=" " class="img-responsive" />
												</a>
											</div>									
										<?php endif; ?>		
										<div class="agileinfo_tab1_img_grid">
											<a href="javascript:void(0)" name="TBL" id="TBL" onclick="selectMethod('tbl')">
												<img src="option-images/trust-bank.png" alt=" " class="img-responsive" />
											</a>
										</div>
										<div class="agileinfo_tab1_img_grid">
											<a href="javascript:void(0)" name="TBMM" id="TBMM" onclick="selectMethod('tbl')">
												<img src="option-images/t-cash.png" alt=" " class="img-responsive" />
											</a>
										</div>			
										<div class="agileinfo_tab1_img_grid">
											<a href="javascript:void(0)" onclick="selectMethod('upay')">
												<img src="option-images/upay-logo.png" alt=" " class="img-responsive" />
											</a>
										</div>		
										<div class="agileinfo_tab1_img_grid">
											<a href="javascript:void(0)" onclick="selectMethod('mCash_iBank')">
												<img src="option-images/islami-bank-m-cash.png" alt=" " class="img-responsive" />
											</a>
										</div>				
										<div class="agileinfo_tab1_img_grid">
											<a href="javascript:void(0)" onclick="selectMethod('ibbl')">
												<img src="option-images/islami-bank.png" alt=" " class="img-responsive" />
											</a>
										</div>
										
										<?php  if($teletalkPayment):?>
											<?php if ( isset($_SESSION['ORDER_DETAILS']['uniqID']) && substr($_SESSION['ORDER_DETAILS']['uniqID'],0,3) == 'JBD' ):?>
                                            <div class="agileinfo_tab1_img_grid">
                                                <a href="javascript:void(0)" onclick="selectMethod('teletalk')">
                                                    <img src="option-images/teletalk-payment.png" alt=" " class="img-responsive" />
                                                </a>
                                            </div>
                                            <?php endif;?>
                                        <?php endif;?>  
                                        
                                        <?php if($nagadOk):?>                                   
                                            <div class="agileinfo_tab1_img_grid">
                                                <a href="javascript:void(0)" onclick="selectMethod('nagad')">
                                                   <img src="option-images/nagad.png" alt=" " class="img-responsive" />
                                                </a>
                                            </div>
                                        <?php endif;?>  

										<div class="clear"> </div>
									</div>
									<!-- Default block //end-->	
								</li>
							</ul>
						</div>
						<div class="agileinfo_tab2">
							<div class="agileinfo_tab1">
							  <ul>
								<li onClick="this.disabled=true;">
									<div class="agileinfo_tab1_img_grids">
									<!-- City Bank //start-->		
										<?php if($cityBankOk):?>
										 <div class="agileinfo_tab1_img_grid">
											<a href="javascript:void(0)" onclick="selectMethod('mx')">
												<img src="option-images/american-express.png" alt=" " class="img-responsive" />
											</a>
										</div>
										<div class="agileinfo_tab1_img_grid">
											<a href="javascript:void(0)" onclick="selectMethod('mx_visa')"><img src="option-images/visa.png" alt=" " class="img-responsive" /></a>
										</div>
										<div class="agileinfo_tab1_img_grid">
											<a href="javascript:void(0)" onclick="selectMethod('mx_master_card')"><img src="option-images/master.png" alt=" " class="img-responsive" /></a>
										</div>										
										<?php endif; ?>
									<!-- City Bank //end-->		
										<div class="clear"> </div>										
									</div>

								</li>
							  </ul>
							</div>
						</div>
						<div class="agileinfo_tab3">
							<div class="agileinfo_tab1">
							  <ul>
								<li>
									<div class="agileinfo_tab1_img_grids">
									<!-- EBL //start-->		
										<?php if($eblBankOk):?>
										<div class="agileinfo_tab1_img_grid">
											<a href="javascript:void(0)" name="ebl_visa"  onclick="selectMethod('ebl_visa')">
												<img src="option-images/visa.png" alt=" " class="img-responsive" />
											</a>
										</div>
										<div class="agileinfo_tab1_img_grid">
											<a  href="javascript:void(0)" onclick="selectMethod('ebl_master')">
												<img  src="option-images/master.png" alt=" " class="img-responsive" />
											</a>
										</div>	
										<?php endif; ?>
									<!-- EBL //end-->											
										<div class="clear"> </div>
									</div>
								</li>
							  </ul>
							</div>
						</div>
						<div class="agileinfo_tab3">
							<div class="agileinfo_tab1">
							  <ul>
								<li>
									<div class="agileinfo_tab1_img_grids">
									<!-- DBBL //start-->	
									<?php if($dbblBankOk):?>										
										<!--
										<div class="agileinfo_tab1_img_grid">
											<a href="javascript:void(0)" onclick="selectMethod('dbbl_master')">
												<img src="option-images/master.png" alt=" " class="img-responsive" />
											</a>
										</div>
										<div class="agileinfo_tab1_img_grid">
											<a href="javascript:void(0)" onclick="selectMethod('dbbl_visa')">
												<img src="option-images/visa.png" alt=" " class="img-responsive" />
											</a>
										</div> 
										-->  
										
										<div class="agileinfo_tab1_img_grid">
											<a href="javascript:void(0)" onclick="selectMethod('dbbl_nexus')">
												<img src="option-images/dbbl-nexus.png" alt=" " class="img-responsive" />
											</a>
										</div>
										
									<?php endif; ?>	                                     
									<!-- DBBL //end-->		
										<div class="clear"> </div>
									</div>
								</li>
							  </ul>
							</div>
						</div>
                        <div class="agileinfo_tab3">
							<div class="agileinfo_tab1">
							  <ul>
								<li>
									<div class="agileinfo_tab1_img_grids">
									<!-- TBL //start-->		
										<div class="agileinfo_tab1_img_grid">
											<a href="javascript:void(0)" name="TBL" id="TBL" onclick="selectMethod('tbl')">
												<img src="option-images/trust-bank.png" alt=" " class="img-responsive" />
											</a>
										</div>
										<div class="agileinfo_tab1_img_grid">
											<a href="javascript:void(0)" name="TBMM" id="TBMM" onclick="selectMethod('tbl')">
												<img src="option-images/t-cash.png" alt=" " class="img-responsive" />
											</a>
										</div>										
									<!-- TBL //end-->		
										<div class="clear"> </div>
									</div>
								</li>
							  </ul>
							</div>
						</div>
                        <div class="agileinfo_tab3">
							<div class="agileinfo_tab1">
							  <ul>
								<li>
									<div class="agileinfo_tab1_img_grids">
									<!-- Mobile Wallet //start-->	
										<div class="agileinfo_tab1_img_grid">
											<a href="javascript:void(0)" onclick="selectMethod('upay')">
												<img src="option-images/upay-logo.png" alt=" " class="img-responsive" />
											</a>
										</div>	
										<?php if($bKashWallet):?>
                                            <div class="agileinfo_tab1_img_grid">
                                                <a href="javascript:void(0)" onclick="selectMethod('bkash_api')">                                                    
                                                    <img src="option-images/bkash-api.png" alt="bKash API" class="img-responsive" />
                                                </a>
                                            </div>      
                                        <?php else:?>  
                                        		<?php if( isset($_SESSION['ORDER_DETAILS']['uniqID']) &&  substr($_SESSION['ORDER_DETAILS']['uniqID'],0,3) != 'VDP'): ?>
			                                        <div class="agileinfo_tab1_img_grid">
			                                            <a href="javascript:void(0)" onclick="selectMethod('bkash')">
			                                                <img src="option-images/bkash.png" alt=" " class="img-responsive" />
			                                            </a>
			                                        </div>  
			                                    <?php endif; ?>	
                                        <?php endif;?>	

										<?php if($dbblBankOk):?>										
											<div class="agileinfo_tab1_img_grid">
												<a href="javascript:void(0)" onclick="selectMethod('dbbl_mobile')">
													<img src="option-images/dbbl-mobile-banking-rocket.png" alt=" " class="img-responsive" />
												</a>
											</div>
										<?php endif; ?>
										<div class="agileinfo_tab1_img_grid">
											<a href="javascript:void(0)" name="TBMM" id="TBMM" onclick="selectMethod('tbl')">
												<img src="option-images/t-cash.png" alt=" " class="img-responsive" />
											</a>
										</div>
                    					<div class="agileinfo_tab1_img_grid">
											<a href="javascript:void(0)" onclick="selectMethod('mCash_iBank')">
												<img src="option-images/islami-bank-m-cash.png" alt=" " class="img-responsive" />
											</a>
										</div>
									<!-- Mobile Wallet //end-->		
										<div class="clear"> </div>
									</div>
								</li>
							  </ul>
							</div>
						</div>
            <div class="agileinfo_tab3">
							<div class="agileinfo_tab1">
							  <ul>
								<li>
									<div class="agileinfo_tab1_img_grids">
									<!-- Online Banking //start-->		
										<div class="agileinfo_tab1_img_grid">
											<a href="javascript:void(0)" onclick="selectMethod('ibbl')">
												<img src="option-images/islami-bank.png" alt=" " class="img-responsive" />
											</a>
										</div>
									<!-- Online Banking //end-->		
										<div class="clear"> </div>
									</div>
								</li>
							  </ul>
							</div>
						</div>
					</div>
				</div>
			</div>
</form>			
		</div>		
		<div class="agileits_copyright">
			<p>© shurjoPay</p>
		</div>
	</div>
	<script src="js/easy-responsive-tabs.js"></script>

	<script>
		$(document).ready(function () {
			$('#horizontalTab').easyResponsiveTabs({
				type: 'default', //Types: default, vertical, accordion           
				width: 'auto', //auto or any width like 600px
				fit: true,   // 100% fit in a container
				//closed: 'accordion', // Start closed if in accordion view
				activate: function(event) { // Callback function if tab is switched
				var $tab = $(this);
				var $info = $('#tabInfo');
				var $name = $('span', $info);
				$name.text($tab.text());
				$info.show();
				}
			});
		});
	</script>

	<script>
		function selectMethod(method) {
			$(document).ready(function () {
				$('#paymentOption').val(method); 
				$('#frmMethod').submit(); 
			});
		} 
	</script>
</body>
</html>
