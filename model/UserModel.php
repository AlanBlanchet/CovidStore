<?php

require_once File::buildPath('model', 'Model.php');

class UserModel extends Model
{
    protected static $object = 'user';
    protected static $primary = 'userLogin';

    protected $userLogin;
    protected $userMail;
    protected $userFirstName;
    protected $userLastName;
    protected $userPassword;
    protected $userRole;
    protected $userNonce;

    public static function checkPassword($login, $password){
        try {
            // On récupère le nom de la table à partir de l'objet static 'object'
            $tableName = ucfirst(static::$object);
            // On récupère le nom de la classe du modèle
            $className = $tableName . 'Model';
            // Nom de la table avec préfix
            $tableName = Conf::getDatabasePrefix() . $tableName;
            // La requete à effectuer
            $sql = "SELECT COUNT(*) AS 'valid' FROM $tableName WHERE userLogin=:login AND userPassword=:password";
            // On prépare la requête puisque l'utilisateur est susceptible
            // d'effectuer une injection SQL.
            $req = Model::$pdo->prepare($sql);
            // On construit notre tableau de valeurs à remplacer dans la requête
            $values = array(
                'login' => $login,
                'password' => Security::encode($password)
            );
            // On exécute la requête avec le tableau
            $req->execute($values);
            // On récupère la valeur de retour
            $res = $req->fetch()[0];
            if ($res == 1) {
                UserModel::storeRole($login);
                return $login;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            if(Conf::getDebug()) echo $e->getMessage();
        }
        return null;
    }

    public static function storeRole($login) {
        try {
            // Nom de la table avec préfix
            $tableName = Conf::getDatabasePrefix() . ucfirst(static::$object);
            // Nom de la table des roles
            $roleTableName = Conf::getDatabasePrefix() . 'UserRole';
            // La requete à effectuer
            $sql = "SELECT u.userRole FROM $tableName u JOIN $roleTableName r ON u.userRole=r.userRole WHERE userLogin=:login;";
            // On prépare la requête puisque l'utilisateur est susceptible
            // d'effectuer une injection SQL.
            $req = Model::$pdo->prepare($sql);
            // On construit notre tableau de valeurs à remplacer dans la requête
            $values = array(
                'login' => $login
            );
            // On exécute la requête avec le tableau
            $req->execute($values);
            // On récupère la valeur de retour
            $_SESSION['role'] = $req->fetch()[0];
        } catch (PDOException $e) {
            if(Conf::getDebug()) echo $e->getMessage();
            die();
        }
    }

    static function setNullNonce($login){
        try{
             $sql = "UPDATE utilisateur SET nonce=NULL WHERE login= BINARY :login";
             // Préparation de la requête
             $req_prep = Model::$pdo->prepare($sql);
         
             $values = array(
                 "login" => $login,
                 //nomdutag => valeur, ...
             );
             // On donne les valeurs et on exécute la requête	 
             $req_prep->execute($values);
         }catch(Exception $e){
               if (Conf::getDebug()) {
                     echo $e->getMessage(); // affiche un message d'erreur
                 } else {
                     echo 'Une erreur est survenue <a href=""> retour a la page d\'accueil </a>';
                 }
                 die();
         }
       }

    public function save() {
        $return = parent::save();
        UserModel::storeRole($this->get('userLogin'));
        return $return;
    }

    public function __construct($data = array())
    {
        foreach ($data as $key => $val) {
            $this->set($key, $val);
        }
    }

    public function get($attrib)
    {
        return $this->$attrib;
    }

    public function set($attrib, $val)
    {
        if ($attrib == "userPassword") {
            $this->$attrib = Security::encode($val);
        } else {
            $this->$attrib = $val;
        }
    }
}
