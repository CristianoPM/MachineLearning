<?php
session_start();

require_once 'dao.php';

if (filter_has_var(INPUT_POST, "newGame")) {
    $_SESSION["inGame"] = TRUE;
    $_SESSION["nbBilles"] = 5;
    $_SESSION["joueur1"] = TRUE;
    $_SESSION["LastCoup"] = NULL;
    $_SESSION["idGame"] = newGame($_SESSION["nbBilles"], NULL);
}
if ($_SESSION["inGame"]) {
    if ($_SESSION["joueur1"]) {
        if (filter_has_var(INPUT_POST, "1bille")) {
            PrendBilles(1);
        } elseif (filter_has_var(INPUT_POST, "2billes")) {
            PrendBilles(2);
        } elseif (filter_has_var(INPUT_POST, "3billes")) {
            PrendBilles(3);
        }
    } if (!$_SESSION["joueur1"]) {
        iAPrendBilles();
    }
} else {
    if (!$_SESSION["joueur1"]) {
        $gagnant = "L'IA";
        setJoueur1Won(FALSE, $_SESSION["idGame"]);
    } else {
        $gagnant = "LE JOUEUR 1";
        setJoueur1Won(TRUE, $_SESSION["idGame"]);
    }
    $gagnant = "$gagnant A GAGNÉ !";
}
?><!DOCTYPE html>
<html>
    <head>
        <title>Jeu de nim</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <script>
            $('.game_bar > span').click(function () {
                $(this).addClass('active').siblings().removeClass('active');
                $(this).parent().attr('data-rating-value', $(this).data('rating-value'));
            });
        </script>
        <style>
            #game_bar {
                width:100px;
                height:100px;
                margin: 4px 175px !important;
                display:inline-block;
                display:inline;
            }
            #game_bar > span:before {
                content:'O';
                color: #c7c5c5;
                cursor:pointer;
                font-size:3em;
            }
            #game_bar:hover > span:before {
                color: #4bce32;
            }

            #game_bar > span:hover ~ span:before{
                color: #c7c5c5;
            }
        </style>
    </head>
    <body class='container' style="background-color: #F17F03;">
        <noscript><p>Please enable javascript for this site to work properly</p><style>
            div {
                display: none;
            }
        </style></noscript>
        <div class="col-md-12 col-xs-12">
            <nav class="navbar navbar-default" style="background-color: #ff9f37; border: none;">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <!--<a class="navbar-brand" href="index.php" style="margin-top: 10px;">NIM</a>-->
                    </div>
                    <div id="navbar" class="navbar-collapse collapse">
                        <ul class="nav navbar-nav">
                            <li><a href="index.php" style="color: white;">Accueil</a></li>
                        </ul>
                        <?php //}        ?>
                    </div><!--/.nav-collapse -->
                </div><!--/.container-fluid -->
            </nav>
            <!-- Carousel
================================================== -->
            <div id="myCarousel" class="carousel slide" data-ride="carousel">
                <!-- Indicators -->
                <ol class="carousel-indicators">
                    <li data-target="#myCarousel" data-slide-to="0"></li>
                    <li data-target="#myCarousel" data-slide-to="1" class="active"></li>
                    <li data-target="#myCarousel" data-slide-to="2"></li>
                </ol>
                <div class="carousel-inner" role="listbox" style="border-radius: 5px;">
                    <div class="item">
                        <div style="height: 300px; width: 100%; background-image: radial-gradient(white, #ff9f37); line-height: 300px; text-align: center;">
                            <img src="images/car1.png" style="height: 100%;" alt="bg">
                        </div>                       
                    </div>
                    <div class="item active">
                        <div style="height: 300px; width: 100%; background-image: radial-gradient(#ff9f37, white); line-height: 300px; text-align: center;">
                            <img class="first-slide" src="images/car2.png" alt="Second slide">
                        </div>
                    </div>
                    <div class="item">
                        <div style="height: 300px; width: 100%; background-image: radial-gradient(white, #ff9f37); line-height: 300px; text-align: center;">
                            <img src="images/car3.png" style="height: 100%;" alt="bg">
                        </div>
                    </div>
                </div>
                <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev" style="background-image: none; border-radius: 5px;">
                    <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next" style="background-image: none;border-radius: 5px;">
                    <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </a>
            </div><!-- /.carousel -->

            <div class="col-md-12 col-xs-12" style="background-color: #ff9f37; border-radius: 5px; margin-top: 20px; height: 400px;">
                <div class="col-md-12 col-xs-12">
                    <form method="POST">
                        <div class="col-md-4 col-md-offset-4">
                            <h1 style="text-align:center;">Nouvelle partie</h1>
                            <input class="btn btn-default center-block" type="submit" value="Joueur vs IA" name="newGame" style="width: 200px; height: 50px; background-color: #F17F03; color: white; border: none; border-radius: 50px; font-size: 20px;"> <br>
                            <input class="btn btn-default center-block" type="submit" value="Joueur vs Joueur" name="" style="width: 200px; height: 50px; background-color: #F17F03; color: white; border: none; border-radius: 50px; font-size: 20px;"disabled=""> <br>
                            <input class="btn btn-default center-block" type="submit" value="IA vs IA" name="" style="width: 200px; height: 50px; background-color: #F17F03; color: white; border: none; border-radius: 50px; font-size: 20px;"disabled="">
                        </div>

                        <?php if ($_SESSION["inGame"]) { ?>
                            <input class="btn btn-default" type="submit" value="1" name="1bille">
                            <input class="btn btn-default" type="submit" value="2" name="2billes">
                            <input class="btn btn-default" type="submit" value="3" name="3billes"><?php } ?>
                    </form>
                    <!--<p class="text-center"><?= ($_SESSION["inGame"] ? $_SESSION["nbBilles"] : "") ?><?= (isset($gagnant) ? $gagnant : "") ?></p>-->
                    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
                    <div id="game_bar"> 
                        <?php for ($i = 0; $i < $_SESSION["nbBilles"]; $i++) { ?>
                            <span data-rating-value="1"></span>
                        <?php } ?>
                    </div>
                </div> 
            </div>

        </div>
        <!-- Bootstrap core JavaScript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
        <script src="Bootstrap/js/bootstrap.min.js"></script>
        <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
        <script src="../../assets/js/ie10-viewport-bug-workaround.js"></script>
    </body>

</html>