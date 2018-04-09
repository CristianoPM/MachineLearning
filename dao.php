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
            $db = new PDO('mysql:host=127.0.0.1;dbname=nimgame;charset=utf8', 'root', '', array(
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
    $_SESSION = array();
    session_destroy();
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
    $joueur1Won = ($joueur1Won ? "TRUE" : "FALSE");
    if ($similarGames === FALSE) {
        $stmt = "SELECT idGame FROM games g, coups c WHERE joueur1Won=$joueur1Won AND nbBilles=$nbBilles AND CoupValue=$coupsPrecedents[0] AND idPremierCoup = idCoup;";
    } else {
        $stmt = "SELECT idGame FROM games g, coups c WHERE joueur1Won=$joueur1Won AND nbBilles=$nbBilles AND CoupValue=$coupsPrecedents[0] AND idPremierCoup = idCoup AND idGame IN (" . implode(", ", $similarGames) . ")";
    }
    $request = $connection->query($stmt);
    $games = $request->fetchAll(PDO::FETCH_NUM);
    $i = 0;
    foreach ($games as $id => $game) {
        if (getCoupSuivant($game, $i) != $coupsPrecedents[$i++]) {
            unset($games[$id]);
        }
        if (!isset($coupsPrecedents[$i])) {
            break;
        }
    }
    return $games;
}

function getCoupSuivant($game, $nbCoups) {
// Retourne le coup suivant de la partie spécifiée
    $coups = getCoups($game[0]);
    //echo '<pre>';var_dump($game);exit;
    /*echo '<pre>';
    var_dump($game);
    var_dump($coups);
    echo '</pre>';*/
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
    //echo '<pre>';
    // var_dump($_SESSION["games"]);echo '</pre>';exit;
    foreach ($_SESSION["games"] as $value) {
        if ($value["idGame"] === $idGame) {
            $game = $value;
        }
    }
    $coups = array();
    if ($game !== NULL) {
        foreach ($_SESSION["coups"] as $value) {
            if ($value["idCoup"] == $game["idPremierCoup"]) {
                $idParent = $value["idCoup"];
                $coups[0] = $value["CoupValue"];
                break;
            }
        }
        do {
            $end = TRUE;
            foreach ($_SESSION["coups"] as $value) {
                if ($idParent == $value["idParent"]) {
                    $coups[] = $value["CoupValue"];
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
    //$v = rand(1, 3);
    $v = CoupIA($_SESSION["joueur1"], getCoups($_SESSION["idGame"]), 5);
    PrendBilles($v);
}

function CoupIA($joueur1, $coupsPrecedents, $nbBillesInitial) {
    $games = getGamesFromCoupsWinnerAndNbBilles($coupsPrecedents, $joueur1, $nbBillesInitial, isset($_SESSION["similarGames"]) ? $_SESSION["similarGames"] : FALSE);
    $_SESSION["similarGames"] = $games;
    if (!empty($games)) {
        $coupPossibles = array();
        foreach ($games as $game) {
            $coupPossibles[] = getCoupSuivant($game, count($coupsPrecedents));
        }
        $coupPossibles[] = 1;
        $coupPossibles[] = 2;
        $coupPossibles[] = 3;
        $rand = array_rand($coupPossibles);
        var_dump($rand);
        return $rand;
    } else {
        return rand(1, 3);
    }
}
