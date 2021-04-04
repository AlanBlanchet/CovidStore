<?php

// Classe permettant de suivre la session de l'utilisateur
class Session
{
    // Vérification de si un utilisateur est bien celui qui est dans la session
    public static function isUser($login)
    {
        return (Session::isConnected() && ($_SESSION['login'] == $login));
    }

    // On change l'utilisateur de la session
    public static function setUser($login)
    {
        $_SESSION['login'] = $login;
    }

    // Vérification du l'utilisateur est un client ou pas
    public static function isClient() {
        return Session::isConnected() && !empty($_SESSION['role']) && $_SESSION['role'] == 'client';
    }

    // On vérifie si l'utilisateur est un administrateur ou pas
    public static function isAdmin()
    {
        return Session::isConnected() && !empty($_SESSION['role']) && $_SESSION['role'] == 'admin';
    }

    // On récupère l'utilisateur qui est dans la session (juste son login)
    public static function getUser()
    {
        return $_SESSION['login'];
    }

    // On vérifie si l'utilisateur est connecté ou non
    public static function isConnected()
    {
        return !empty($_SESSION['login']);
    }

    // On déconnecte l'utilisateur
    public static function disconnect()
    {
        // On vide le tableau de session
        session_unset();
        // On supprime les données sur le disque du serveur
        session_destroy();
        // On demande à l'utilisateur de supprimer son cookie associé (pas de garantie)
        setcookie(session_name(), '', time() - 1);
    }
}
