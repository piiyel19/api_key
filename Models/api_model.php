<?php
    class ApiModel
    {
        function __construct()
        {

            $this->config = new Config();
            $this->query = new Query();
            $this->security = new Security();
        }

        public function get($table='')
        {
            $API_KEY = $_GET['api_key'];
            if($this->config->api_key()=='123'){

                $link = $_SERVER['PHP_SELF'];
                $link_array = explode('/',$link);
                $get_id = end($link_array);

                if(!empty($get_id)){
                    if(is_numeric($get_id)){
                        $sql = "SELECT * FROM $table WHERE id=$get_id";
                    } else {
                        $sql = "SELECT * FROM $table";
                    }
                } else {
                    $sql = "SELECT * FROM $table";
                }

                $conn = $this->config->database();
                $sql = mysqli_real_escape_string($conn,$sql);
                $sql = $this->security->clean($sql);
                $query = mysqli_query($conn, $sql );
                $query = mysqli_fetch_all($query);

                header('Content-Type: application/json');
                return json_encode($query);
            } else {
                echo json_encode(array( 'response'=>'api key not valid..' ));
            }
        }



        public function post($table='')
        {
            $array1 = $_POST['myform'];
            $array2 = array('API_KEY'=>$this->config->api_key());
            $api_pass = '';

            $intersect = array_intersect( $array1, $array2 );

            if(!empty($intersect)){
                if(!empty($_POST['myform'])){
                    $values = $_POST['myform'];
                    $skip = array_shift($values);
                    $columns = implode(", ",array_keys($values));
                    $columns = str_replace("'", '', $columns);
                    $columns = str_replace('"', '', $columns);

                    $escaped_values = array_map('mysql_real_escape_string',  array_values($values));

                    foreach ($escaped_values as $idx=>$data) $escaped_values[$idx] = "'".$data."'";
                    $values  = implode(", ", $escaped_values);
                    $sql = "INSERT INTO $table ($columns) VALUES ($values)";
                    $conn = $this->config->database();
                    $sql = $this->security->clean($sql);
                    if (mysqli_query($conn, $sql)) {
                        echo json_encode(array( 'response'=>'success created..' ));
                    } else {
                        echo json_encode(array( 'response'=>'data post not valid..' ));
                    }
                } else {
                    echo json_encode(array( 'response'=>'data post not valid..' ));
                }
            } else {
                echo json_encode(array( 'response'=>'api key not valid..' ));
            }
        }


        public function put($table='')
        {
            $raw_data = file_get_contents('php://input');
            $boundary = substr($raw_data, 0, strpos($raw_data, "\r\n"));

            $parts = array_slice(explode($boundary, $raw_data), 1);
            $data = array();

            foreach ($parts as $part) {
                if ($part == "--\r\n") break; 
                $part = ltrim($part, "\r\n");
                list($raw_headers, $body) = explode("\r\n\r\n", $part, 2);
                $raw_headers = explode("\r\n", $raw_headers);
                $headers = array();
                foreach ($raw_headers as $header) {
                    list($name, $value) = explode(':', $header);
                    $headers[strtolower($name)] = ltrim($value, ' '); 
                } 

                if (isset($headers['content-disposition'])) {
                    $filename = null;
                    preg_match(
                        '/^(.+); *name="([^"]+)"(; *filename="([^"]+)")?/', 
                        $headers['content-disposition'], 
                        $matches
                    );
                    list(, $type, $name) = $matches;
                    isset($matches[4]) and $filename = $matches[4]; 

                    switch ($name) {
                        case 'userfile':
                             file_put_contents($filename, $body);
                             break;
                        default: 
                             $data[$name] = substr($body, 0, strlen($body) - 2);
                             break;
                    } 
                }

            }

            $new_array = array();
            $arrayObject = $data;
            foreach($arrayObject as $key=>$data) {
                if(is_array($data)) {
                    displayRecursiveResults($data);
                } elseif(is_object($data)) {
                    displayRecursiveResults($data);
                } else {
                    $key = str_replace("myform['",'',$key);
                    $key = str_replace("'];",'',$key);

                    $new_array[$key] = $data;
                }
            }

            $data = $new_array;
            $array1 = $data;
            $array2 = array('API_KEY'=>$this->config->api_key());
            $api_pass = '';

            $intersect = array_intersect( $array1, $array2 );

            if(!empty($intersect)){
                if(!empty($array1)){
                    $skip = array_shift($data);


                    // build in 
                    $statement = '';
                    $arrayObject = $data;
                    foreach($arrayObject as $key=>$data) {
                        if(is_array($data)) {
                            displayRecursiveResults($data);
                        } elseif(is_object($data)) {
                            displayRecursiveResults($data);
                        } else {

                            $key = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $key);
                            $data = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $data);

                            $statement .= $key."='".$data."',";
                        }
                    }

                    $link = $_SERVER['PHP_SELF'];
                    $link_array = explode('/',$link);
                    $get_id = end($link_array);

                    if(empty($get_id)){
                        echo json_encode(array( 'response'=>'method not valid..' ));
                    } else {
                        if(!empty($statement)){
                            $statement = rtrim($statement, ",");

                            $sql = "UPDATE $table SET ".$statement." WHERE id=".$get_id."";
                            $this->security->clean($sql);

                            $conn = $this->config->database();
                            if ($conn->query($sql) === TRUE) {
                                echo json_encode(array( 'response'=>'success updated..' ));
                            } else {
                                echo json_encode(array( 'response'=>'data post not valid..' ));
                            }
                        }
                    }

                } else {
                    echo json_encode(array( 'response'=>'api key not valid..' ));
                }
            } else {
                echo json_encode(array( 'response'=>'api key not valid..' ));
            }
        }


        public function delete($table='')
        {
            if(empty($_GET['api_key'])){
                    echo json_encode(array( 'response'=>'api key not valid..' ));
            } else {
                $API_KEY = $_GET['api_key'];
                if($API_KEY==$this->config->api_key()){
                    $link = $_SERVER['PHP_SELF'];
                    $link_array = explode('/',$link);
                    $get_id = end($link_array);
                    if(empty($get_id)){
                        echo json_encode(array( 'response'=>'method not valid..' ));
                    } else {
                        if(is_numeric($get_id)){
                            $sql = "DELETE FROM $table WHERE id=".$get_id."";
                            $sql = $this->security->clean($sql);
                            $conn = $this->config->database();
                            if ($conn->query($sql) === TRUE) {
                                echo json_encode(array( 'response'=>'success deleted..' ));
                            } else {
                                echo json_encode(array( 'response'=>'data post not valid..' ));
                            }
                        } else {
                            echo json_encode(array( 'response'=>'data not valid..' ));
                        }
                    }
                } else {
                    echo json_encode(array( 'response'=>'data not valid..' ));
                }
            }
        }
    }