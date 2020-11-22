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
            $method = filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_ENCODED);
            if($method=='POST'){
                return $this->model->login($table);
            } else {
                echo json_encode(array( 'response'=>'method not valid..' ));
            }
        }

    }