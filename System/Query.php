<?php if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) { die('Access denied'); };
    class Query{
    	
        function insertArr($tableName, $values){

        	$config = new Config();
            $conn = $config->database();

		    $columns = implode(", ",array_keys($values));
            $columns = str_replace("'", '', $columns);
            $columns = str_replace('"', '', $columns);


            $escaped_values = array_map('mysql_real_escape_string',  array_values($values));

            foreach ($escaped_values as $idx=>$data) $escaped_values[$idx] = "'".$data."'";
            $values  = implode(", ", $escaped_values);
            $sql = "INSERT INTO $tableName ($columns) VALUES ($values)";
            //var_dump($conn); exit();

            if (mysqli_query($conn, $sql)) {
              echo "New record created successfully";
            } else {
              echo "Error: " . $sql . "<br>" . mysqli_error($conn);
            }

            mysqli_close($conn);
		}	


		function getArr($tableName,$field)
		{
			$config = new Config();
            $conn = $config->database();

            if(empty($field)){
            	$select = '*';
            } else {
            	$field = implode(",",$field);
            	$select = $field;
            }

            //var_dump($field); 

			$sql = "SELECT $select FROM $tableName ORDER BY id";

			//var_dump($sql);
			$result = $conn->query($sql);
			$data = $result->fetch_all(MYSQLI_ASSOC);
			//$data = $result->fetch_array(MYSQLI_ASSOC);
			return $data;

			$conn -> close();
		}



        function count_login($username,$password)
        {
            $config = new Config();
            $conn = $config->database();

            $sql="SELECT * FROM login WHERE username = '$username' AND password='$password'";

            //var_dump($sql); exit();
            $rowcount='';

            if ($result=mysqli_query($conn,$sql))
            {
                // Return the number of rows in result set
                $rowcount=mysqli_num_rows($result);
            }

            return $rowcount;
        }


        function profile($username)
        {
            $config = new Config();
            $conn = $config->database();

            $sql = "SELECT 
                    b.username, b.id_user,b.role
                    FROM login as a 
                    LEFT JOIN  profile as b ON a.username = b.username 
                    WHERE a.username='$username'";

            //$sql = array_map('mysql_real_escape_string',  array_values($sql));

            //var_dump($sql);
            $result = $conn->query($sql);
            $data = $result->fetch_array(MYSQLI_ASSOC);
            //$data = $result->fetch_array(MYSQLI_ASSOC);
            return $data;

            $conn -> close();
        }


        function check_user($id_user)
        {
            $config = new Config();
            $conn = $config->database();

            $sql="SELECT username FROM profile WHERE id_user='$id_user'";

            $rowcount = '';
            if ($result=mysqli_query($conn,$sql))
            {
                // Return the number of rows in result set
                $rowcount=mysqli_num_rows($result);

            }

            return $rowcount;

            mysqli_close($con);
        }
        
    }
        
?>