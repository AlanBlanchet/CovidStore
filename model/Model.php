<?php

// On importe le fichier de configuration pour faire la liaison avec la BD
require_once File::buildPath("config", "Conf.php");
// On récupère le fichier qui permet de sécuriser certaines données (mdp)
require_once File::buildPath('lib', 'Security.php');

class Model
{
    // L'objet pdo qui nous permet d'accéder aux fonctionnalitées des requêtes
    public static $pdo;

    // Méthode qui retourne tous les objets issue de la table
    public static function selectAll()
    {
        try {
            // On récupère le nom de la table à partir de l'objet static 'object'
            $tableName = ucfirst(static::$object);
            // On récupère le nom de la classe du modèle
            $className = $tableName . 'Model';
            // Nom de la table avec préfix
            $tableName = Conf::getDatabasePrefix() . $tableName;
            // On effectue notre requête sans préparer puisque l'utilisateur
            // ne rentre aucune donnée dedans
            $req = Model::$pdo->query("SELECT * FROM $tableName");
            // On change le mode fetch pour pouvoir directement avoir
            // le résultat sous forme d'objet du modèle voulu
            $req->setFetchMode(PDO::FETCH_CLASS, $className);
            // On récupère les objets
            return $req->fetchAll();
        } catch (Exception $e) {
            if(Conf::getDebug()) echo $e->getMessage();
            // Si il y a eu une erreur on retourne un tableau vide
            return array();
        }
    }

    public static function select($primaryValue)
    {
        try {
            // On récupère le nom de la table à partir de l'objet static 'object'
            $tableName = ucfirst(static::$object);
            // On récupère le nom de la classe du modèle
            $className = $tableName . 'Model';
            // On récupère le nom de la clé primaire du modèle en question
            $primaryKey = static::$primary;
            // Nom de la table avec préfix
            $tableName = Conf::getDatabasePrefix() . $tableName;
            // La requete à effectuer
            $sql = "SELECT * from $tableName WHERE $primaryKey=:primary";
            // On prépare la requête puisque l'utilisateur est susceptible
            // d'effectuer une injection SQL.
            $req = Model::$pdo->prepare($sql);
            // On construit notre tableau de valeurs à remplacer dans la requête
            $values = array(
                "primary" => $primaryValue,
            );
            // On exécute la requête avec le tableau
            $req->execute($values);
            // On change le mode fetch pour pouvoir directement avoir
            // le résultat sous forme d'objet du modèle voulu
            $req->setFetchMode(PDO::FETCH_CLASS, $className);
            // On récupère les objets dans une variable
            $res = $req->fetchAll();
            if (empty($res)) {
                return null;
            }
            return $res[0];
        } catch (Exception $e) {
            // Si il y a eu une erreur on retourne null
            if(Conf::getDebug()) echo $e->getMessage();
            return null;
        }
    }

    public static function delete($primaryValue)
    {
        try {
            // On récupère le nom de la table à partir de l'objet static 'object'
            $tableName = ucfirst(static::$object);
            // On récupère le nom de la clé primaire du modèle en question
            $primaryKey = static::$primary;
            // Nom de la table avec préfix
            $tableName = Conf::getDatabasePrefix() . $tableName;
            // La requete à effectuer
            $sql = "DELETE FROM $tableName WHERE $primaryKey=:primary;";
            // On prépare la requête puisque l'utilisateur est susceptible
            // d'effectuer une injection SQL.
            $req = Model::$pdo->prepare($sql);
            // On construit notre tableau de valeurs à remplacer dans la requête
            $values = array(
              'primary' => $primaryValue
            );
            // On exécute la requête avec le tableau
            $req->execute($values);
            return true;
        } catch (PDOException $e) {
            if(Conf::getDebug()) echo $e->getMessage();
            return false;
        }
    }

    public static function update($data)
    {
        try {
            // On récupère le nom de la table à partir de l'objet static 'object'
            $tableName = ucfirst(static::$object);
            // On récupère le nom de la clé primaire du modèle en question
            $primaryKey = static::$primary;
            // On récupère la valeur de la clé primaire du modèle en question
            $primaryValue = $data[$primaryKey];
            // Nom de la table avec prefix
            $tableName = Conf::getDatabasePrefix() . $tableName;
            // On initaliser une variable de type string à ''
            $sets = '';
            // On récupère toute les valeurs du tableau qui servent à savoir
            // quel attribut il faudra changer
            foreach ($data as $key => $value) {
                $sets .= $key . '=:' . $key . ',';
            }
            // On coupe la dernière ','
            $sets = rtrim($sets, ',');
            // La requete à effectuer
            $sql = "UPDATE $tableName SET $sets WHERE $primaryKey='$primaryValue';";
            // On prépare la requête puisque l'utilisateur est susceptible
            // d'effectuer une injection SQL.
            $req = Model::$pdo->prepare($sql);
            // On exécute la requête avec le tableau
            $req->execute($data);
            return true;
        } catch (PDOException $e) {
            if(Conf::getDebug()) echo $e->getMessage();
            return false;
        }
    }

    public function save()
    {
        try {
            // On récupère le nom de la table à partir de l'objet static 'object' et du préfixe
            $tableName = Conf::getDatabasePrefix() . ucfirst(static::$object);
            // On récupère la valeur des attributs
            $data = get_object_vars($this);
            // On initaliser une variable de type string à ''
            $keys = '';
            // Celle-ci permet de récupérer juste les valeurs
            $values = '';
            // On récupère toute les valeurs du tableau qui servent à savoir
            // quel attribut il faudra changer
            foreach ($data as $key => $value) {
                $keys .= $key . ',';
                $values .= ':' . $key . ',';
            }
            // On coupe la dernière ','
            $values = rtrim($values, ',');
            $keys = rtrim($keys, ',');
            // La requete à effectuer
            $sql = "INSERT INTO $tableName ($keys) VALUES ($values);";
            // On prépare la requête puisque l'utilisateur est susceptible
            // d'effectuer une injection SQL.
            $req = Model::$pdo->prepare($sql);
            // On exécute la requête avec le
            $req->execute($data);
            return 0;
        } catch (PDOException $e) {
            if(Conf::getDebug()) echo $e->getMessage();
            return $e->getCode();
        }
    }

    public static function init()
    {
        // L'URL de l'emplacement de la BD
        $hostname = Conf::getHostname();
        // Le nom de la BD
        $databaseName = Conf::getDatabase();
        // Le login de la BD
        $login = Conf::getLogin();
        // Le mdp de la BD
        $password = Conf::getPassword();

        try {
            // Création de l'objet pdo qui sera utilisé pour effectuer les requêtes
            self::$pdo = new PDO(
                "mysql:host=$hostname;dbname=$databaseName",
                $login,
                $password,
                array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
            );
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            if (Conf::getDebug()) {
                echo $e->getMessage();
            } else {
                echo 'Une erreur est survenue <a href=""> retour a la page d\'accueil </a>';
            }
            die();
        }
    }
}

// Initialisation de la BD
Model::init();
