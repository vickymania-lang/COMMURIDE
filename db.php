<?php
session_start();
	class Database {

    private $db;
    private static $instance;

		// private constructor
    private function __construct() {
		$servername = "localhost";
		$username = "root";
		$password = "";

		try {
			$this->db = new PDO("mysql:host=$servername;dbname=commuride", $username, $password);
			// set the PDO error mode to exception
			$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			}
		catch(PDOException $e){
			echo "Connection failed: " . $e->getMessage();
		}
    }
    public static function getInstance() {
        if (!self::$instance instanceof self) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function __clone() {
        trigger_error('Clone is not allowed.', E_USER_ERROR);
    }

    public function __wakeup() {
        trigger_error('Deserializing is not allowed.', E_USER_ERROR);
    }

	public function get_pass($tab,$col){
		try{
			$que= $this->db->prepare("SELECT $tab FROM $col");
			$que->execute([]);
			$SingleVar = $que->fetchColumn();
			return $SingleVar;
			$que = null;
		} catch(PDOException $e){
			echo 'Error: ' . $e->getMessage();
		}
	}


	//delete

	public function delete($tab,$col,$value) {
		
		try{
			$stmt = $this->db->prepare("DELETE FROM $tab WHERE $col=?");
			$stmt->execute([$value]);
			$success = 'Done';
			return $success;
			$stmt = null;
		} catch (PDOException $e) {
			// For handling error
			echo 'Error: ' . $e->getMessage();

		}
	}
	
	public function select_count_where($table, $col2, $val){
		
		$stmt = $this->db->prepare("SELECT COUNT(*) FROM $table WHERE $col2 = ? ");
		$stmt->execute([$val]);
		$count = $stmt->fetch(PDO::FETCH_COLUMN);
		return $count;
		$stmt = null;
	}

	public function count($table){
		
		$stmt = $this->db->prepare("SELECT COUNT(*) FROM $table");
		$stmt->execute([]);
		$count = $stmt->fetch(PDO::FETCH_COLUMN);
		return $count;
		$stmt = null;
	}

	public function member_login($table,$email,$pass){

		try {
			$que= $this->db->prepare("SELECT * FROM member WHERE email= ?");
			$que->execute([$email]);
			$count= $que->rowCount();
				if ($count===1) {
					$row = $que->fetch(PDO::FETCH_OBJ);
					$email = $row->email;
					$password = $row->password;
					if (password_verify($pass, $password)) {

					$user_email= $email;
					$_SESSION['user'] = $email;
					//header("Location:index.php");
					return "Done";
					}else{
							return "Login Error: Invalid login details";
						}
				}else{ return "<div class='alert alert-danger'>No user with this detail</div>";}
			}catch (PDOException $e) {
			// For handling error
			echo 'Error: ' . $e->getMessage();
		}

	}

	//This method is for general select
	public function select($table){
		$stmt = $this->db->prepare("SELECT * FROM $table");
		$stmt->execute();
		$arr = $stmt->fetchAll();
		return $arr;
		$stmt = null;
	}

	// for select distinct
	public function select_distinct($table,$col,$col2, $col3, $val1, $val2,$val3){
		try {
			$que = $this->db->prepare("SELECT DISTINCT $col, $col2, $col3 FROM $table where class =? AND session=? AND term=?");
			$que->execute([$val1, $val2, $val3]);
			$arr = $que->fetchAll();
			return $arr;
			$que = null;
		} catch (PDOException $e) {
			// For handling error
			echo 'Error: ' . $e->getMessage();
		}
	}


	// for select distinct
	public function let_see($table,$col){
		try {
			$que = $this->db->prepare("SELECT DISTINCT $col FROM $table");
			$que->execute();
			$arr = $que->fetchAll();
			return $arr;
			$que = null;
		} catch (PDOException $e) {
			// For handling error
			echo 'Error: ' . $e->getMessage();
		}
	}
	

	// for select distinct where
	public function select_distinct_where($table,$col,$col2, $where,$where_val){
		try {
			$que = $this->db->prepare("SELECT DISTINCT $col, $col2 FROM $table WHERE $where=?");
			$que->execute([$where_val]);
			$arr = $que->fetchAll();
			return $arr;
			$que = null;
		} catch (PDOException $e) {
			// For handling error
			echo 'Error: ' . $e->getMessage();
		}
	}

	//Selecet order
	public function select_from_ord($table,$id,$ord){
		try {
			$que = $this->db->prepare("SELECT * FROM $table ORDER BY $id $ord");
			$que->execute();
			$arr = $que->fetchAll();
			return $arr;
			$que = null;
		} catch (PDOException $e) {
			// For handling error
			echo 'Error: ' . $e->getMessage();
		}
	}


	public function select_from_limit($table,$col,$user_id){
		try {
			$que= $this->db->prepare("SELECT * FROM $table WHERE $col= ? ORDER BY id DESC LIMIT 10 ");
			$que->execute([$user_id]);
			$arr = $que->fetchAll();
			return $arr;
			$que = null;
		} catch (PDOException $e) {
			// For handling error
			echo 'Error: ' . $e->getMessage();
		}
	}

	public function select_from_where($table,$col,$id){

		try {
			$que= $this->db->prepare("SELECT * FROM $table WHERE $col= ? LIMIT 1"); //using LIMIt fro optimization purpose
			$que->execute([$id]);
			return $que;
			$que = null;
		} catch (PDOException $e) {
			// For handling error
			echo 'Error: ' . $e->getMessage();
		}
	}

	public function select_from_while($table,$col,$val){
		try {
			$que= $this->db->prepare("SELECT * FROM $table WHERE $col= ?");
			$que->execute([$val]);
			return $que;
			$que = null;
		} catch (PDOException $e) {
			// For handling error
			echo 'Error: ' . $e->getMessage();
		}
	}

	public function select_where_double($table,$col,$col2,$val,$val2){
		try {
			$que= $this->db->prepare("SELECT * FROM $table WHERE $col= ? AND $col2=?");
			$que->execute([$val,$val2]);
			return $que;
			$que = null;
		} catch (PDOException $e) {
			// For handling error
			echo 'Error: ' . $e->getMessage();
		}
	}

	public function select_where_3($table,$col1,$col2,$col3,$val1,$val2,$val3){
		try {
			$que= $this->db->prepare("SELECT * FROM $table WHERE $col1= ? AND $col2=? AND $col3=?");
			$que->execute([$val,$val2,$val3]);
			return $que;
			$que = null;
		} catch (PDOException $e) {
			// For handling error
			echo 'Error: ' . $e->getMessage();
		}
	}

	public function select_count_where_3($table,$col1,$col2,$col3,$val1,$val2,$val3){
		try {
			$que= $this->db->prepare("SELECT * FROM $table WHERE $col1= ? AND $col2=? AND $col3=?");
			$que->execute([$val1,$val2,$val3]);
			$arr = $que->rowCount();
			return $arr;
			$que = null;
		} catch (PDOException $e) {
			// For handling error
			echo 'Error: ' . $e->getMessage();
		}
	}
	public function select_count_where_4($table,$col1,$col2,$col3,$col4,$val1,$val2,$val3,$val4){
		try {
			$que= $this->db->prepare("SELECT * FROM $table WHERE $col1= ? AND $col2=? AND $col3=? AND $col4 = ?");
			$que->execute([$val1,$val2,$val3, $val4]);
			$arr = $que->rowCount();
			return $arr;
			$que = null;
		} catch (PDOException $e) {
			// For handling error
			echo 'Error: ' . $e->getMessage();
		}
	}

	public function select_table($table){
		$stmt = $this->db->prepare("SELECT * FROM $table");
		$stmt->execute();
		return $stmt;
		$stmt = null;
	}//end class


	public function select_from_where_limit($table, $user_id, $offset, $limit){
		try {
			$que= $this->db->prepare("SELECT * FROM $table WHERE user_id = ? ORDER BY id DESC LIMIT $offset, $limit");
			$que->execute([$user_id]);
			return $que;
			$que = null;
		} catch (PDOException $e) {
			// For handling error
			echo 'Error: ' . $e->getMessage();
		}
	}

	//update db fro when a user logs output_add_rewrite_var
	public function logout($admin_id){
		try{
			unset($_SESSION['admin_id']);
		} catch (PDOException $e) {
			// For handling error
			echo 'Error: ' . $e->getMessage();
		}
	}

	//insert support
	public function insert_front_support($name, $email, $phone,  $message){
		try {

				include '../inc/mail/mail_script.php';  //needed for sending emails
				inform_admin_contact($email, $name, $phone, $message);
				$success = 'Done';
				return $success;

		} catch (PDOException $e) {
			// For handling error
			echo 'Error: ' . $e->getMessage();
		}
	}

	public function editAdmin($username,$hash){
		try {
			$one = 1;
			$stmt = $this->db->prepare("UPDATE admin SET username = ?, password = ? WHERE admin_id = ?")->execute([$username,$hash, $one]);
			$stmt = null;
			return 'Done';
		} catch (PDOException $e) {
			// For handling error
			echo '<div class="alert alert-danger">
					Could not be updated
				  </div>: ' . $e->getMessage();
		}
	}

	public function get_admin_pass($col, $tab, $where){
		try{
			$val = 1;
			$que= $this->db->prepare("SELECT $col FROM $tab WHERE $where = ?");
			$que->execute([$val]);
			$SingleVar = $que->fetchColumn();
			return $SingleVar;
			$que = null;
		} catch(PDOException $e){
			echo 'Error: ' . $e->getMessage();
		}
	}

	public function get_admin_for_pass($email, $email_pass, $e_host, $val){
		
		try{
			$stmt = $this->db->prepare("UPDATE admin_setting SET email=?, email_password=?, e_host=? WHERE id = ?")->execute([$email, $email_pass, $e_host, $val]);
			$stmt = null;
			echo "Updated Successfully!";
		} catch (PDOException $e) {
			// For handling error
			echo 'Error: ' . $e->getMessage();
		}
	}
	// GENERAL INSERT FUNCTION BASED ON 2 COLUMNS
	public function insert_double_col($table, $col1, $col2, $val1, $val2){
		try {
		 $sql = "INSERT INTO $table ($col1, $col2) VALUES(?, ?)";
		$run = $this->db->prepare($sql);
		$run->execute([ $val1, $val2]);
		return "Done";
		} catch (PDOException $e) {
			echo "Error Occured". $e->getMessage();
		}

	}
	// GENERAL UPDATE FUNCTION BASED ON 2 COLUMNS
	public function update_double_col($table, $col1, $col2, $val1, $val2, $where, $where_val){
		try {
		 $sql = "UPDATE $table SET $col1 =?, $col2 = ? WHERE $where = ?";
		$run = $this->db->prepare($sql);
		$run->execute([ $val1, $val2, $where_val]);
		return "Done";
		} catch (PDOException $e) {
			echo "Error Occured". $e->getMessage();
		}

	}

	// GENERAL UPDATE FUNCTION BASED ON 2 COLUMNS
	public function update_3col_2con($table, $col1, $col2,$col3, $where,$where2, $val1, $val2,$val3, $where_val,$where_val_2){
		try {
		 $sql = "UPDATE $table SET $col1 =?, $col2 = ?, $col3=? WHERE $where = ? AND $where2 = ?";
		$run = $this->db->prepare($sql);
		$run->execute([ $val1, $val2,$val3, $where_val,$where_val_2]);
		return "Done";
		} catch (PDOException $e) {
			echo "Error Occured". $e->getMessage();
		}

	}

		// GENERAL UPDATE FUNCTION BASED ON 2 COLUMNS
	public function delete_where3($table, $col1,$col2,$col3, $val1, $val2,$val3){
		try {
		 $sql = "DELETE FROM $table WHERE $col1 = ? AND $col2 = ? AND $col3=?";
		$run = $this->db->prepare($sql);
		$run->execute([$val1, $val2,$val3]);
		return "Done";
		} catch (PDOException $e) {
			echo "Error Occured". $e->getMessage();
		}

	}

	// GENERAL UPDATE FUNCTION BASED ON 2 COLUMNS
	public function update1($table, $col1,$val1, $where, $where_val){
		try {
		 $sql = "UPDATE $table SET $col1= ? WHERE $where = ?";
		$run = $this->db->prepare($sql);
		$run->execute([ $val1, $where_val]);
		return "Done";
		} catch (PDOException $e) {
			echo "Error Occured". $e->getMessage();
		}

	}

	// GENERAL UPDATE FUNCTION BASED ON 2 COLUMNS
	public function update1_where2($table, $col1,$val1, $where1,$where2, $where_val1,$where_val_2){
		try {
		 $sql = "UPDATE $table SET $col1= ? WHERE $where1 = ? AND $where2 = ?";
		$run = $this->db->prepare($sql);
		$run->execute([ $val1, $where_val1, $where_val_2]);
		return "Done";
		} catch (PDOException $e) {
			echo "Error Occured". $e->getMessage();
		}

	}

	// GENERAL UPDATE FUNCTION BASED ON 2 COLUMNS
	public function update1_where3($table, $col1,$val1, $where1,$where2,$where3, $where_val1,$where_val2,$where_val3){
		try {
		 $sql = "UPDATE $table SET $col1= ? WHERE $where1 = ? AND $where2 = ? AND $where3=?";
		$run = $this->db->prepare($sql);
		$run->execute([ $val1, $where_val1, $where_val2,$where_val3]);
		return "Done";
		} catch (PDOException $e) {
			echo "Error Occured". $e->getMessage();
		}

	}

	// GENERAL INSERT FUNCTION BASED ON 3 COLUMNS
	public function insert_tripple_col($table, $col1, $col2, $col3, $val1, $val2, $val3){
		try {
		 $sql = "INSERT INTO $table ($col1, $col2, $col3) VALUES(?, ?, ?)";
		$run = $this->db->prepare($sql);
		$run->execute([ $val1, $val2, $val3]);
		return "Done";
		} catch (PDOException $e) {
			echo "Error Occured". $e->getMessage();
		}

	}

	// GENERAL INSERT FUNCTION BASED ON 13 COLUMNS
	public function insert_13_col($table, $c1, $c2, $c3,$c4,$c5,$c6,$c7,$c8,$c9,$c10,$c11,$c12,$c13,$v1, $v2, $v3,$v4,$v5,$v6,$v7,$v8,$v9,$v10,$v11,$v12,$v13){
		try {
		 $sql = "INSERT INTO $table ($c1, $c2, $c3,$c4,$c5,$c6,$c7,$c8,$c9,$c10,$c11,$c12,$c13) VALUES(?, ?, ?,?, ?, ?,?, ?, ?,?, ?, ?,?)";
		$run = $this->db->prepare($sql);
		$run->execute([ $v1, $v2, $v3,$v4,$v5,$v6,$v7,$v8,$v9,$v10,$v11,$v12,$v13]);
		return "Done";
		} catch (PDOException $e) {
			echo "Error Occured". $e->getMessage();
		}
	}

	// GENERAL INSERT FUNCTION BASED ON 13 COLUMNS
	public function insert_14_col($table, $c1, $c2, $c3,$c4,$c5,$c6,$c7,$c8,$c9,$c10,$c11,$c12,$c13,$c14,$v1, $v2, $v3,$v4,$v5,$v6,$v7,$v8,$v9,$v10,$v11,$v12,$v13,$v14){
		try {
		 $sql = "INSERT INTO $table ($c1, $c2, $c3,$c4,$c5,$c6,$c7,$c8,$c9,$c10,$c11,$c12,$c13,$c14) VALUES(?, ?, ?,?, ?, ?,?, ?, ?,?, ?, ?,?,?)";
		$run = $this->db->prepare($sql);
		$run->execute([ $v1, $v2, $v3,$v4,$v5,$v6,$v7,$v8,$v9,$v10,$v11,$v12,$v13,$v14]);
		return "Done";
		} catch (PDOException $e) {
			echo "Error Occured". $e->getMessage();
		}
	}

	// GENERAL INSERT FUNCTION BASED ON 10 COLUMNS
	public function insert_10($table, $c1, $c2, $c3,$c4,$c5,$c6,$c7,$c8,$c9,$c10,$v1, $v2, $v3,$v4,$v5,$v6,$v7,$v8,$v9,$v10){
		try {
		 $sql = "INSERT INTO $table ($c1, $c2, $c3,$c4,$c5,$c6,$c7,$c8,$c9,$c10) VALUES(?, ?, ?,?, ?, ?,?, ?, ?,?)";
		$run = $this->db->prepare($sql);
		$run->execute([ $v1, $v2, $v3,$v4,$v5,$v6,$v7,$v8,$v9,$v10]);
		
		return "Done";
		} catch (PDOException $e) {
			echo "Error Occured". $e->getMessage();
		}
	}

	// GENERAL INSERT FUNCTION BASED ON 11 COLUMNS
	public function insert_11_col($table, $c1, $c2, $c3,$c4,$c5,$c6,$c7,$c8,$c9,$c10,$col11,$v1, $v2, $v3,$v4,$v5,$v6,$v7,$v8,$v9,$v10,$v11){
		try {
		 $sql = "INSERT INTO $table ($c1, $c2, $c3,$c4,$c5,$c6,$c7,$c8,$c9,$c10,$col11) VALUES(?, ?, ?,?, ?, ?,?,?, ?, ?,?)";
		$run = $this->db->prepare($sql);
		$run->execute([ $v1, $v2, $v3,$v4,$v5,$v6,$v7,$v8,$v9,$v10,$v11]);
		
		return "Done";
		} catch (PDOException $e) {
			echo "Error Occured". $e->getMessage();
		}
	}

	// GENERAL INSERT FUNCTION BASED ON 11 COLUMNS
	public function insert_12_col($table, $c1, $c2, $c3,$c4,$c5,$c6,$c7,$c8,$c9,$c10,$col11,$col12,$v1, $v2, $v3,$v4,$v5,$v6,$v7,$v8,$v9,$v10,$v11,$v12){
		try {
		 $sql = "INSERT INTO $table ($c1, $c2, $c3,$c4,$c5,$c6,$c7,$c8,$c9,$c10,$col11,$val12) VALUES(?, ?, ?, ?,?, ?, ?,?,?, ?, ?,?)";
		$run = $this->db->prepare($sql);
		$run->execute([ $v1, $v2, $v3,$v4,$v5,$v6,$v7,$v8,$v9,$v10,$v11,$v12]);
		
		return "Done";
		} catch (PDOException $e) {
			echo "Error Occured". $e->getMessage();
		}
	}

	// GENERAL update FUNCTION BASED ON 13 COLUMNS
	public function update_12_col($table, $c1, $c2, $c3,$c4,$c5,$c6,$c7,$c8,$c9,$c10,$c11,$c12,$con, $v1, $v2, $v3,$v4,$v5,$v6,$v7,$v8,$v9,$v10,$v11,$v12,$id){
		try {
		$sql = "UPDATE $table SET $c1=?, $c2=?, $c3=?, $c4=?, $c5=?, $c6=?, $c7=?, $c8=?, $c9=?, $c10=?, $c11=?, $c12=? WHERE $con=?";
		$run = $this->db->prepare($sql);
		$run->execute([ $v1, $v2, $v3,$v4,$v5,$v6,$v7,$v8,$v9,$v10,$v11,$v12,$id]);
		
		return "Done";
		} catch (PDOException $e) {
			echo "Error Occured". $e->getMessage();
		}
	}

	

	//GENERAL update Function BASED ON 14 COLUMNS
	public function update_14_cols($table, $c1, $c2, $c3,$c4,$c5,$c6,$c7,$c8,$c9,$c10,$c11,$c12,$c13,$c14,$con, $v1, $v2, $v3,$v4,$v5,$v6,$v7,$v8,$v9,$v10,$v11,$v12,$v13,$v14,$id){
		try {
		$sql = "UPDATE $table SET $c1=?, $c2=?, $c3=?, $c4=?, $c5=?, $c6=?, $c7=?, $c8=?, $c9=?, $c10=?, $c11=?, $c12=?, $c13=?, $c14=? WHERE $con=?";
		$run = $this->db->prepare($sql);
		$run->execute([ $v1, $v2, $v3,$v4,$v5,$v6,$v7,$v8,$v9,$v10,$v11,$v12,$v13,$v14,$id]);
		
		return "Done";
		} catch (PDOException $e) {
			echo "Error Occured". $e->getMessage();
		}
	}

	public function insert1($table, $c1, $v1){
		try {
		 $sql = "INSERT INTO $table ($c1) VALUES(?)";
		$run = $this->db->prepare($sql);
		$run->execute([ $v1]);	
		return "Done";
		} catch (PDOException $e) {
			echo "Error Occured". $e->getMessage();
		}
	}

	public function insert4($table, $c1, $c2, $c3,$c4,$v1, $v2, $v3,$v4){
		try {
		 $sql = "INSERT INTO $table ($c1, $c2, $c3,$c4) VALUES(?, ?,?, ?)";
		$run = $this->db->prepare($sql);
		$run->execute([ $v1, $v2, $v3,$v4]);	
		return "Done";
		} catch (PDOException $e) {
			echo "Error Occured". $e->getMessage();
		}
	}

	public function insert5($table, $c1, $c2, $c3,$c4,$c5,$v1, $v2, $v3,$v4,$v5){
		try {
		 $sql = "INSERT INTO $table ($c1, $c2, $c3,$c4,$c5) VALUES(?, ?, ?,?, ?)";
		$run = $this->db->prepare($sql);
		$run->execute([ $v1, $v2, $v3,$v4,$v5]);	
		return "Done";
		} catch (PDOException $e) {
			echo "Error Occured". $e->getMessage();
		}
	}

	public function insert6($table, $c1, $c2, $c3,$c4,$c5,$c6,$v1, $v2, $v3,$v4,$v5,$v6){
		try {
		 $sql = "INSERT INTO $table ($c1, $c2, $c3,$c4,$c5,$c6) VALUES(?, ?, ?,?, ?,?)";
		$run = $this->db->prepare($sql);
		$run->execute([ $v1, $v2, $v3,$v4,$v5,$v6]);	
		return "Done";
		} catch (PDOException $e) {
			echo "Error Occured". $e->getMessage();
		}
	}

	public function insert7($table, $c1, $c2, $c3,$c4,$c5,$c6,$c7,$v1, $v2, $v3,$v4,$v5,$v6,$v7){
		try {
		 $sql = "INSERT INTO $table ($c1, $c2, $c3,$c4,$c5,$c6,$c7) VALUES(?, ?, ?,?, ?, ?,?)";
		$run = $this->db->prepare($sql);
		$run->execute([ $v1, $v2, $v3,$v4,$v5,$v6,$v7]);	
		return "Done";
		} catch (PDOException $e) {
			echo "Error Occured". $e->getMessage();
		}
	}

	public function insert8($table, $c1, $c2, $c3,$c4,$c5,$c6,$c7,$c8,$v1, $v2, $v3,$v4,$v5,$v6,$v7,$v8){
		try {
		 $sql = "INSERT INTO $table ($c1, $c2, $c3,$c4,$c5,$c6,$c7,$c8) VALUES(?, ?, ?,?, ?, ?,?,?)";
		$run = $this->db->prepare($sql);
		$run->execute([ $v1, $v2, $v3,$v4,$v5,$v6,$v7,$v8]);	
		return "Done";
		} catch (PDOException $e) {
			echo "Error Occured". $e->getMessage();
		}
	}

		//This method is for general select
	public function select_where_id($table,$col_id, $id){
		$stmt = $this->db->prepare("SELECT * FROM $table WHERE $col_id = ? ");
		$stmt->execute([$id]);
		$arr = $stmt->fetchAll();
		return $arr;
		$stmt = null;
	}

		//This method is for selecting single column
	public function select_single_id($table,$col_to_select,$col_id, $id){
		$stmt = $this->db->prepare("SELECT $col_to_select FROM $table WHERE $col_id = ? ");
		$stmt->execute([$id]);
		$arr = $stmt->fetchAll();
		return $arr;
		$stmt = null;
	}

		//This method is for selecting double column
	public function select_double_id($table,$col_to_select1,$col_to_select2,$col_id, $id){
		$stmt = $this->db->prepare("SELECT $col_to_select1,$col_to_select2 FROM $table WHERE $col_id = ? ");
		$stmt->execute([$id]);
		$arr = $stmt->fetchAll();
		return $arr;
		$stmt = null;
	}

	//This method is for select multiple
	public function select_multiple($table,$col1,$col2,$col3,$col4,$val1,$val2,$val3,$val4){
		
	try{
		($que= $this->db->prepare("SELECT * FROM $table WHERE ($col1 = ? AND $col2 = ?) AND ($col3 = ? AND $col4 = ?)"));
		$que->execute([$val1,$val2,$val3,$val4]);
			return $que;
			$que = null;
		} catch (PDOException $e) {
			// For handling error
			echo 'Error: ' . $e->getMessage();
		}
	}


	//This method is for select multiple
	public function select_where_4($table,$col1,$col2,$col3,$col4,$val1,$val2,$val3,$val4){
		try{
		($que= $this->db->prepare("SELECT * FROM $table WHERE $col1 = ? AND $col2= ? AND $col3 = ? AND $col4 = ?"));
		$que->execute([$val1,$val2,$val3,$val4]);
		$arr = $que->fetchAll();
		} catch (PDOException $e) {
			// For handling error
			echo 'Error: ' . $e->getMessage();
		}
	}

		//This method is for select multiple
	public function select_result($table,$col1,$col2,$col3,$col4,$val1,$val2,$val3,$val4){
		try{
		($que= $this->db->prepare("SELECT * FROM result WHERE reg_number = '$val1' AND term= '$val2' AND class= '$val3' AND session = '$val4'"));
		$que->execute();
			return $que;
			//$que = null;
		} catch (PDOException $e) {
			// For handling error
			echo 'Error: ' . $e->getMessage();
		}
	}

	//select from table where its like
	public function select_like($table,$col1,$col2,$val){
		try {
			$que= $this->db->prepare("SELECT * FROM $table WHERE $col1 LIKE ? || $col2 LIKE ?");
			$que->execute([$val,$val]);
			$arr = $que->fetchAll();
			return $arr;
			$que = null;
		} catch (PDOException $e) {
			// For handling error
			echo 'Error: ' . $e->getMessage();
		}
	}
	//select from table where its like
	public function select_like_1($table,$col1,$val){
		try {
			$que= $this->db->prepare("SELECT * FROM $table WHERE $col1 LIKE ?");
			$que->execute([$val]);
			$arr = $que->fetchAll();
			return $arr;
			$que = null;
		} catch (PDOException $e) {
			// For handling error
			echo 'Error: ' . $e->getMessage();
		}
	}
	//count number of rows in select from table where its like
	public function select_like_count($table,$col1,$col2,$val){
		try {
			$que= $this->db->prepare("SELECT * FROM $table WHERE $col1 LIKE ? || $col2 LIKE ?");
			$que->execute([$val,$val]);
			$arr = $que->rowCount();
			return $arr;
			$que = null;
		} catch (PDOException $e) {
			// For handling error
			echo 'Error: ' . $e->getMessage();
		}
	}

	//check result restriction
	public function check_restriction($table,$col, $where1, $where2,$where3, $whereval1, $whereval2,$whereval3){
		
		try {
			$que= $this->db->prepare("SELECT $col FROM $table WHERE $where1='$whereval1' AND $where2='$whereval2' AND $where3='$whereval3'");
			$que->execute();
			$count=$que->rowCount();
			// $arr = $que->fetch(PDO::FETCH_OBJ);
			 return $count;
			$que = null;
		} catch (PDOException $e) {
			// For handling error
			echo 'Error: ' . $e->getMessage();
		}
	}
}
