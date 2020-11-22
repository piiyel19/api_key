<?php

   

    /**
    * The home page view
    */
    class YcomView
    {

        private $model;
        private $controller;

        protected $view;
        protected $config;
        protected $query;

        protected $template;

        function __construct($controller, $model)
        {
            $this->controller = $controller;

            $this->model = $model;
            
            $this->view = new View();
            $this->config = new Config();
            $this->query = new Query();
            $this->template = new Template();
            $this->migrate = new Migration();
        }

        public function index()
        {
            $base = $this->config->url();
            $route = '/about/submit_form';
            return $this->view->templates('welcome', array('url' => $base.$route));
            //return $this->controller->sayWelcome();
        }

        
        public function login()
        {
            $table = 'login';
            return $this->controller->login($table);
        }

        public function check_session()
        {
            session_start();
            //var_dump($_SESSION['PHPSESSID']);
            if(empty($_SESSION['session'])){
                echo 'Failed No Sesion Valid';
            } else {
                return $_SESSION['session'];
                
            }
        }

        public function destroy()
        {
            session_start();
            session_destroy();
        }
    }