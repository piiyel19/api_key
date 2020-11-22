<?php
    /**
    * The home page view
    */
    class ApiView
    {

        private $model;
        private $controller;

        protected $view;
        protected $config;
        protected $query;

        function __construct($controller, $model)
        {
            $this->controller = $controller;

            $this->model = $model;
            
            $this->view = new View();
            $this->config = new Config();
            $this->query = new Query();
        }

        public function getting_start()
        {
            $base = $this->config->url();
            $route = '/about/submit_form';
            return $this->view->templates('api', array('url' => $base.$route));
            
        }
        
        public function get()
        {
            $table = 'test';
            return $this->controller->get($table);
        }

        public function post()
        {
            $table = 'test';
            return $this->controller->post($table);
        }

        public function put()
        {
            $table = 'test';
            return $this->controller->put($table);
        }

        public function delete()
        {
            $table = 'test';
            return $this->controller->delete($table);
        }



        public function login_api()
        {
            return $this->controller->login_api();
        }
    }