<?php

   

    /**
    * The home page view
    */
    class IndexView
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
        
        public function action()
        {
            return $this->controller->takeAction();
        }


        public function test_template()
        {
            $view = $this->config->view();
            $this->template->assign('username','Terry');
            $this->template->assign('age','26');
            $this->template->render($view.'myTemplate.html');
        }

        

    }