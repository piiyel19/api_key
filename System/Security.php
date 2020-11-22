<?php if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) { die('Access denied'); };
    class Security{
    	public function clean($data)
        {
            $data = htmlspecialchars($data);
            $data = stripslashes($data);
            $data = trim($data);
            return $data;
        }
    }