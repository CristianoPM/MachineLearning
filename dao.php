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
    $request2 = $db->prepare($sql2);
    $request2->execute();
    return $request2->fetch()[0];
}

function setJoueur1Won($won, $idGame) {
    $db = myPdo();
    $sql = "UPDATE games SET joueur1Won=:won WHERE idGame = :idGame;";
    $request = $db->prepare($sql);
    $request->execute(array("won" => $won,
        "idGame" => $idGame));
}

function FirstCoup($idGame, $coupValue, $idUser) {
    $db = myPdo();
    $idCoup = addCoups(NULL, $coupValue, $idUser);
    $sql = "UPDATE games SET idPremierCoup=:idPremierCoup WHERE idGame = :idGame;";
    $request = $db->prepare($sql);
    $request->execute(array("idPremierCoup" => $idCoup,
        "idGame" => $idGame));

    $sql2 = "SELECT LAST_INSERT_ID();";
    $request2 = $db->prepare($sql2);
    $request2->execute();
    return $request2->fetch()[0];
}

function newGame($nbBilles) {
    $db = myPdo();
    $sql = "INSERT INTO games(NbBilles) VALUES (:nbBilles);";
    $sql2 = "SELECT LAST_INSERT_ID();";
    $request = $db->prepare($sql);
    $request->execute(array("nbBilles" => $nbBilles));
    $request2 = $db->prepare($sql2);
    $request2->execute();
    return $request2->fetch()[0];
}

function getLastCoup($game) {
// Retourne un tableau avec les valeurs des coups [1, 2]
    return end(getCoups($game));
}

function getGamesFromCoupsWinnerAndNbBilles($coupsPrecedents, $joueur1Won, $nbBilles, $similarGames) {
// Retourne les parties qui ont les mêmes coups précédents et où le joueur 1/2 (selon la valeur de $joueur1Won) a gagné
    $connection = myPdo();
    if ($similarGames === FALSE) {
        $stmt = "SELECT idGame FROM games g, coups c WHERE joueur1Won=$joueur1Won AND nbBilles=$nbBilles AND CoupValue=$coupsPrecedents[0]";
    } else {
        $stmt = "SELECT idGame FROM games g, coups c WHERE joueur1Won=$joueur1Won AND nbBilles=$nbBilles AND CoupValue=$coupsPrecedents[0] AND idGame IN (" . implode(", ", $similarGames) . ")";
    }
    $request = $connection->query($stmt);
    $games = $request->fetchAll(PDO::FETCH_NUM);
    foreach ($games as $id => $game) {
        if (getCoupSuivant($game, $i++)) {
            unset($games[$id]);
        }
    }
    return $games;
}

function getCoupSuivant($game, $nbCoups) {
// Retourne le coup suivant de la partie spécifiée
    $coups = getCoups($game);
    return $coups[$nbCoups];
}

function getCoups($idGame) {
    if (!isset($_SESSION["coups"])) {
        $_SESSION["coups"] = getAllCoups();
    }
    if (!isset($_SESSION["games"])) {
        $_SESSION["games"] = getAllGames();
    }
    $game = NULL;
    foreach ($_SESSION["games"] as $value) {
        if ($value["idParent"] === $idGame) {
            $game = $value;
        }
    }
    $coups = array();
    if ($game != NULL) {
        foreach ($_SESSION["coups"] as $value) {
            if ($value["idCoup"] == $game["idPremierCoup"]) {
                $coups[0] = $value["idCoup"];
            }
        }
        $idParent = $coups[0];
        do {
            $end = TRUE;
            foreach ($_SESSION["coups"] as $value) {
                if ($idParent == $value["idParent"]) {
                    $coups[] = $value["idCoup"];
                    $idParent = $value["idCoup"];
                    $end = FALSE;
                }
            }
        } while (!$end);
    }
    return $coups;
// Retourne tous les coups d'une partie
}

function getAllCoups() {
    $db = myPdo();
    $sql = "SELECT * FROM coups;";
    $request = $db->query($sql);
    return $request->fetchAll(PDO::FETCH_ASSOC);
}

function getAllGames() {
    $db = myPdo();
    $sql = "SELECT * FROM games;";
    $request = $db->query($sql);
    return $request->fetchAll(PDO::FETCH_ASSOC);
}

function PrendBilles($nb) {
    $_SESSION["nbBilles"] -= $nb;
    if ($_SESSION["LastCoup"] === NULL) {
        $_SESSION["LastCoup"] = FirstCoup($_SESSION["idGame"], $nb, ($_SESSION["joueur1"] ? "1" : "2"));
    } else {
        $_SESSION["LastCoup"] = addCoups($_SESSION["LastCoup"], $nb, ($_SESSION["joueur1"] ? "1" : "2"));
    }
    $_SESSION["joueur1"] = !$_SESSION["joueur1"];
    CheckEndGame();
}

function CheckEndGame() {
    if ($_SESSION["nbBilles"] <= 0) {
        $_SESSION["inGame"] = FALSE;
        header("Refresh:0");
    }
}

function iAPrendBilles() {
    PrendBilles(rand(1, 3));
// CoupIA($_SESSION["joueur1"], getCoups($_SESSION["idGame"]));
}
