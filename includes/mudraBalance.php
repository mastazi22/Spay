<?php


/**
this class will provide the balance related functionality  of a user

*/
class mudraBalance {

	public function __construct() {

	}

	/*
		this function will return user balance
		it takes one param
		1. userID 
	*/
	public function getUserBalance($userID,$con) {
		$userTransactions = $this->getUserHistories($userID, 'all',null,$con);
		$userDebit = 0;
		$userCredit = 0;
		$userBalance = 0;

		if (is_array($userTransactions) && count($userTransactions) > 0) {
			foreach ($userTransactions as $key => $value) {
				if ($value->userDebitCredit == 'c') {
					$userCredit += $value->amount;
				} 
				else if ($value->userDebitCredit == 'd') {
					$userDebit += $value->amount;
				}

			} // end foreach

			return $userBalance = $userCredit - $userDebit;
		} 
		else {
			return 0.00;
		}
		
	} // end getUserBalance
	/**
		this function will return transactions rows for a user
		it takes three parameters
		1. userID 
		2. limit [default is 5. ]
		3. dates  [[ optional as date range array. array('dateStart'=>'','dateEnd'=>'') ]. 
		if date range if defined then it will return rows between the date ranges] 
	*/
	public function getUserHistories($userID, $limit = 5, $dates = array (), $con) {
		$limit_srt = "LIMIT {$limit}";
		if ($limit == "all") {
			$limit_srt = "";
		}

		$dateStr = "";
		if (is_array($dates) && count($dates) > 0) {
			$dateStart = date("Y-m-d H:i:s", strtotime($dates['dateStart']));
			$dateEnd = date("Y-m-d H:i:s", strtotime($dates['dateEnd']));

			$dateStr = " AND (actionTime>='{$dateStart}' AND actionTime<='{$dateEnd}') ";

		}

		$sql = "SELECT txn.*,tp.typeName,tp.companyDebitCredit,tp.userDebitCredit 
										FROM transactions txn 
										LEFT JOIN  transaction_type tp 
										ON txn.transactionTypeID=tp.typeID 
										WHERE txn.userID='{$userID}' AND txn.status='SUCCESS' {$dateStr}
										ORDER BY txn.actionTime DESC
										{$limit_srt}
										";

		return $this->loadObjectList($sql, null, $con);
	} // end getUserTransactions
	/**
		this function will return total number of transactions done for a user
		it takes two parameters
		1. userID 
		2. dates  [[ optional as date ranges]. if date range if defined then it will return 
		total number between the date ranges] 
	*/
	public function getUserTotalHistory($userID, $dates = array (), $con) {

		$dateStr = "";
		if (is_array($dates) && count($dates) > 0) {
			$dateStart = date("Y-m-d H:i:s", strtotime($dates['dateStart']));
			$dateEnd = date("Y-m-d H:i:s", strtotime($dates['dateEnd']));
			$dateStr = " AND (actionTime>='{$dateStart}' AND actionTime<='{$dateEnd}') ";
		}
		$sql = "SELECT count(transactionID) as total 
										FROM transactions
										WHERE userID='{$userID}' AND status='SUCCESS' {$dateStr}
										ORDER BY actionTime DESC";

		$query = $this->query($sql);
		if ($this->affected_rows()) {
			$result = $query->fetchAll();
			return $result[0]->total;
		}
		return 0;
	}
	
	public function generateTxnID() {
		return uniqid('SMD');
	}
	
	public function doTransaction($userID,$amount,$transactionTypeID,$source,$remark='',$status='SUCCESS',$con) {
		$transactionID =$this->generateTxnID();
		$date=new DateTime();
		$date->setTimezone(new DateTimeZone('Asia/Dhaka'));
		$actionTime=$date->format("Y-m-d H:i:s"); 	

		
		$sql="INSERT INTO transactions SET 
										transactionID='{$transactionID}', 	
										userID='{$userID}', 	
										transactionTypeID='{$transactionTypeID}', 	
										amount='{$amount}', 	
										status='{$status}', 
										actionTime='{$actionTime}', 	
										remarks='{$remark}', 	
										source='{$source}'";
		$sql_query=mysqli_query($GLOBALS["___mysqli_sm"], $sql,$con);
		
		return $transactionID;
		
	} // end doTransaction
			
	
	function loadObjectList($sql, $key = '', $con) {
		if (!($cur = mysqli_query($GLOBALS["___mysqli_sm"], $sql,$con))) {
			return null;
		}
		$array = array ();
		while ($row = mysql_fetch_object($cur)) {
			if ($key) {
				$array[$row-> $key] = $row;
			} 
			else {
				$array[] = $row;
			}
		}
		mysql_free_result($cur);
		return $array;
	}

} // end class mudraBalance
?>