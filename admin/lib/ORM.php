<?php

    class ORM {

    	private $host;
	    private $dbuser;
	    private $dbpass;
	    private $dbname;
	    private $TBLKeyCode;
	    private $TBLMerchantId;
	    private $BISCMerchantId;
	    private $BISCTBLMerchantId;
	    public $dbCon;

	    public function __construct($dbObject) {

			// initialize credentails
			$this->TBLKeyCode = 'b28eaabc-1840-4b9b-b519-41d8accd31fd';
			$this->TBLMerchantId = 'SHURJOMUKHI';
			$this->BISCMerchantId = '78';
			$this->BISCTBLMerchantId = 'BISC';

			$this->dbCon = $dbObject;
	    	
		}

		
		public function getCityBankSessionId($order_id)
		{
			$sql = "SELECT OrderID,SessionID FROM sp_mx_transactions WHERE transaction_id ='" . $order_id . "'";
			$sql_query = mysqli_query($this->dbCon, $sql);
			$rowMx = mysqli_fetch_object($sql_query);
			if(is_object($rowMx))
			{
				return $rowMx;//->SessionID;					
			}
		}

		public function getMerchants()
		{
			$sql   = "select * from sp_merchants";
			$query = mysqli_query($this->dbCon, $sql);	
			$rows  = mysqli_affected_rows($this->dbCon);
			$merchantCombo  = array();

			if($rows > 0)
			{ 
				while($row = mysqli_fetch_assoc($query)) 
				{
				   $merchantCombo[] = array(
				   		'merchant_id' => $row['id'],
				   		'merchant_name'=> $row['username']

				   );
				}
			}
			return $merchantCombo;			
		}

		public function getPaymentRecords($search = null)
		{
			$whereArr = array();			
			if($search['uid'] != "") $whereArr[] = "uid = {$search['uid']}";
			if($search['txid'] != "") $whereArr[] = "txid = '{$search['txid']}'";
			if($search['bank_tx_id'] != "") $whereArr[] = "bank_tx_id = '{$search['bank_tx_id']}'";
			if($search['bank_status'] != "") $whereArr[] = "bank_status = '{$search['bank_status']}'";
			if($search['from_date'] != "") $whereArr[] = "intime <= '{$search['from_date']}'";
			if($search['from_date'] != "" && $search['to_date'] != "") 
				$whereArr[] = "intime >= '{$search['to_date']}'";
			$whereStr = implode(" AND ", $whereArr);
			if(is_array($whereArr) && !empty($whereArr))
				$sql = "Select * from sp_epay WHERE {$whereStr} order by intime desc limit 10 ";
			else
				$sql = "Select * from sp_epay order by intime desc limit 10";			
			$query = mysqli_query($this->dbCon, $sql);	
			$rows  = mysqli_affected_rows($this->dbCon);
			$result  = array();
			$i = 1;
			if($rows > 0)
			{ 
				while($row = mysqli_fetch_assoc($query)) 
				{
				   $result[$i]['uid']	= $row['uid'];
				   $result[$i]['order_id'] = $row['order_id'];
				   $result[$i]['txid']	= $row['txid'];
				   $result[$i]['amount']	 =  $row['amount'];
				   $result[$i]['method']	 =  $row['method'];
				   $result[$i]['intime']	 =  $row['intime'];
				   $result[$i]['bank_status'] = $row['bank_status'];
				   $result[$i]['bank_tx_id']  = $row['bank_tx_id'];
					$i++;
				}
				
			}

			return $result;

		}

		public function getMerchantCredentials($merchantID = null)
		{
			$sql   = "select * from sp_merchants where id = '$merchantID' limit 1";
			$query = mysqli_query($this->dbCon, $sql);			
			$rows  = mysqli_affected_rows($this->dbCon);
			$cred  = array();

			if($rows > 0)
			{ 
				while($row = mysqli_fetch_assoc($query)) 
				{
				   $cred['merchantId']	= $row['id'];
				   $cred['CityBankMerchant'] = $row['mx_merchant_id'];
				   $cred['BkashJson']	= $row['bkash_credentials'];
				   $cred['TBLKeyCode']	= $this->TBLKeyCode;
				   if($merchantID == $this->BISCMerchantId)
				   {
						$cred['TBLMerchantId']	= $this->BISCTBLMerchantId;	
				   }
				   else
				   {
				   		$cred['TBLMerchantId']	= $this->TBLMerchantId;	
				   }
				   
				   $cred['eblMerchantId']	= $row['ebl_merchant_id'];
				   $cred['eblPassword']	= $row['ebl_password'];


				}
			}

			return $cred;

		}

		public function getInitializedPayment($merchantId = null, $min = 5)
		{
			$sql   = "select txid, order_id, method, gateway, intime, bank_tx_id, remarks from sp_epay where uid = '$merchantId' and bank_status is null and bank_tx_id is not null and intime < ( NOW( ) - INTERVAL $min MINUTE ) ";

			$query = mysqli_query($this->dbCon, $sql);
			$rows  = mysqli_affected_rows($this->dbCon);
			$cred  = array();

			if($rows > 0)
			{ 
				$i = 1;
				while($row = mysqli_fetch_assoc($query)) 
				{
				   $cred[$i] = array(
				   		'txid' => $row['txid'],
				   		'order_id' => $row['order_id'],
				   		'method' => $row['method'],
				   		'gateway' => $row['gateway'],
				   		'intime' => $row['intime'],
				   		'bank_tx_id' => $row['bank_tx_id'],
				   		'remarks' => $row['remarks']
				   );

				   $i++;
				}
			}

			return $cred;
		}

		public function updateLog($updateData = null, $order_id)
		{
			
			if (count($updateData) > 0) 
			{
	            foreach ($updateData as $key => $value) 
	            {

	                $value = mysqli_real_escape_string($this->dbCon,$value); 
	                $value = "'$value'";
	                $updates[] = "$key = $value";
	            }
	        }

	        $implodeArray = implode(', ', $updates);	        
	        $sql = ("UPDATE sp_epay SET $implodeArray WHERE order_id = '$order_id' ");		        
	        try 
			{
				mysqli_query($this->dbCon,$sql) or die(mysqli_error($this->dbCon));
	        	// mysqli_close($this->dbCon);
	        	return TRUE;
			}
			catch (Exception $e) 
			{
				return $e->getMessage();
			}
	        
		}

		public function getPaymentDetails($txid)
		{
			$query_txid_details = "select uid,order_id,txid,amount,method,intime,gateway,fwdtime,bank_tx_id,gw_return_id,gw_return_msg,return_code,remarks,bank_status,bank_response, returnurl from sp_epay where txid = '".$txid."'";
			$response =  mysqli_query($this->dbCon, $query_txid_details);
			return mysqli_fetch_object($response);			
		}

    }



?>