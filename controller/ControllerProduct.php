<?php

    require_once File::buildPath('model','ProductModel.php');
    require_once File::buildPath('controller','ControllerUser.php');

    class ControllerProduct {

        // L'objet 'object' qui permet d'effectuer des actions générales à partir de Modèle
        protected static $object = 'product';

        public static function readAll() {
            // On souhaite afficher la page principale de produits
            $view = 'accueil.php';
            // On affecte une valeur au titre
            $pageTitle = 'Accueil';
            // On requière notre vue
            require File::buildPath('view','view.php');
        }

        // Action qui permet de lire un produit
        public static function read()
        {
            $userLogin = Conf::get('userLogin');
            // On récupère le produit
            $user = UserModel::select($userLogin);
            // On souhaite afficher le détail de ce produit
            $view = 'detail.php';
            // On affecte une valeur au titre
            $pageTitle = $user->get('userFirstName') . $user->get('userLastName');
            // On requière notre vue
            require File::buildPath('view', 'view.php');
        }

        // Action pour ajouter un produit
        public static function create(){
            if(Session::isAdmin()) {
                // Le titre de la page
                $pageTitle = "Ajout d'un produit";
                // La vue désirée
                $view = 'create.php';
                // On requière notre vue
                require File::buildPath('view','view.php');
            } else {
                ControllerUser::eligibilityError();
            }
        }

    }

?>