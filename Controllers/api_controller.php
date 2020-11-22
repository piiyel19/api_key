<?php

    class ApiController
    {
        private $modelObj;

        protected $config;
        protected $query;

        function __construct( $model )
        {
            $this->modelObj = $model;

            $this->config = new Config();
            $this->query = new Query();
        }


        public function get($table='')
        {
            $method = filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_ENCODED);
            if($method=='GET'){
                if(empty($_GET['api_key'])){
                        echo json_encode(array( 'response'=>'api key not valid..' ));
                } else {
                    return $this->modelObj->get($table);
                }
            } else {
                echo json_encode(array( 'response'=>'method not valid..' ));
            }
        }


        public function post($table='')
        {
            $method = filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_ENCODED);
            if($method=='POST'){
                return $this->modelObj->post($table);
            } else {
                echo json_encode(array( 'response'=>'method not valid..' ));
            }
        }

        public function put($table='')
        {
            $method = filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_ENCODED);
            if($method=='PUT'){
                return $this->modelObj->put($table);
            } else {
                echo json_encode(array( 'response'=>'method not valid..' ));
            }
        }


        public function delete($table='')
        {
            $method = filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_ENCODED);
            if($method=='DELETE'){
                if(empty($_GET['api_key'])){
                        echo json_encode(array( 'response'=>'api key not valid..' ));
                } else {
                    return $this->modelObj->delete($table);
                }
            } else {
                echo json_encode(array( 'response'=>'method not valid..' ));
            }
        }

        public function login_api()
        {
            $username = $_POST['username'];
            $password = $_POST['password'];
            if(($username=='admin')||($username=='1234')){
                return json_encode(array( 'response'=>'Success','api_key'=>'1234' ));
            } else {
                return json_encode(array( 'response'=>'Failed' ));
            }
        }


     }