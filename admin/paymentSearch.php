<?php
	session_start();
	include("../includes/configure.php");
	require_once 'database.php';
	if(!$_SESSION['ORDER_DETAILS']['loggedINAdmin']) {    
	 	header('Location: login.php');
	}

	require_once('lib/ORM.php');
	$db = new ORM($GLOBALS["___mysqli_sm"]);
	// prepared merchant combo
	$va = $db->getMerchants();	
	// get search options	
	$serachRecord = array();
	if($_POST['submit'])
	{
		$search['from_date'] = isset($_POST['from_date']) && !empty($_POST['from_date']) ?$_POST['from_date']." 00:00:00":'';
		if(!empty($_POST['to_date']))
			$search['to_date']   = $_POST['to_date']." 23:59:59";
		else
			$search['to_date']   = $_POST['from_date']." 23:59:59";

		$search['uid'] = $_POST['uid'];
		$search['bank_status'] = $_POST['bank_status'];
		$search['txid'] = $_POST['txid'];
		$search['bank_tx_id'] = $_POST['bank_tx_id'];		
		$serachRecord = $db->getPaymentRecords($search);

	}
	// get result
	// var_dump($serachRecord['uid']);
	// set result to show in grid


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Payment Search!</title>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1"/>
	<meta name="description" content="description"/>
	<meta name="keywords" content="keywords"/> 
	<meta name="#" content="#"/> 	
	<link rel="stylesheet" media="screen" href="css/style.css" media="all" />
	<title>..::MIS::..</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</head>

<body>
	<div class="main">
		<div class="container">

			<div class="gfx">
			  <div class="logo">	
				<span class="left"><a href=""><img src="images/sand-box-logo.jpg" /></a></span>		
				<span class="right"><div id="merchantlogo"><img src="./img/merchantimage" alt="SHURJOMUKHI"></div></span>
				<div class="clearer"><span></span></div>	
			  </div>
	                    
			</div>

			<div class="menu">
				<a href="loginMIS.php"><span >Search by Date</span></a>                   
			</div>
			<div style="float:right;font-size:15px" class="menu"><a href="logout.php"><span >Logout</span></a></div>
			<div style="float:right;font-size:15px" class="menu"><a href="navigation.php"><span >Home</span></a>
			</div>
			<div class="content">
				<div class="container">
					
				
					<!--Search Form-->	
					<div class="row">
						<form method="post"  class="needs-validation" novalidate="">
							<div class="row">
								<div class="col-md-2 mb-2">
								<input type="text" class="form-control" id="from_date" name="from_date"  placeholder="From (Y-m-d)" value="" required="">
								</div>
								<div class="col-md-2 mb-2">
									<input type="text" class="form-control" id="to_date" name="to_date" placeholder="To (Y-m-d)" value="" required="">
								</div>
								<div class="col-md-2 mb-2">
									<input type="text" class="form-control" id="txid" name="txid" placeholder="Txid" value="" required="">
								</div>
								<div class="col-md-2 mb-2">
									<input type="text" class="form-control" id="bank_tx_id" name="bank_tx_id" placeholder="Bank ID" value="" required="">
								</div>
								<div class="col-md-2 mb-2">
									<select class="custom-select d-block w-100" id="bank_status" name="bank_status" required="">
						              <option value="">Status</option>
						              <option value="SUCCESS">SUCCESS</option>
						              <option value="FAIL">FAIL</option>
						              <option value="NULL">Initialized</option>
						            </select>
								</div>								
								<div class="col-md-2 mb-2">

									<select class="custom-select d-block w-100" id="uid" name="uid" required="">
									<option  value="">Select Merchant</option>
									<?php foreach($db->getMerchants() as $key=>$val):?>	
										<option value="<?=$val['merchant_id']?>">
											<?=$val['merchant_name']?>
										</option>					              
						            <?php endforeach;?> 
									 
						            </select>
								</div>								
								
							</div>	
							<hr class="mb-3">
								<div class="row">
									<div class="col-md-2 mb-2">	
										<button style="background-color: #95cebf;color: #000;" class="btn btn-primary btn-lg btn-block" value="submit" name="submit" type="submit">Search</button>
									</div>
        						
        						</div>
						</form>
					</div>
				</div>

				<!--Search Result-->
				<div class="row">
					<div class="table-responsive">
						<table class="table table-bordered table-sm">
				          <thead>
				            <tr>
				              <th>Txid</th>
				              <th>Order ID</th>
				              <th>Bank ID</th>
				              <th>Method</th>
				              <th>Amount</th>
				              <th>Intime</th>
				              <th>Status</th>
				            </tr>
				          </thead>
				          <tbody>
				          	<?php foreach($serachRecord as $row):?>
				          	
				            <tr>
				              <td><a target="_blank" href="payment-details.php?txid=<?=$row['txid']?>" ><?=$row['txid']?></a></td>
				              <td><?=$row['order_id']?></td>
				              <td><?=$row['bank_tx_id']?></td>				              
				              <td><?=$row['method']?></td>
				              <td><?=$row['amount']?></td>
				              <td><?=$row['intime']?></td>
				              <td><?=$row['bank_status']?></td>
				            </tr>
				            <?php endforeach;?>            
				          </tbody>
				        </table>
					</div>
				</div>
	                    
			</div>
			
			<div class="footer">		
				<span class="left">&copy; 2018 <a href="#">shurjoPay</a></span>		
				<span class="right"><a href="#">Designed & Developed</a> by <a href="#">Shurjomukhi</a>
				</span>
				<div class="clearer"><span></span></div>		
			</div>
		</div>	
	</div>
</body>

</html>