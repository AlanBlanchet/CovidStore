<?php

require_once File::buildPath('config','Credentials.php');

class Conf
{
   
    private static $debug = false;

    private static $databasePrefix = 'cov__';

    private static $databases = array(
        'hostname' => $hostname,
        'database' => $database,
        'login' => $login,
        'password' => $password
    );
   
    public static function getLogin()
    {
        //en PHP l'indice d'un tableau n'est pas forcement un chiffre.
        return self::$databases['login'];
    }

    public static function getHostname()
    {
        //en PHP l'indice d'un tableau n'est pas forcement un chiffre.
        return self::$databases['hostname'];
    }

    public static function getDatabase()
    {
        //en PHP l'indice d'un tableau n'est pas forcement un chiffre.
        return self::$databases['database'];
    }

    public static function getPassword()
    {
        //en PHP l'indice d'un tableau n'est pas forcement un chiffre.
        return self::$databases['password'];
    }

    public static function getDebug()
    {
        return self::$debug;
    }

    public static function echoFormMethod() {
        echo self::$debug ? 'get' : 'post';
    }

    public static function isSet($param) {
        if(is_null(Conf::get($param))) return false;
        return true;
    }

    public static function get($param) {
        if (isset($_POST[$param])){
            return $_POST[$param];
        } else if (isset($_GET[$param])){
            return $_GET[$param];
        } else {
            return null;
        }
    }

    public static function getDatabasePrefix()
    {
        return Conf::$databasePrefix;
    }
}
