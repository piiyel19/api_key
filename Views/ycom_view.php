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

            
        /* LOGIN */
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
        /* END */



        /* REGISTER */
        public function register()
        {
            $table = 'profile';
            return $this->controller->register($table);
        }
        /* END */




        /* TICKET */
        function form_ticket()
        {
            $base = $this->config->url();
            $route = '/ycom/create_ticket';
            return $this->view->templates('form_ticket', array('url' => $base.$route));
        }

        function create_ticket()
        {
            $table = 'ticket';
            //var_dump($_FILES); exit();
            //var_dump($_FILES['userfile']["name"]); exit();
            return $this->controller->create_ticket($table);
        }



        function create_boq()
        {
            $table = 'boq';
            return $this->controller->create_boq($table);
        }



        function view_image()
        {
            $conn = $this->config->database();
            $sql = "select userfile from ticket";
            $result = mysqli_query($conn,$sql);
            $row = mysqli_fetch_array($result);

            $image = $row['userfile'];
            $image_src = "upload/".$image;

            echo '<img src="'.$image.'">';
        }
        /* END */
    }