<?php

    // On requière le fichier permettant d'accéder à la bonne variable GET ou POST
    require_once File::buildPath('config','Conf.php');
    // Le controlleur utilisateur est à importé automatiquement car il est
    // beaucoup utilisé. Si on ne l'importe pas et qu'on utilise une fonction qui vient
    // de ce fichier alors pas de message d'erreur sera affiché. D'où le fait
    // d'éviter une longue séance de débuggage en important directement ce fichier
    require_once File::buildPath('controller','ControllerUser.php');

    // Recherche du contrôleur
    if (Conf::isSet('controller')) {
        $controller = Conf::get('controller');
    }
    else {
        // Affectation d'un controlleur par défaut
        $controller = 'product';
    }

    // Recherche de l'action
    if (Conf::isSet('action')) {
        $action = Conf::get('action');
    }
    else {
        // Affectation d'une action par défaut
        $action = 'readAll';
    }
    
    // On converti la chaîne de caractère du controlleur en classe controlleur
    $controllerClass = 'Controller' . ucfirst($controller);

    // On récupère le lien du fichier du controlleur
    $controllerPath = File::buildPath('controller', $controllerClass . '.php');

    // On regarde si le fichier du controlleur existe
    if (!file_exists($controllerPath)) {
        echo 'Le controlleur ' . $controllerClass . ' n\'a pas été intégré. Le fichier du controlleur n\'existe pas.';
        exit(1);
    } else {
        // Le fichier existe. On l'importe donc
        require_once $controllerPath;
        // Et si la classe existe ou non
        if (!class_exists($controllerClass)) {
            // Le controlleur n'existe pas. On affiche une erreur
            echo 'Le controller ' . $controllerClass . ' n\'existe pas.';
            exit(1);
        } else {
            // On récupère les méthodes de la class en question (controlleur)
            $classMethods = get_class_methods($controllerClass);
            // Le controlleur existe
            // On regarde si le controlleur possède l'action recherchée
            if (in_array($action, $classMethods)) {
                // L'action existe. On appelle l'action du controlleur
                $controllerClass::$action();
            } else {
                // Le controlleur ne possède pas cette action
                $controllerErrorViewPath = File::buildPath('view', $controller, 'error.php');
                // On regarde si le controlleur possède une vue d'erreur
                if (!file_exists($controllerErrorViewPath)) {
                    // S'il n'en a pas on affiche un message commun
                    echo 'Le controlleur ' . $controllerClass . ' n\'a pas l\'action ' . $action . '.';
                    exit(1);
                } else {
                    // Si il en a une on l'affiche
                    require_once $controllerErrorViewPath;
                }
            }
        }
    }
