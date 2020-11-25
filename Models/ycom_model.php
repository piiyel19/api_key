<?php
    class YcomModel
    {

        private $message = 'Welcome to Home page.';

        function __construct()
        {

            $this->config = new Config();
            $this->query = new Query();
            $this->security = new Security();
        }


        /* LOGIN */
        public function login($table)
        {   

            //var_dump($_POST); exit();
            if(!empty($_POST['myform'])){
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

                        $columns = explode(',', $columns);
                        

                        $escaped_values = array_map('mysql_real_escape_string',  array_values($values));

                        $i=0;
                        foreach ($escaped_values as $idx=>$data){
                            if($i=='0'){
                                $username = $data;
                            } else {
                                $password = $data;
                            }
                            $i++;
                        }
                        
                        
                        $myArray = array();

                        $count = $this->query->count_login($username,$password);
                        //var_dump($count);
                        if($count>0){
                            $data = $this->query->profile($username);

                            // create session
                            $session = $this->create_session($data);

                            $myArray[] =$data;

                            $myArray[] =array("session"=>$session);
                            //$myArray = array_push($session,$myArray);

                            //var_dump($session); exit();
                            echo json_encode($myArray);
                        } else {
                            echo json_encode(array( 'response'=>'data not exist..' ));
                        }



                        
                    } else {
                        echo json_encode(array( 'response'=>'data post not valid..' ));
                    }
                } else {
                    echo json_encode(array( 'response'=>'api key not valid..' ));
                }
            } else {
                    echo json_encode(array( 'response'=>'method post not valid..' ));
            }
        }


        function create_session($data)
        {
            session_start();

            $username = $data['username'];
            $id_user = $data['id_user'];

            $token = $this->generate_session($username,$id_user);

            return $token;
        }


        function generate_session($username,$id_user)
        {
            //this method using header request
            $string = $_SERVER['HTTP_USER_AGENT'];
            $string .= 'SHIFLETT';

            $header = md5($string);
            $user = md5($username.$id_user);
            // or method generate 
            $token = md5(uniqid(rand(), TRUE));

            $session = $header.$user.$token;

            return $session;
        }
        /* END */


        /* REGISTER */
        public function register($table)
        {   

            //var_dump($_POST); exit();
            if(!empty($_POST['myform'])){
                $array1 = $_POST['myform'];
                $array2 = array('API_KEY'=>$this->config->api_key());
                $api_pass = '';

                $intersect = array_intersect( $array1, $array2 );

                if(!empty($intersect)){
                    if(!empty($_POST['myform'])){
                        $values = $_POST['myform'];


                        if (
                            array_key_exists("'API_KEY'",$values) && array_key_exists("'company'",$values) && array_key_exists("'first_name'",$values) && array_key_exists("'last_name'",$values) && array_key_exists("'email'",$values) && array_key_exists("'password'",$values) && array_key_exists("'phone_number'",$values) && array_key_exists("'role'",$values) && array_key_exists("'location'",$values) && array_key_exists("'city'",$values) && array_key_exists("'state'",$values) && array_key_exists("'postcode'",$values) && array_key_exists("'country'",$values) && array_key_exists("'username'",$values)
                           ) 
                        {
                            $skip = array_shift($values);
                            
                            // Push id_user random
                            $values["'id_user'"] = rand();

                            $username = $values["'username'"];
                            $password = $values["'password'"];


                            // remove password from array
                            foreach($values as $key => $value){
                                //var_dump($key);
                                if("'password'"==$key)
                                    unset($values[$key]);
                            }

                            // var_dump($values); 
                            // exit();

                            $columns = implode(", ",array_keys($values));
                            $columns = str_replace("'", '', $columns);
                            $columns = str_replace('"', '', $columns);

                            
                            $count = $this->query->count_login($username,$password);
                            if($count>0){

                                echo json_encode(array( 'response'=>'User is already account' ));
                                exit();
                            }




                            //exit();
                            $escaped_values = array_map('mysql_real_escape_string',  array_values($values));

                            foreach ($escaped_values as $idx=>$data) $escaped_values[$idx] = "'".$data."'";
                            $values  = implode(", ", $escaped_values);
                            $sql = "INSERT INTO $table ($columns) VALUES ($values)";
                            $sql = $this->security->clean($sql);

                            //var_dump($sql); exit();
                            
                            $conn = $this->config->database();
                            $sql = $this->security->clean($sql);
                            if (mysqli_query($conn, $sql)) {

                                // created to login form
                                $this->create_login($username,$password);


                                
                            } else {
                                echo json_encode(array( 'response'=>'Failed' ));
                            }

                        }
                        else
                        {
                            echo json_encode(array( 'response'=>'Failed' ));
                        }


                        
                    } else {
                        echo json_encode(array( 'response'=>'data post not valid..' ));
                    }
                } else {
                    echo json_encode(array( 'response'=>'api key not valid..' ));
                }
            } else {
                    echo json_encode(array( 'response'=>'method post not valid..' ));
            }
        }


        function create_login($username,$password)
        {
            $sql_login = "INSERT INTO login (username,password) VALUES ('$username','$password')";
            $sql_login = $this->security->clean($sql_login);
            //var_dump($sql_login); exit();
            $conn = $this->config->database();
            $sql_login = $this->security->clean($sql_login);
            if (mysqli_query($conn, $sql_login)) {
                echo json_encode(array( 'response'=>'Success' ));
            } else {
                echo json_encode(array( 'response'=>'Failed' ));
            }
        }
        /* END */


        /* CREATE TICKET */
        function create_ticket($table)
        {
            $base = $this->config->base_url();
            $conn = $this->config->database();

            if(!empty($_POST['myform'])){
                $array1 = $_POST['myform'];
                $array2 = array('API_KEY'=>$this->config->api_key());
                $api_pass = '';

                $intersect = array_intersect( $array1, $array2 );



                $image = '';
                if(!empty($intersect)){
                    if(!empty($_POST['myform'])){
                        $values = $_POST['myform'];

                        if(!empty($values["'id_user'"])){

                            $id_user = $values["'id_user'"];
                            $count = $this->query->check_user($id_user);

                            if($count>0){

                                if(!empty($_FILES['userfile']["name"])){
                                    $name = $_FILES['userfile']['name'];
                                    $target_dir = "upload/";
                                    $target_file = $target_dir . basename($_FILES["userfile"]["name"]);



                                    // Select file type
                                    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

                                    // Valid file extensions
                                    $extensions_arr = array("jpg","jpeg","png","gif");

                                    // Check extension
                                    if( in_array($imageFileType,$extensions_arr) ){

                                        // Convert to base64 
                                        $image_base64 = base64_encode(file_get_contents($_FILES['userfile']['tmp_name']) );
                                        $image = 'data:image/'.$imageFileType.';base64,'.$image_base64;
                                        // Insert record
                                        // $query = "insert into images(name,image) values('".$target_file."','".$image."')";
                                        // mysqli_query($conn,$query);

                                        // Upload file
                                        move_uploaded_file($_FILES['userfile']['tmp_name'],$target_dir.$name);


                                        $target_file = $base.$target_file;
                                        $imgLocation = $this->get_image_location($target_file);
                                        $imgLat = $imgLocation['latitude'];
                                        $imgLng = $imgLocation['longitude'];
                                        
                                        if((empty($values["'latitude'"]))&&(empty($values["'latitude'"]))){
                                            echo json_encode(array( 'response'=>'please open your current location..' ));
                                            exit();
                                        }

                                        $lat = $values["'latitude'"];
                                        $long = $values["'longitude'"];


                                        if((!empty($lat))&&(!empty($long))){

                                            if((!empty($imgLat))&&(!empty($imgLng))){


                                                // check variable should have
                                                // echo '<pre>';
                                                // var_dump($values);
                                                // echo '</pre>';
                                                if (
                                                    array_key_exists("'API_KEY'",$values) && array_key_exists("'id_user'",$values) && array_key_exists("'task_id'",$values) && array_key_exists("'source_ticket'",$values) && array_key_exists("'site_name'",$values) && array_key_exists("'location_id'",$values) && array_key_exists("'severity'",$values) && array_key_exists("'category'",$values) && array_key_exists("'type_of_services'",$values) && array_key_exists("'occurrence_time'",$values) && array_key_exists("'element'",$values) && array_key_exists("'service_impact'",$values) && array_key_exists("'site_access'",$values) && array_key_exists("'alarm_name'",$values) && array_key_exists("'district'",$values) && array_key_exists("'territtory'",$values) && array_key_exists("'collector_or_end_site'",$values) && array_key_exists("'assigned_to'",$values) && array_key_exists("'status'",$values) && array_key_exists("'status'",$values) 
                                                   ) 
                                                {
                                                    $values['image_latitude'] = $imgLat;
                                                    $values['image_longitude'] = $imgLng;

                                                    $values['userfile']=$image;
                                                    $values['id_ticket ']=rand();

                                                    $skip = array_shift($values);
                                                    $columns = implode(", ",array_keys($values));
                                                    $columns = str_replace("'", '', $columns);
                                                    $columns = str_replace('"', '', $columns);

                                                    $escaped_values = array_map('mysql_real_escape_string',  array_values($values));

                                                    foreach ($escaped_values as $idx=>$data) $escaped_values[$idx] = "'".$data."'";
                                                    $values  = implode(", ", $escaped_values);
                                                    $sql = "INSERT INTO $table ($columns) VALUES ($values)";
                                                    $sql = $this->security->clean($sql);

                                                    //var_dump($sql); exit();
                                                    
                                                    $conn = $this->config->database();
                                                    $sql = $this->security->clean($sql);
                                                    if (mysqli_query($conn, $sql)) {
                                                        // created ticket
                                                        echo json_encode(array( 'response'=>'Success' ));                            
                                                    } else {
                                                        echo json_encode(array( 'response'=>'Failed' ));
                                                    }

                                                    //var_dump($values);
                                                } else {
                                                    echo json_encode(array( 'response'=>'Failed' ));
                                                }
                                                
                                            } else {
                                                echo json_encode(array( 'response'=>'we cannot defined your location image..' ));
                                            }
                                        } else {
                                            echo json_encode(array( 'response'=>'please open your current location..' ));
                                        }

                                    }
                                } else {
                                    echo json_encode(array( 'response'=>'we cannot defined your location image..' ));
                                    exit();
                                }
                            } else {
                                echo json_encode(array( 'response'=>'User not authorized..' ));
                            }
                        } else {
                            echo json_encode(array( 'response'=>'User not authorized..' ));
                        }

                        
                        
                    } else {
                       echo json_encode(array( 'response'=>'Failed' ));
                    }
                } else {
                    echo json_encode(array( 'response'=>'Failed' ));
                }
            } else {
                echo json_encode(array( 'response'=>'Failed' ));
            }
        }
        /* END */


        /* BOQ */
        function create_boq($table)
        {
            $base = $this->config->base_url();
            $conn = $this->config->database();

            //var_dump($_POST); exit();
            if(!empty($_POST['myform'])){
                $array1 = $_POST['myform'];
                $array2 = array('API_KEY'=>$this->config->api_key());
                $api_pass = '';

                $intersect = array_intersect( $array1, $array2 );



                $image = '';
                if(!empty($intersect)){
                    if(!empty($_POST['myform'])){
                        $values = $_POST['myform'];

                        if(!empty($values["'id_user'"])){

                            $id_user = $values["'id_user'"];
                            //var_dump($id_user); exit();
                            $count = $this->query->check_user($id_user);

                            if($count>0){
                        
                                //var_dump($values);
                                // check variable should have
                                if (array_key_exists("'API_KEY'",$values) && array_key_exists("'id_user'",$values) && array_key_exists("'from_destination'",$values) && array_key_exists("'subject'",$values) && array_key_exists("'ewpcm_number'",$values) && array_key_exists("'note'",$values) ) 
                                {

                                    $values['id_boq']=rand();

                                    $skip = array_shift($values);
                                    $columns = implode(", ",array_keys($values));
                                    $columns = str_replace("'", '', $columns);
                                    $columns = str_replace('"', '', $columns);

                                    $escaped_values = array_map('mysql_real_escape_string',  array_values($values));

                                    foreach ($escaped_values as $idx=>$data) $escaped_values[$idx] = "'".$data."'";
                                    $values  = implode(", ", $escaped_values);
                                    $sql = "INSERT INTO $table ($columns) VALUES ($values)";
                                    $sql = $this->security->clean($sql);

                                    //var_dump($sql); exit();
                                    
                                    $conn = $this->config->database();
                                    $sql = $this->security->clean($sql);
                                    if (mysqli_query($conn, $sql)) {
                                        // created ticket
                                        echo json_encode(array( 'response'=>'Success' ));                            
                                    } else {
                                        echo json_encode(array( 'response'=>'Failed' ));
                                    }


                                } else {
                                    echo json_encode(array( 'response'=>'User not authorized..' ));
                                }
                            } else {
                                echo json_encode(array( 'response'=>'User not authorized..' ));
                            }
                        } else {

                        }

                    } else {
                        echo json_encode(array( 'response'=>'data post not valid..' ));
                    }
                } else {
                    echo json_encode(array( 'response'=>'data post not valid..' ));
                }
            } else {
                echo json_encode(array( 'response'=>'Failed' ));
            }
        }



        function get_image_location($image = ''){
            
            $exif = exif_read_data($image, 0, true);
            if($exif && isset($exif['GPS'])){
                $GPSLatitudeRef = $exif['GPS']['GPSLatitudeRef'];
                $GPSLatitude    = $exif['GPS']['GPSLatitude'];
                $GPSLongitudeRef= $exif['GPS']['GPSLongitudeRef'];
                $GPSLongitude   = $exif['GPS']['GPSLongitude'];
                
                $lat_degrees = count($GPSLatitude) > 0 ? $this->gps2Num($GPSLatitude[0]) : 0;
                $lat_minutes = count($GPSLatitude) > 1 ? $this->gps2Num($GPSLatitude[1]) : 0;
                $lat_seconds = count($GPSLatitude) > 2 ? $this->gps2Num($GPSLatitude[2]) : 0;
                
                $lon_degrees = count($GPSLongitude) > 0 ? $this->gps2Num($GPSLongitude[0]) : 0;
                $lon_minutes = count($GPSLongitude) > 1 ? $this->gps2Num($GPSLongitude[1]) : 0;
                $lon_seconds = count($GPSLongitude) > 2 ? $this->gps2Num($GPSLongitude[2]) : 0;
                
                $lat_direction = ($GPSLatitudeRef == 'W' or $GPSLatitudeRef == 'S') ? -1 : 1;
                $lon_direction = ($GPSLongitudeRef == 'W' or $GPSLongitudeRef == 'S') ? -1 : 1;
                
                $latitude = $lat_direction * ($lat_degrees + ($lat_minutes / 60) + ($lat_seconds / (60*60)));
                $longitude = $lon_direction * ($lon_degrees + ($lon_minutes / 60) + ($lon_seconds / (60*60)));



                return array('latitude'=>$latitude, 'longitude'=>$longitude);
            }else{
                
                return false;
            }

           
        }


        function gps2Num($coordPart){
            $parts = explode('/', $coordPart);
            if(count($parts) <= 0)
            return 0;
            if(count($parts) == 1)
            return $parts[0];
            return floatval($parts[0]) / floatval($parts[1]);
        }
        /* END */

    }