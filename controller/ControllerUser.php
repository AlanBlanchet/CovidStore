<?php

// On récupère le modèle pour ce controlleur
require_once File::buildPath('model', 'UserModel.php');
// On utilise le controlleur des produits car c'est lui qui
// possède la page d'accueil du site
require_once File::buildPath('controller', 'ControllerProduct.php');

class ControllerUser
{
    // L'objet 'object' qui permet d'effectuer des actions générales à partir de Modèle
    protected static $object = 'user';

    // Action qui permet de lire un utilisateur
    public static function read()
    {
        if (Conf::isSet('userLogin')) {
            if (Session::isUser(Conf::get('userLogin')) || Session::isAdmin()) {
                if (Session::isUser(Conf::get('userLogin'))) {
                    ControllerUser::update();
                } else {
                    $userLogin = Conf::get('userLogin');
                    // On récupère l'utilisateur
                    $user = UserModel::select($userLogin);
                    if (is_null($user)) {
                        $returnLink = '?controller=user&action=showAdminPanel';
                        $returnMessage = 'Retourné dans le panneau administrateur';
                        $message = 'Le compte de login ' . $userLogin . ' n\'est pas enregistrée. Impossible de le lire.';
                        require File::buildPath('view', 'error.php');
                    } else {
                        // On souhaite afficher le détail de cet utilisateur
                        $view = 'profile.php';
                        // On affecte une valeur au titre
                        $pageTitle = $user->get('userFirstName') . ' ' . $user->get('userLastName');
                        // On requière notre vue
                        require File::buildPath('view', 'view.php');
                    }
                }
            } else {
                $message = 'Seul les admins ou un utilisater connectée peuvent lire des infos.';
                require File::buildPath('view', 'error.php');
            }
        } else {
            $message = 'Donnez un utilisateur à lire.';
            require File::buildPath('view', 'error.php');
        }
    }

    // Action qui permet de créer un compte
    public static function create()
    {
        if (!Session::isConnected()) {
            // On souhaite afficher un formulaire
            $view = 'update.php';
            // On affecte une valeur au titre
            $pageTitle = 'Création de compte';
            // On souhaite qu'à la fin de l'action on lance la nouvelle
            // action 'created'
            $formAction = 'created';
            // On souhaite que le premier champ soit obligatoire
            $requirement = 'required';
            // Le message du header
            $headMessage = 'Créer mon compte';
            // Le message du bouton valider
            $message = $headMessage;
            // On créer un nouvel objet
            $user = new UserModel();
            // On requière notre vue
            require File::buildPath('view', 'view.php');
        } else {
            $message = 'Vous êtes connecté. Déconnectez-vous pour créer un autre compte.';
            require File::buildPath('view', 'error.php');
        }
    }

    // On créé le compte, on affiche le profile de l'utilisateur
    public static function created()
    {
        if (!Session::isConnected()) {
            // Variables à définir en cas d'erreur
            $returnLink = '?controller=user&action=create';
            $returnMessage = 'Réssayer de créer un compte';
            // On regarde si les variables sont bien disponibles
            if (
                Conf::isSet('userLogin') &&
                Conf::isSet('userLastName') &&
                Conf::isSet('userFirstName') &&
                Conf::isSet('userPassword') &&
                Conf::isSet('confirmPassword') &&
                Conf::isSet('userMail')
            ) {

                if (Conf::get("userPassword") != Conf::get("confirmPassword")) {
                    $message = 'Mot de passe et sa confirmation non identique';
                    require File::buildPath('view', 'error.php');
                } else if (!filter_var(Conf::get("userMail"), FILTER_VALIDATE_EMAIL)) {
                    $message = 'Adresse Mail non valide';
                    require File::buildPath('view', 'error.php');
                } else {

                    // On les stoque
                    $login = Conf::get('userLogin');
                    $firstName = Conf::get('userFirstName');
                    $lastName = Conf::get('userLastName');
                    $password = Conf::get('userPassword');
                    $mail = Conf::get('userMail');
                    $nonce = Security::generateRandomHex();
                    // On créer un tableau avec ces valeurs
                    $arr = array(
                        'userLogin' => $login,
                        'userLastName' => $lastName,
                        'userFirstName' => $firstName,
                        'userPassword' => $password,
                        'userNonce' => $nonce,
                        'userMail' => $mail,
                        'userRole' => "client"
                    );
                    // On créer un modèle utilisateur à partir des données
                    $user = new UserModel($arr);

                    // On tente de sauvegarder notre utilisateur sur la bd
                    $saved = $user->save();
                    if ($saved == 23000) {
                        // Le compte existe déjà
                        $message = 'Le compte de login ' . $user->get('userLogin') . ' est déjà enregistrée';
                        require File::buildPath('view', 'error.php');
                    } elseif ($saved != 0) {
                        // Autre erreur
                        $message = 'Erreur lors de l\'insertion de données';
                        require File::buildPath('view', 'error.php');
                    } else {

                        $mailToSend = "Bonjour " . $firstName . ",<br>Nous vous invitons à vérifiez votre mail : <a href= \"localhost/index.php?action=validate&controller=utilisateur&login=" . $login . "&nonce=" . $nonce . "\"> appuyez ici</a> ";
                        mail(Conf::get("mail"), "Vérification de votre mail", $mailToSend);
                        $pageMessage = "Le compte a été créé avec succès";
                        ControllerUser::connect();
                    }
                }
            } else {
                $message = 'Vous n\'avez pas bien rempli le formulaire';
                require File::buildPath('view', 'error.php');
            }
        } else {
            $message = 'Vous êtes connecté. Déconnectez-vous pour créer un autre compte.';
            require File::buildPath('view', 'error.php');
        }
    }

    // Action pour supprimer un utilisateur
    public static function delete()
    {
        if ((Session::isUser(Conf::get('userLogin'))) || (Session::isAdmin())) {
            // On regarde si la variable est bien disponible
            if (Conf::isSet('userLogin')) {
                // On la stoque
                $userLogin = Conf::get('userLogin');
                if (is_null(UserModel::select($userLogin))) {
                    $returnLink = '?controller=user&action=showAdminPanel';
                    $returnMessage = 'Retourné dans le panneau administrateur';
                    $message = 'Le compte de login ' . $userLogin . ' n\'est pas enregistrée. Impossible de le supprimé.';
                    require File::buildPath('view', 'error.php');
                } else {
                    UserModel::delete($userLogin);
                    // On doit détruire la session
                    Session::disconnect();
                    // On souhaite montrer une page qui montre que l'utilisateur a bien été supprimé
                    $view = 'deleted.php';
                    // On affecte une valeur au titre
                    $pageTitle = 'Utilisateur supprimé';
                    // On requière notre vue
                    require File::buildPath('view', 'view.php');
                }
            } else {
                $returnLink = '?controller=user&action=showAdminPanel';
                $returnMessage = 'Retourné dans le panneau administrateur';
                $message = "Indiqué un compte a supprimer";
                require File::buildPath('view', 'error.php');
            }
        } else if (Session::isConnected()) {
            $message = "Vous n'êtes pas éligibles à supprimé un compte autre que vous.";
            require File::buildPath('view', 'error.php');
        } else {
            $returnLink = '?controller=user&action=connect';
            $returnMessage = 'Connectez-vous';
            $message = "Vous devez être au minima connectée pour supprimer un compte.";
            require File::buildPath('view', 'error.php');
        }
    }

    // Action pour mettre à jour l'utilisateur
    public static function update()
    {
        if (Session::isUser(Conf::get('userLogin'))) {
            // On montre un formulaire d'affichage
            $view = 'update.php';
            // On affecte une valeur au titre
            $pageTitle = 'Modifier votre compte';
            // On souhaite que l'action suivante soit 'updated'
            $formAction = 'updated';
            // Le premier champ du formulaire sera en lecture seule
            $requirement = 'readonly';
            // On récupère les données de l'utilisateur
            if (
                Conf::isSet('userLastName') &&
                Conf::isSet('userFirstName') &&
                Conf::isSet('userMail')
            ) {
                $userWithBadInfo = array(
                    'userLogin' => Conf::get("userLogin"),
                    'userLastName' => Conf::get("userLastName"),
                    'userFirstName' => Conf::get("userFirstName"),
                    'userMail' => Conf::get("userMail")
                );
                $user = new UserModel($userWithBadInfo);
            } else {
                $user = UserModel::select(Conf::get('userLogin'));
            }
            // Le message du header
            $headMessage = $user->get('userFirstName') . ' ' . $lastName = $user->get('userLastName');
            // Le message du bouton valider
            $message = 'Mettre à jour';
            require File::buildPath('view', 'view.php');
        } else {
            $message = "Vous n'êtes pas autorisés à modifier les données d'un autre utilisateur";
            require File::buildPath('view', 'error.php');
        }
    }

    public static function updatedRole()
    {
        if (Session::isAdmin()) {
            if (Conf::isSet('userLogin')) {
                $userLogin = Conf::get('userLogin');
                if (Conf::isSet('userRole')) {
                    $userRole = Conf::get('userRole');
                    $user = UserModel::select($userLogin);
                    if ($user->get('userRole') != "admin") {
                        $arr = array(
                            "userLogin" => $userLogin,
                            "userRole" => $userRole
                        );

                        if (!UserModel::update($arr)) {
                            $returnLink = '?controller=user&action=read&userLogin=' . $userLogin;
                            $returnMessage = 'Retourné lire l\'utilisateur';
                            $message = "Impossible de mettre à jour le compte de $userLogin";
                            require File::buildPath('view', 'error.php');
                        } else {
                            // Le message à afficher en haut de la page
                            $pageMessage = "Le compte a été mis à jour avec succès";
                            // On montre un formulaire d'affichage
                            $view = 'profile.php';
                            // On affecte une valeur au titre
                            $pageTitle = 'Modifier votre compte';
                            // On souhaite que l'action suivante soit 'updated'
                            $formAction = 'updated';
                            // Le premier champ du formulaire sera en lecture seule
                            $requirement = 'readonly';
                            // On récupère les données de l'utilisateur
                            $user = UserModel::select(Conf::get('userLogin'));
                            // Le message du header
                            $headMessage = $user->get('userFirstName') . ' ' . $lastName = $user->get('userLastName');
                            // Le message du bouton valider
                            $message = 'Mettre à jour';
                            require File::buildPath('view', 'view.php');
                        }
                    } else {
                        $returnLink = '?controller=user&action=read&userLogin=' . $userLogin;
                        $returnMessage = 'Retourné lire l\'utilisateur';
                        $message = "Impossible de modifier le role d'un admin.";
                        require File::buildPath('view', 'error.php');
                    }
                } else {
                    $returnLink = '?controller=user&action=read&userLogin=' . $userLogin;
                    $returnMessage = 'Retourné lire l\'utilisateur';
                    $message = "Veillez à renseigner un role.";
                    require File::buildPath('view', 'error.php');
                }
            } else {
                $returnLink = '?controller=user&action=showAdminPanel';
                $returnMessage = 'Retourné dans le panneau administrateur';
                $message = "Veillez à renseigner un login.";
                require File::buildPath('view', 'error.php');
            }
        } else {
            $message = "Vous n'êtes pas administrateur.";
            require File::buildPath('view', 'error.php');
        }
    }

    public static function updated()
    {
        if (Session::isUser(Conf::get('userLogin'))) {
            $login = Conf::get("userLogin");
            // On regarde si les variables sont bien disponibles
            if (
                Conf::isSet('userLastName') &&
                Conf::isSet('userFirstName') &&
                Conf::isSet('userPassword') &&
                Conf::isSet('confirmPassword') &&
                Conf::isSet('userMail')
            ) {
                $userLastName = Conf::get("userLastName");
                $userFirstName = Conf::get("userFirstName");
                $userMail = Conf::get("userMail");
                if (Conf::get("userPassword") != Conf::get("confirmPassword")) {

                    $returnLink = '?controller=user&action=update&userLastName=' . $userLastName . '&userMail=' . $userMail . '&userFirstName=' . $userFirstName . '&userLogin=' . $login;
                    $returnMessage = 'Retourné sur la page de modification';
                    $message = "Mot de passe et sa confirmation non identique";
                    require File::buildPath('view', 'error.php');
                } else if (!filter_var(Conf::get("userMail"), FILTER_VALIDATE_EMAIL)) {
                    $returnLink = '?controller=user&action=update&userLastName=' . $userLastName . '&userMail=' . $userMail . '&userFirstName=' . $userFirstName . '&userLogin=' . $login;
                    $returnMessage = 'Retourné sur la page de modification';
                    $message = "Mail non conforme";
                    require File::buildPath('view', 'error.php');
                } else {
                    // On les stoque et on créer un tableau
                    $login = Conf::get('userLogin');

                    $arr = array(
                        'userLogin' => $login
                    );
                    if (!Conf::get("userPasword") == "") { //si le password veut être changer
                        $password = Conf::get('userPassword');
                        $arr["userPassword"] = $password;
                    }
                    $arr["userFirstName"] = $userFirstName;
                    $arr["userLastName"] = $userLastName;
                    $arr["userMail"] = $userMail;
                    // On met à jour les valeurs
                    if (!UserModel::update($arr)) {
                        $returnLink = '?controller=user&action=update&userLogin=' . $login;
                        $returnMessage = 'Retourné dans votre compte';
                        $message = "Impossible de mettre à jour le compte de $login";
                        require File::buildPath('view', 'error.php');
                    } else {
                        // Le message à afficher en haut de la page
                        $pageMessage = "Votre compte a été mis à jour avec succès";
                        // On montre un formulaire d'affichage
                        $view = 'profile.php';
                        // On affecte une valeur au titre
                        $pageTitle = 'Modifier votre compte';
                        // On souhaite que l'action suivante soit 'updated'
                        $formAction = 'updated';
                        // Le premier champ du formulaire sera en lecture seule
                        $requirement = 'readonly';
                        // On récupère les données de l'utilisateur
                        $user = UserModel::select(Conf::get('userLogin'));
                        // Le message du header
                        $headMessage = $user->get('userFirstName') . ' ' . $lastName = $user->get('userLastName');
                        // Le message du bouton valider
                        $message = 'Mettre à jour';
                        require File::buildPath('view', 'view.php');
                    }
                }
            } else {
                $returnLink = '?controller=user&action=update&userLogin=' . $login;
                $returnMessage = 'Retourné dans votre compte';
                $message = 'Vous n\'avez pas bien rempli le formulaire.';
                require File::buildPath('view', 'error.php');
            }
        } else {
            $message = "Vous n'êtes pas autorisés à modifier les données d'un autre utilisateur";
            require File::buildPath('view', 'error.php');
        }
    }

    // Action pour se connecter
    public static function connect()
    {
        if (!Session::isConnected()) {
            // On montre un formulaire d'affichage
            $view = 'connect.php';
            // On affecte une valeur au titre
            $pageTitle = 'Connexion';
            // On souhaite que l'action suivante soit 'connected'
            $formAction = 'connected';
            // Le message qui sera dans le bouton de validation
            $message = 'Se connecter';
            // On requière notre vue
            require File::buildPath('view', 'view.php');
        } else {
            $message = "Vous êtes connecté. Déconnectez-vous pour vous connecter avec un autre compte";
            require File::buildPath('view', 'error.php');
        }
    }

    // Action une fois qu'on s'est connecté
    public static function connected()
    {
        if (!Session::isConnected()) {
            if (
                Conf::isSet('userLogin') &&
                Conf::isSet('userPassword')
            ) {
                $login = Conf::get('userLogin');
                $password = Conf::get('userPassword');
                // On regarde si le mot de passe est valide
                $validLogin = UserModel::checkPassword($login, $password);
                if ($validLogin != null) {
                    $u = UserModel::select($validLogin);
                    if ($u->get('userNonce') != NULL) {
                        $message = "validez votre email, correctement";
                        // Message d'erreur
                        // On requière la vue d'erreur
                        require File::buildPath('view', 'error.php');
                    } else {
                        // On stoque la session de l'utilisateur valide
                        Session::setUser($validLogin);
                        // On affiche la page de modification des données utilisateur
                        ControllerUser::update();
                    }
                } else {
                    // Le message d'erreur
                    $message = 'Identifiant / Mot de passe incorrecte';
                    // Le lien sur lequel l'utilisateur sera invité à cliquer
                    $returnLink = '?controller=user&action=connect';
                    // Le message du bouton de retour
                    $returnMessage = 'Réessayer de se connecter';
                    // On requière la vue d'erreur
                    require File::buildPath('view', 'error.php');
                }
            } else {
                $returnLink = '?controller=user&action=connect';
                $returnMessage = 'Réessayer de se connecter';
                // Message d'erreur si on ne récupère pas les bonnes données
                $message = 'Identifiant / Mot de passe manquant';
                require File::buildPath('view', 'error.php');
            }
        } else {
            $message = "Vous êtes connecté. Déconnectez-vous pour vous connecter avec un autre compte";
            require File::buildPath('view', 'error.php');
        }
    }

    // Action quand on se déconnecte
    public static function deconnect()
    {
        // Si on est connecté alors on se déconnecte
        if (Session::isConnected()) {
            Session::disconnect();
        }
        // On revient sur la page principale avec tous les produits
        ControllerProduct::readAll();
    }

    // Action pour visualiser le panneau dédié aux administrateurs
    public static function showAdminPanel()
    {
        // On regarde si l'utilisateur est un administrateur
        if (Session::isAdmin()) {
            // On récupère tous les utilisateurs
            $users = UserModel::selectAll();
            // On veut montrer le panneau d'administration
            $view = 'adminPanel.php';
            // On affecte une valeur au titre
            $pageTitle = "Panneau d'administration";
            // On requière notre vue
            require File::buildPath('view', 'view.php');
        } else {
            ControllerUser::eligibilityError();
        }
    }

    // Action pour visualiser les utilisateurs du site et éventuellement modifier leur rôle
    public static function readAll()
    {
        // On regarde si l'utilisateur est un administrateur
        if (Session::isAdmin()) {
            // On récupère tous les utilisateurs de la table
            $users = UserModel::selectAll();
            // On souhaite obtenir la vue sous forme de list
            $view = 'list.php';
            // On affecte une valeur au titre
            $pageTitle = 'Liste des utilisateurs';
            // On requière notre vue
            require File::buildPath('view', 'view.php');
        } else {
            ControllerUser::eligibilityError();
        }
    }

    // Action permettant d'envoyer un mail à un utilisateur
    public static function sendMail()
    {
        if (Session::isAdmin()) {
            // Si on est admin on est capable d'envoyer un mail à un utilisateur
            $view = 'mailSend.php';
            // La cible du mail
            $target = Conf::get('target');
            // Le titre de la page
            $pageTitle = "Mail à $target";
            // On requière notre vue
            require File::buildPath('view', 'view.php');
        } else {
            ControllerUser::eligibilityError();
        }
    }

    public static function sentMail()
    {
        if (Session::isAdmin()) {
            // Envoie du mail à l'utilisateur target
            $target = Conf::get('target');
            // Le message de confirmation de l'envoie du mail
            $pageMessage = "Mail envoyé à $target";
            // On revient sur le panneau d'administration
            // On récupère tous les utilisateurs
            $users = UserModel::selectAll();
            // On veut montrer le panneau d'administration
            $view = 'adminPanel.php';
            // On affecte une valeur au titre
            $pageTitle = "Mail envoyé";
            // On requière notre vue
            require File::buildPath('view', 'view.php');
        } else {
            ControllerUser::eligibilityError();
        }
    }

    // Erreur d'élilibité
    public static function eligibilityError()
    {
        // Message d'erreur
        $message = "Vous n'êtes pas éligibles à visualiser cette page";
        // On requière la vue d'erreur
        require File::buildPath('view', 'error.php');
    }

    public static function validate()
    {
        $login = Conf::get("login");
        $nonce = Conf::get("nonce");
        $u = UserModel::select($login);
        if ($u->getNonce() == $nonce) {
            UserModel::setNullNonce($login);
            $view = "connect";
            $pagetitle = "Connectez-vous";
            require File::buildpath(array('view', "view.php"));
        } else {
            $message = "validez votre email, correctement";
            // Message d'erreur
            // On requière la vue d'erreur
            require File::buildPath('view', 'error.php');
        }
    }
}
