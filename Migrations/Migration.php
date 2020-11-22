<?php if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) { die('Access denied'); };
    class Migration{
    	function create_table($table,$arrayObject)
    	{
    		$count = $this->TableExists($table);
    		//var_dump($count);
    		if($count>0){
    			echo 'Table is Existing..';
    		} else {
    			$statement = implode(', ', array_map(
				    function ($v, $k) { return sprintf("%s %s", $k, $v); },
				    $arrayObject,
				    array_keys($arrayObject)
				));
	    		$sql = 'CREATE TABLE '.$table. '('.$statement.')';
	    		$config = new Config();
	    		$conn = $config->database();
	    		if ($conn->query($sql) === TRUE) {
	    			echo "Table MyGuests created successfully";
	    		} else {
	    			echo "Error creating table: " . $conn->error;
	    		}
    			
    		}
    	}


    	public function TableExists($table) {
    		$config = new Config();
    		$conn = $config->database();
			// SQL query
			$sql = "SELECT * FROM $table";

			$count = '0';
			$config = new Config();
    		$conn = $config->database();
    		if ($result = mysqli_query($conn, $sql)) {
			  // Get field information for all fields
			  while ($fieldinfo = mysqli_fetch_field($result)) {
			    $count = '1';
			  }
			  mysqli_free_result($result);
			}

			return $count;
		}

		public function drop_table($table) {
    		$config = new Config();
    		$conn = $config->database();

    		$sql = "DROP TABLE $table";
         
			if (mysqli_query($conn, $sql)) {
				echo "Record deleted successfully";
			} else {
				echo "Error deleting record: " . mysqli_error($conn);
			}
    	}

    	public function show_table()
    	{
    		$config = new Config();
    		$conn = $config->database();
    		$databaseName = $config->database_name();

			$sql = "SHOW TABLES FROM $databaseName";
			$result = mysqli_query($conn, $sql);

			//var_dump($result);

			if (!$result) {
				echo "DB Error, could not list tables\n";
				echo 'MySQL Error: ' . mysql_error();
				exit;
			}

			while ($row = mysqli_fetch_row($result)) {
				echo "Table: {$row[0]}\n";

				$sql = "SELECT * FROM {$row[0]}";
				$result2 = mysqli_query($conn, $sql);
				$fieldinfo = $result2 -> fetch_fields();

				$i=1	;
				foreach ($fieldinfo as $val) {
					echo '<br>';
				    printf("Column ".$i.": %s\n", $val -> name);
				    echo '<br>';
				    printf("Type: %s\n", $this->type_field($val -> type).'('.$val -> type.')');
				    $i++;
				  }

				echo '<br>';
				echo '<br>';
			}

			mysqli_free_result($result);
		}
	

		function type_field($type)
		{
			switch ($type) {
				case '1': return 'tinyint';
					# code...
					break;
				
				case '2': return 'smallint';
					# code...
					break;

				case '3': return 'int';
					# code...
					break;

				case '4': return 'float';
					# code...
					break;

				case '5': return 'double';
					# code...
					break;

				case '7': return 'timestamp';
					# code...
					break;

				case '8': return 'bigint';
					# code...
					break;

				case '9': return 'mediumint';
					# code...
					break;

				case '10': return 'date';
					# code...
					break;

				case '11': return 'time';
					# code...
					break;

				case '12': return 'datetime';
					# code...
					break;

				case '13': return 'year';
					# code...
					break;

				case '16': return 'bit';
					# code...
					break;

				case '252': return 'varchar';
					# code...
					break;

				case '253': return 'varchar';
					# code...
					break;

				case '254': return 'char';
					# code...
					break;


				case '246': return 'decimal';
					# code...
					break;

				default:
					return 'Not Found';
					# code...
					break;
			}
		}



		function alter_table($data)
    	{
    		foreach($data as  $key => $value){
    			$config = new Config();
				$conn = $config->database();

	    		if (!$conn->query($value)) {
				    echo "Table creation failed: (" . $conn->errno . ") " . $conn->error;
				    echo '<br>';
				} else {
					echo 'Success';
					echo '<br>';
				}
    		}
    	}	



    	public function drop_field($table,$field) {
    		$config = new Config();
    		$conn = $config->database();

    		$sql = "ALTER TABLE $table DROP COLUMN $field";
         
			if (mysqli_query($conn, $sql)) {
				echo "Record deleted successfully";
			} else {
				echo "Error deleting record: " . mysqli_error($conn);
			}
    	}



    	public function rename_field($table,$field,$newfield,$type) {
    		$config = new Config();
    		$conn = $config->database();

    		$sql = "ALTER TABLE $table CHANGE COLUMN $field $newfield $type";
         
			if (mysqli_query($conn, $sql)) {
				echo "Record rename successfully";
			} else {
				echo "Error rename record: " . mysqli_error($conn);
			}
    	}

    }