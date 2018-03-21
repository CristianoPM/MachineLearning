<?php

//-------------------------------------------------------------------------------------------
// Examen à blanc du module M151 - Janvier 2018
// 
// Auteur      : Cristiano Pereira kiadz simcir
// Classe      : I-FA-P3B
// Date        : 2018/01/31
// Projet      : Jeu de NIM
// Description : Entraînement sur le machine learning

/**
 * Connexion à la base de données
 * @staticvar type $db
 * @return \PDO
 */
function myPdo() {
    static $db = NULL;
    try {
        if ($db == NULL) {
            $db = new PDO('mysql:host=localhost;dbname=nimgame;charset=utf8', 'root', '', array(
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                PDO::ATTR_PERSISTENT => true,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false
            ));
        }
    } catch (PDOException $e) {
        echo "DB connection error, see logs.";
        var_dump($e);
        error_log($e->getMessage());
    }
    return $db;
}

function addCoups($idParent, $coupValue, $idUser) {
    $db = myPdo();
    $sql = "INSERT INTO coups(idParent, CoupValue, idUser) VALUES (:idParent,:coupValue, :idUser);";
    $sql2 = "SELECT LAST_INSERT_ID();";
    $request = $db->prepare($sql);
    $request->execute(array("idParent" => $idParent,
        "coupValue" => $coupValue,
        "idUser" => $idUser));
    $request2=$db->prepare($sql2);
    $request2->execute();
    return $request2->fetch()[0];
}

function FirstCoup($idGame, $coupValue, $idUser){
    $db = myPdo();
    $idCoup = addCoups(NULL, $coupValue, $idUser);
    $sql = "UPDATE games SET idPremierCoup=:idPremierCoup WHERE idGame = :idGame;";
    $request = $db->prepare($sql);
    $request->execute(array("idPremierCoup" => $idCoup,
        "idGame" => $idGame));
    
    $sql2 = "SELECT LAST_INSERT_ID();";
    $request2=$db->prepare($sql2);
    $request2->execute();
    return $request2->fetch()[0];
}

function newGame($nbBilles) {
    $db = myPdo();
    $sql = "INSERT INTO games(NbBilles) VALUES (:nbBilles);";
    $sql2 = "SELECT LAST_INSERT_ID();";
    $request = $db->prepare($sql);
    $request->execute(array("nbBilles" => $nbBilles));
    $request2=$db->prepare($sql2);
    $request2->execute();
    return $request2->fetch()[0];
}
function getCoupsPrecedent($game){
    // Retourne un tableau avec les valeurs des coups 
}
function getGamesFromCoupsAndWinner($coupsPrecedents, $joueur1Won){
    // Retourne les parties qui ont les mêmes coups précédents et où le joueur 1/2 (selon la valeur de $joueur1Won) a gagné
}
function getCoupSuivant($game, $nbCoups){
    // Retourne le coup suivant de la partie spécifiée
}