<?php

    /**
    * The home page controller
    */
    class YcomController
    {
        private $model;

        function __construct($model)
        {
            $this->model = $model;
        }

        public function sayWelcome()
        {
            return $this->model->welcomeMessage();
        }


        public function login($table)
        {
            header('Content-Type: application/json; charset=utf-8');
            header("Access-Control-Allow-Origin: *");

            $method = filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_ENCODED);
            if($method=='POST'){
                return $this->model->login($table);
            } else {
                echo json_encode(array( 'response'=>'method not valid..' ));
            }
        }


        public function register($table)
        {
            header('Content-Type: application/json; charset=utf-8');
            header("Access-Control-Allow-Origin: *");

            $method = filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_ENCODED);
            if($method=='POST'){
                return $this->model->register($table);
            } else {
                echo json_encode(array( 'response'=>'method not valid..' ));
            }
        }


        public function create_ticket($table)
        {
            header('Content-Type: application/json; charset=utf-8');
            header("Access-Control-Allow-Origin: *");

            $method = filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_ENCODED);
            if($method=='POST'){
                return $this->model->create_ticket($table);
            } else {
                echo json_encode(array( 'response'=>'method not valid..' ));
            }
        }



        public function create_boq($table)
        {
            header('Content-Type: application/json; charset=utf-8');
            header("Access-Control-Allow-Origin: *");

            $method = filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_ENCODED);
            if($method=='POST'){
                return $this->model->create_boq($table);
            } else {
                echo json_encode(array( 'response'=>'method not valid..' ));
            }
        }
        

    }