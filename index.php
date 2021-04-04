<?php
// Début de la session
session_start();
// On récupère la classe File qui nous permet
// De toujours avoir un chemin de fichier fixe
$DS = DIRECTORY_SEPARATOR;
$ROOT_FOLDER = __DIR__ . $DS . "lib" . $DS . "File.php";

require_once "$ROOT_FOLDER";
// On requière la classe qui enregistre la session
require_once File::buildPath('lib', 'Session.php');
// On requière ensuite le Routeur
require_once File::buildPath('controller', 'Router.php');
