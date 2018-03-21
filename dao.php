<?php

//-------------------------------------------------------------------------------------------
// Examen à blanc du module M151 - Janvier 2018
// 
// Auteur      : Cristiano Pereira kiadz
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
    $sql = "INSERT INTO coups(idParent, CoupValue, idUser) VALUES (:idParent,:coupValue, :idUser); SELECT LAST_INSERT_ID();";
    $request = $db->prepare($sql);
    $request->execute(array("idParent" => $idParent,
        "coupValue" => $coupValue,
        "idUser" => $idUser));
    return $request->fetch()[0];
}

function FirstCoup($idGame, $coupValue, $idUser){
    $idCoup = addCoups(NULL, $coupValue, $idUser);
    $sql = "UPDATE games SET idPremierCoup=:idPremierCoup WHERE idGame = : idGame;";
    $request = $db->prepare($sql);
    $request->execute(array("idPremierCoup" => $idCoup,
        "idGame" => $idGame));
}

function newGame($nbBilles) {
    $db = myPdo();
    $sql = "INSERT INTO games(NbBilles) VALUES (:nbBilles); SELECT LAST_INSERT_ID();";
    $request = $db->prepare($sql);
    $request->execute(array("nbBilles" => $nbBilles));
    return $request->fetch()[0];
}