<?php

include "./dao.php";

/**
 * Méthode permettant de déterminer le coup de l'IA en fonction des parties déjà jouées
 * @param bool $joueur1 Booléen représentant si l'IA est le joueur1
 * @param array $coupsPrecedents Tableau représentant la valeur des coups prédédents
 * @return integer de 1 à 3
 */
