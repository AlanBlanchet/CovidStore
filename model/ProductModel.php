<?php

    require_once File::buildPath('model','Model.php');

    class ProductModel extends Model {
        
        protected static $object = 'product';
        protected static $primary = 'userLogin';

        protected $userLogin;
        protected $userFirstName;
        protected $userLastName;
        protected $userPassword;

    }

?>