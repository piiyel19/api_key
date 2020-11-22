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

    }