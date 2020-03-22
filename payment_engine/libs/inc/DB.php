<?php
/*
we are using PDO to make DB Class

*/
class DB
{
	public $pdo;
	public $lastInsertId;
	public $affected_rows;
	public $pdo_error;
	public $error_msg;

	public function __construct(){
			try{
				//$this->pdo=new PDO('mysql:host='.T_DB_HOST.';dbname='.T_DB_NAME,T_DB_USER,T_DB_PASS);
				$this->pdo = new PDO('mysql:host=localhost;dbname=shurjopay', 'asif', 'smasif@1234');
				$this->pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
				
			}catch(PDOException $e)
			{
				$this->error_msg='<h2>Database Connection Error</h2>';
				$this->pdo_error=$e->getMessage();
				$this->error_log($this->pdo_error);
			}
		
	} // end constructor
	
	
	/*****
	@table_name= name of the table
	@data = filed_name => value paired array
	*/
	public function insert($table_name,$data){
		$sql="INSERT INTO `".$table_name."` SET ";
		foreach($data as $field=>$val) {
			$sql .=" `$field`=:$field,";
		}
		$sql=rtrim($sql,',');
		$statement=$this->pdo->prepare($sql);
		foreach($data as $field=>$val) {
			$statement->bindValue(":{$field}",$val);
		}
		try{
			$statement->execute();
			$this->lastInsertId=$this->pdo->lastInsertId();
			return true;
			
		}catch(PDOException $e){
			
			$this->error_msg='Insertion Failed';
			$this->pdo_error=$e->getMessage();
			$this->error_log($this->pdo_error);
			return false;
		}
		
		
	} // end insert()
	
	
	/*
	@table_name= name of the table
	@data = filed_name => value paired array
	@where = where clause for update the table
	*/	
	public function update($table_name, $data, $where,$join='OR') {
		$sql="UPDATE `".$table_name."` SET ";
		foreach($data as $field=>$val) {
			$sql .=" `{$field}`=:{$field},";
		}
		$sql=rtrim($sql,',');
		$sql .=' WHERE ';
		foreach($where as $field=>$val) 
		{
			$sql .="`{$field}`=:{$field} {$join}";
		}
		if($join=='OR') $sql =substr($sql,0,-2);
		if($join=='AND') $sql =substr($sql,0,-3);
		  
		$statement=$this->pdo->prepare($sql);
		foreach($where as $field=>$val) 
		{
			$statement->bindValue(":{$field}",$val);
		}
		foreach($data as $field=>$val) 
		{
			$statement->bindValue(":{$field}",$val);
		}
		
		try{
			$statement->execute();
			return true;
			
		}catch(PDOException $e){
			
			$this->error_msg='update Failed';
			$this->pdo_error=$e->getMessage();
			$this->error_log($this->pdo_error);
			return false;
		}
		
	} // end update()
	
	
	
	/*
	@table_name= name of the table
	@where = where clause for select rows
	@join = OR  | AND . default is OR
	*/	
	public function select($table_name,$where,$join='OR') {
		$join =strtoupper($join);
		$sql="SELECT * FROM `".$table_name."` WHERE ";
		foreach($where as $field=>$val) 
		{
			$sql .="`{$field}`=:{$field} {$join}";
		}
		if($join=='OR') $sql =substr($sql,0,-2);
		if($join=='AND') $sql =substr($sql,0,-3);
		  
		//$statement=$this->pdo->prepare($sql);
		foreach($where as $field=>$val) 
		{
			//$statement->bindValue(":{$field}",$val);
		}
		
		try{
			
			//if($statement->execute()===TRUE)
			//return $statement->fetchAll();
			
			
		}catch(PDOException $e){
			
			$this->error_msg='select Failed';
			$this->pdo_error=$e->getMessage();
			$this->error_log($this->pdo_error);
			return false;
		}
		
	} // end update()
		
		
	
	public function error_msg(){
		return $this->error_msg;
	}
	
	
	public function error_log($msg){
		file_put_contents("shurjo-pdo_errors.txt","\r\n\r\n".$msg,FILE_APPEND);
	}
	
	
	public function pdo_error(){
		return $this->pdo_error;
	}
	
	public function lastInsertId(){
		return $this->lastInsertId;
	}
	
	
	public function affected_rows(){
		return $this->affected_rows;
	}
	
	public function getTransactionType()
	{
		if(isset($_SESSION['CURRENT_TRANSACTION_TYPE']) && $_SESSION['CURRENT_TRANSACTION_TYPE']!="" )
		return $_SESSION['CURRENT_TRANSACTION_TYPE'];
		else
		return false;
	}
	
	
} // end class DB

?>
