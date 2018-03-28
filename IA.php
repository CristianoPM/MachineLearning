<?php

include "./dao.php";

/**
 * Méthode permettant de déterminer le coup de l'IA en fonction des parties déjà jouées
 * @param bool $joueur1 Booléen représentant si l'IA est le joueur1
 * @param array $coupsPrecedents Tableau représentant la valeur des coups prédédents
 * @return integer de 1 à 3
 */
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
        return array_rand($coupPossibles);
    } else {
        return rand(1, 3);
    }
}
