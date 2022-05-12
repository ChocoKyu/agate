<?php
    $_POST['hiddenuserlogin'] = "cslefebvre";
    $_POST['hiddenislogged'] = true;
    $_POST['hiddenuserlastname'] = "Lefebvre";
    $_POST['hiddenuserfirstname'] = "Cassandra";
    $_POST['hiddenuserid'] = "53";
    $_POST['hiddenuserrole'] = "atip";

    // if(!isset($_POST['hiddenuserlogin']) || !isset($_POST['hiddenislogged'])) {
    //     header("Location: http://".$_SERVER['SERVER_ADDR']."/portail.php");
    //     die();
    // }
    // if(isset($_POST['hiddenislogged'])) {
    //     if ($_POST['hiddenislogged'] == 'no') {
    //         header("Location: http://".$_SERVER['SERVER_ADDR']."/portail.php");
    //         die();
    //     }
    // }
    $hiddensaphir_qrcodes = 'no';
    if (isset($_POST['hiddensaphir_qrcodes'])) {
        $hiddensaphir_qrcodes = $_POST['hiddensaphir_qrcodes'];
    }
    if(isset($_POST['hiddenuserlastname'])) { $userLastName = $_POST['hiddenuserlastname']; }
    if(isset($_POST['hiddenuserlogin'])) { $userLogin = $_POST['hiddenuserlogin']; }
    if(isset($_POST['hiddenuserfirstname'])) { $userFirstName = $_POST['hiddenuserfirstname']; }
    if(isset($_POST['hiddenuserid'])) { $userid = $_POST['hiddenuserid']; }
    if(isset($_POST['hiddenuserrole'])) { $userrole = $_POST['hiddenuserrole']; }
?>

<!DOCTYPE html>

<html lang='fr-FR'>
    <head>
        <meta charset="UTF-8">
        <title>Annotations</title>
        <!-- Link bootstrap -->
        <!-- CSS only -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <!-- JavaScript Bundle with Popper -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

        <script src="https://kit.fontawesome.com/9a39daeedf.js" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="./styles/index.css"/>
    </head>

    <body>
    <nav class="navbar navbar-default fixed-top navbar-expand-xl">
            <div class="container-fluid">
                <?php echo('<a class="navbar-brand inactiveLink" href="#">Bienvenue ' . $userFirstName . ' ' . $userLastName . ' : <i>' . $userLogin . '</i></a>'); ?>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="fas fa-bars "></i>
                </button>
                <div class="collapse navbar-collapse flex-row-reverse" id="navbarSupportedContent">
                    <?php $portalAddr = "http://" .$_SERVER['SERVER_ADDR'] . "/portail.php"; 
                    //echo('<form class="nav-link" method="post" action="' .$portalAddr. '">'); ?>
                        <!-- <input type="submit" name="submitBackToPortal" value="Portail serres-4PMI" class="btn btn-outline-info">
                        <input type="hidden" size="100" name="hiddenuserlogin" value="<?php //echo($_POST['hiddenuserlogin']); ?>">
                        <input type="hidden" size="100" name="hiddenislogged" value="<?php //echo($_POST['hiddenislogged']); ?>">
                        <input type="hidden" size="100" name="hiddensaphir_qrcodes" value="<?php //echo($hiddensaphir_qrcodes); ?>">
                        <input type="hidden" size="100" name="hiddenuserlastname" value="<?php //echo($_POST['hiddenuserlastname']); ?>">
                        <input type="hidden" size="100" name="hiddenuserfirstname" value="<?php //echo($_POST['hiddenuserfirstname']); ?>">
                        <input type="hidden" size="100" name="hiddenuserid" value="<?php //echo($_POST['hiddenuserid']); ?>">
                        <input type="hidden" size="100" name="hiddenuserrole" value="<?php //echo($_POST['hiddenuserrole']); ?>">
                    </form> -->
                    <?php if($userrole == "atip"){
                    // echo('<form class="nav-link" method="post" action="./pages/settings.php">
                    //         <input type="submit" name="submitSettings" value="Gérer les paramètres" class="btn btn-outline-info saphir-btn"/>
                    //         <input type="hidden" size="100" name="hiddenuserlogin" value="'.$_POST['hiddenuserlogin'].'">
                    //         <input type="hidden" size="100" name="hiddenislogged" value="'.$_POST['hiddenislogged'].'">
                    //         <input type="hidden" size="100" name="hiddensaphir_qrcodes" value="'.$hiddensaphir_qrcodes.'">
                    //         <input type="hidden" size="100" name="hiddenuserlastname" value="'.$_POST['hiddenuserlastname'].'">
                    //         <input type="hidden" size="100" name="hiddenuserfirstname" value="'.$_POST['hiddenuserfirstname'].'">
                    //         <input type="hidden" size="100" name="hiddenuserid" value="'.$_POST['hiddenuserid'].'">
                    //         <input type="hidden" size="100" name="hiddenuserrole" value="'.$_POST['hiddenuserrole'].'">
                    //     </form>');
                    }?>
                    <form class="nav-link" method="post" action="./pages/research.php">
                        <input type="submit" name="submitResearche" value="Recherche" class="btn btn-outline-info saphir-btn"/>
                        <input type="hidden" size="100" name="hiddenuserlogin" value="<?php echo($_POST['hiddenuserlogin']); ?>">
                        <input type="hidden" size="100" name="hiddenislogged" value="<?php echo($_POST['hiddenislogged']); ?>">
                        <input type="hidden" size="100" name="hiddensaphir_qrcodes" value="<?php echo($hiddensaphir_qrcodes); ?>">
                        <input type="hidden" size="100" name="hiddenuserlastname" value="<?php echo($_POST['hiddenuserlastname']); ?>">
                        <input type="hidden" size="100" name="hiddenuserfirstname" value="<?php echo($_POST['hiddenuserfirstname']); ?>">
                        <input type="hidden" size="100" name="hiddenuserid" value="<?php echo($_POST['hiddenuserid']); ?>">
                        <input type="hidden" size="100" name="hiddenuserrole" value="<?php echo($_POST['hiddenuserrole']); ?>">
                    </form>
                    <form class="nav-link" method="post" action="./pages/annotation.php">
                        <input type="submit" name="submitAnnotation" value="Ajouter une annotation" class="btn btn-outline-info saphir-btn"/>
                        <input type="hidden" size="100" name="hiddenuserlogin" value="<?php echo($_POST['hiddenuserlogin']); ?>">
                        <input type="hidden" size="100" name="hiddenislogged" value="<?php echo($_POST['hiddenislogged']); ?>">
                        <input type="hidden" size="100" name="hiddensaphir_qrcodes" value="<?php echo($hiddensaphir_qrcodes); ?>">
                        <input type="hidden" size="100" name="hiddenuserlastname" value="<?php echo($_POST['hiddenuserlastname']); ?>">
                        <input type="hidden" size="100" name="hiddenuserfirstname" value="<?php echo($_POST['hiddenuserfirstname']); ?>">
                        <input type="hidden" size="100" name="hiddenuserid" value="<?php echo($_POST['hiddenuserid']); ?>">
                        <input type="hidden" size="100" name="hiddenuserrole" value="<?php echo($_POST['hiddenuserrole']); ?>">
                    </form>
                    <form class="nav-link" method="post" action="./pages/plant.php">
                        <input type="submit" name="submitPlant" value="Ajouter une plante" class="btn btn-outline-info saphir-btn"/>
                        <input type="hidden" size="100" name="hiddenuserlogin" value="<?php echo($_POST['hiddenuserlogin']); ?>">
                        <input type="hidden" size="100" name="hiddenislogged" value="<?php echo($_POST['hiddenislogged']); ?>">
                        <input type="hidden" size="100" name="hiddensaphir_qrcodes" value="<?php echo($hiddensaphir_qrcodes); ?>">
                        <input type="hidden" size="100" name="hiddenuserlastname" value="<?php echo($_POST['hiddenuserlastname']); ?>">
                        <input type="hidden" size="100" name="hiddenuserfirstname" value="<?php echo($_POST['hiddenuserfirstname']); ?>">
                        <input type="hidden" size="100" name="hiddenuserid" value="<?php echo($_POST['hiddenuserid']); ?>">
                        <input type="hidden" size="100" name="hiddenuserrole" value="<?php echo($_POST['hiddenuserrole']); ?>">
                    </form>
                </div>
            </div>
        </nav>
        <br><br><br><br>
            <div class="cute-robot-v1">
                <div class="circle-bg">
                    <div class="robot-ear left"></div>
                    <div class="robot-head">
                    <div class="robot-face">
                        <div class="eyes left"></div>
                        <div class="eyes right"></div>
                        <div class="mouth"></div>
                    </div>
                    </div>
                    <div class="robot-ear right"></div>
                    <div class="robot-body"></div>
                </div>
            </div><br><br>

            <h1>AGATE<br>Application de Gestion des Annotations TEchniques</h1>

            <!-- <fieldset>       
            <legend>Informations de mise à jour</legend>
                <ul>
                <li>Ajouter une plante ou une annotation</li>
                    <ul>
                        <li>Vendredi 31 Mars 2022</li>
                    </ul>
                <li>Recherche & modification des annotations</li>
                        <ul>
                            <li>Vendredi 18 Mars 2022</li>
                        </ul>
                <li>Gérer les paramètres</li>
                        <ul>
                            <li>Vendredi 18 Mars 2022</li>
                        </ul>
                        
                </ul>
            </fieldset> -->


    </body>
</html>