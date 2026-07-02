<?php
class Database {
	function check_database_exist_or_not($data){
		$hostname = $data['hostname'];
		$username = $data['username'];
		$password = $data['password'];
		$database_name = $data['database'];

		// Creating a connection
		$conn = new mysqli($hostname, $username, $password);
		// Check connection
		if ($conn->connect_error) {
		    die("Connection failed: " . $conn->connect_error);
		} 

		$q3=$conn->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$database_name'");
		if(mysqli_num_rows($q3)>0){
			//Success
			return true;
		}
		return false;
	}
	function create_database($data){
		$mysqli = new mysqli($data['hostname'],$data['username'],$data['password'],'');
		if(mysqli_connect_errno())
			return false;
		$mysqli->query("CREATE DATABASE IF NOT EXISTS ".$data['database']);
		$mysqli->close();
		return true;
	}

	function is_database_empty($data){
		$hostname = $data['hostname'];
		$username = $data['username'];
		$password = $data['password'];
		$database_name = $data['database'];

		$conn = new mysqli($hostname, $username, $password, $database_name);
		if ($conn->connect_error) {
			return false;
		}

		$q = $conn->query("SELECT COUNT(*) AS c FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '".$conn->real_escape_string($database_name)."'");
		$row = $q->fetch_assoc();
		$conn->close();

		return ((int)$row['c'] === 0);
	}

	function create_tables($data){
		$response = file_get_contents('db.txt',true);
		if(true){

			$con1=mysqli_connect($data['hostname'],$data['username'],$data['password'],$data['database']);

			$q1=mysqli_multi_query($con1,$response);

			if($q1){
				return true;
			}
			else{
				echo $this->support();
				echo "<div class='container'>
                <div class='col-md-4 col-md-offset-4'>
        					<p class='alert alert-warning alert-dismissible'>
        					 <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                                <span aria-hidden='true'>&times;</span>
                            </button>
        					Failed To Install!! Check you already Updated Application
        					</p>
        		</div>
        		</div>";
        		exit;

				///echo "";exit();
				//return false;
			}
		}
	}//create_tables() end
	
	function support(){
		return "
		<link href='https://maxcdn.bootstrapcdn.com/bootswatch/3.3.7/cosmo/bootstrap.min.css' rel='stylesheet'>
		<br>
		<div class='container'>
        <div class='col-md-4 col-md-offset-4'>
					<p class='alert alert-success alert-dismissible'>
					If you facing any issue in Installtion, Please Contact Support. <br>
					Email: support@creatantech.com
					</p>
		</div>
		</div>
		<br>
				";
	}
}
