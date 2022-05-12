<?php
    if(!isset($_POST['hiddenuserlogin']) || !isset($_POST['hiddenislogged'])) {
        header("Location: http://".$_SERVER['SERVER_ADDR']."/portail.php");
        die();
    }
    if(isset($_POST['hiddenislogged'])) {
        if ($_POST['hiddenislogged'] == 'no') {
            header("Location: http://".$_SERVER['SERVER_ADDR']."/portail.php");
            die();
        }
    }
    $hiddensaphir_qrcodes = 'no';
    if (isset($_POST['hiddensaphir_qrcodes'])) {
        $hiddensaphir_qrcodes = $_POST['hiddensaphir_qrcodes'];
    }
    if(isset($_POST['hiddenuserlastname'])) { $userLastName = $_POST['hiddenuserlastname']; }
    if(isset($_POST['hiddenuserlogin'])) { $userLogin = $_POST['hiddenuserlogin']; }
    if(isset($_POST['hiddenuserfirstname'])) { $userFirstName = $_POST['hiddenuserfirstname']; }
    if(isset($_POST['hiddenuserid'])) { $userid = $_POST['hiddenuserid']; }
    if(isset($_POST['hiddenuserrole'])) { $userrole = $_POST['hiddenuserrole']; }
    if(isset($_POST['nombreAjout'])) { $nombreDeLignes = $_POST['nombreAjout']; } else {$nombreDeLignes = 0;}

    // connextion BDD avec fichier infos du credential
    $credentials = fopen('../../credentials/credentials.cred', 'rb');
    $credential_dict = array();
    while(!feof($credentials)){
        $ligne = fgets($credentials);
        $cred = explode('=', $ligne)[1];
        $cred = str_replace("'", "", $cred);
        $cred = str_replace(";", "", $cred);
        $cred = str_replace(" ", "", $cred);
        $cred = str_replace("\n", "", $cred);
        $credential_dict[explode('=', $ligne)[0]] = $cred;
    }
    $hostname = $credential_dict['$hostname '];
    $port = 5432;
    $dbname = $credential_dict['$dbname2 '];
    $dbuser = $credential_dict['$dbuser '];
    $dbuserpass = $credential_dict['$dbuserpass '];
    $infosDB = "host=$hostname port=$port dbname=$dbname user=$dbuser password=$dbuserpass";
    $dbconn = pg_connect($infosDB);

    if ($dbconn) {
        // envois des données dans la base de donnee
        if(isset($_POST['submitValider'])) {
            for($i = 0; $i < $nombreDeLignes+1; ++$i){
                $champs = array();
                array_push($champs, "userid");
                array_push($champs, "date");
                array_push($champs, "modification");
                $values = array();
                array_push($values, $userid);
                array_push($values, date('Y-m-d'));
                array_push($values, date('Y-m-d'));
                if(!isset($_POST['competition_'.$i])){
                    array_push($champs, "competition");
                    array_push($values, 'false');
                }
                foreach($_POST as $key => $value) {
                    if(substr($key, -1) == strval($i) && $value != ""){
                        array_push($champs, str_replace("_$i", "", $key));
                        array_push($values, $value);
                    }
                }
                $query = "INSERT INTO annotations_plants (".str_replace("'", "", implode("','",$champs)).") VALUES ('".implode("','",$values)."');";
                $res = pg_query($dbconn, $query);
            }
        }
    }
?>

<!DOCTYPE html>
<html lang='fr-FR'>
    <head>
        <meta charset="UTF-8">
        <title>Annotations</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
        <script src="https://kit.fontawesome.com/9a39daeedf.js" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="../styles/add.css"/>
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
                   // echo('<form class="nav-link" method="post" action="' .$portalAddr. '">'); ?>
                        <!-- <input type="submit" name="submitBackToPortal" value="Portail serres-4PMI" class="btn btn-outline-info">
                        <input type="hidden" size="100" name="hiddenuserlogin" value="<?php //echo($_POST['hiddenuserlogin']); ?>">
                        <input type="hidden" size="100" name="hiddenislogged" value="<?php //echo($_POST['hiddenislogged']); ?>">
                        <input type="hidden" size="100" name="hiddensaphir_qrcodes" value="<?php //echo($hiddensaphir_qrcodes); ?>">
                        <input type="hidden" size="100" name="hiddenuserlastname" value="<?php //echo($_POST['hiddenuserlastname']); ?>">
                        <input type="hidden" size="100" name="hiddenuserfirstname" value="<?php //echo($_POST['hiddenuserfirstname']); ?>">
                        <input type="hidden" size="100" name="hiddenuserid" value="<?php //echo($_POST['hiddenuserid']); ?>">
                        <input type="hidden" size="100" name="hiddenuserrole" value="<?php //echo($_POST['hiddenuserrole']); ?>">
                    </form> -->
                    <!-- affichage du bouton des parametres uniquement pour les membres de l'equipe ATIP -->
                    <?php if($userrole == "atip"){
                    // echo('<form class="nav-link" method="post" action="./settings.php">
                    //         <input type="submit" name="submitSettings" value="Gérer les paramètres" class="btn btn-outline-info"/>
                    //         <input type="hidden" size="100" name="hiddenuserlogin" value="'.$_POST['hiddenuserlogin'].'">
                    //         <input type="hidden" size="100" name="hiddenislogged" value="'.$_POST['hiddenislogged'].'">
                    //         <input type="hidden" size="100" name="hiddensaphir_qrcodes" value="'.$hiddensaphir_qrcodes.'">
                    //         <input type="hidden" size="100" name="hiddenuserlastname" value="'.$_POST['hiddenuserlastname'].'">
                    //         <input type="hidden" size="100" name="hiddenuserfirstname" value="'.$_POST['hiddenuserfirstname'].'">
                    //         <input type="hidden" size="100" name="hiddenuserid" value="'.$_POST['hiddenuserid'].'">
                    //         <input type="hidden" size="100" name="hiddenuserrole" value="'.$_POST['hiddenuserrole'].'">
                    //     </form>');
                    }?>
                    <form class="nav-link" method="post" action="./research.php">
                        <input type="submit" name="submitResearche" value="Recherche" class="btn btn-outline-info"/>
                        <input type="hidden" size="100" name="hiddenuserlogin" value="<?php echo($_POST['hiddenuserlogin']); ?>">
                        <input type="hidden" size="100" name="hiddenislogged" value="<?php echo($_POST['hiddenislogged']); ?>">
                        <input type="hidden" size="100" name="hiddensaphir_qrcodes" value="<?php echo($hiddensaphir_qrcodes); ?>">
                        <input type="hidden" size="100" name="hiddenuserlastname" value="<?php echo($_POST['hiddenuserlastname']); ?>">
                        <input type="hidden" size="100" name="hiddenuserfirstname" value="<?php echo($_POST['hiddenuserfirstname']); ?>">
                        <input type="hidden" size="100" name="hiddenuserid" value="<?php echo($_POST['hiddenuserid']); ?>">
                        <input type="hidden" size="100" name="hiddenuserrole" value="<?php echo($_POST['hiddenuserrole']); ?>">
                    </form>
                    <form class="nav-link" method="post" action="./annotation.php">
                        <input type="submit" name="submitAnnotation" value="Ajouter une annotation" class="btn btn-outline-info saphir-btn"/>
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
        <h1 class="titleUnderNavbar">Ajouter une plante</h1>
        <form class="nav-link centerDiv" method="post" action="">
            <label for="nombreAjout">Afficher des lignes de plante supplémentaire:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
            <input type="number" name="nombreAjout" id="nombreAjout" value="<?php echo($nombreDeLignes); ?>" step="1" min="0" max="10" class="btn btn-outline-info" id="number"/>
            <input type="submit" name="submitAjout" id="submitAjout" value="Valider" class="btn btn-outline-info"/>
            <input type="hidden" size="100" name="hiddenuserlogin" value="<?php echo($_POST['hiddenuserlogin']); ?>">
            <input type="hidden" size="100" name="hiddenislogged" value="<?php echo($_POST['hiddenislogged']); ?>">
            <input type="hidden" size="100" name="hiddensaphir_qrcodes" value="<?php echo($hiddensaphir_qrcodes); ?>">
            <input type="hidden" size="100" name="hiddenuserlastname" value="<?php echo($_POST['hiddenuserlastname']); ?>">
            <input type="hidden" size="100" name="hiddenuserfirstname" value="<?php echo($_POST['hiddenuserfirstname']); ?>">
            <input type="hidden" size="100" name="hiddenuserid" value="<?php echo($_POST['hiddenuserid']); ?>">
            <input type="hidden" size="100" name="hiddenuserrole" value="<?php echo($_POST['hiddenuserrole']); ?>">
        </form>
        <form class="" method="post" action="">
            <table cellpadding="5" cellspacing="5">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Nom Latin</th>
                        <th>Espèce</th>
                        <th>Compétition</th>
                        <th>Génotype</th>
                        <th>H2O condition</th>
                        <th>K condition</th>
                        <th>N condition</th>
                        <th>P condition</th>
                        <th>S condition</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for($i = 0; $i < $nombreDeLignes+1; ++$i){?>
                        <tr>
                            <td><input required type="text" name="name_<?php echo($i); ?>" id="name_<?php echo($i); ?>" value="" class="form-control"></td>
                            <td><input required type="text" name="latin_name_<?php echo($i); ?>" id="latin_name_<?php echo($i); ?>" value="" class="form-control"></td>
                            <td><input required type="text" name="specie_<?php echo($i); ?>" id="specie_<?php echo($i); ?>" value="" class="form-control"></td>
                            <td>
                                <input type="checkbox" name="competition_<?php echo($i); ?>" id="competition_<?php echo($i); ?>" class="form-check-input">
                            </td>
                            <td><input required type="text" name="genotype_<?php echo($i); ?>" id="genotype_<?php echo($i); ?>" value="" class="form-control"></td>
                            <td><input required type="text" name="h20_condition_<?php echo($i); ?>" id="h20_condition_<?php echo($i); ?>" value="" class="form-control"></td>
                            <td><input required type="text" name="k_condition_<?php echo($i); ?>" id="k_condition_<?php echo($i); ?>" value="" class="form-control"></td>
                            <td><input required type="text" name="n_condition_<?php echo($i); ?>" id="n_condition_<?php echo($i); ?>" value="" class="form-control"></td>
                            <td><input required type="text" name="p_condition_<?php echo($i); ?>" id="p_condition_<?php echo($i); ?>" value="" class="form-control"></td>
                            <td><input required type="text" name="s_condition_<?php echo($i); ?>" id="s_condition_<?php echo($i); ?>" value="" class="form-control"></td>
                        </tr>
                    <?php    }
                    ?>
                </tbody>
            </table>
            <div class="centerDiv submitMarge">
                <input type="submit" name="submitValider" value="Valider" onclick="SubmitBDD()" class="btn btn-outline-info"/>
                <input type="hidden" size="100" name="hiddenuserlogin" value="<?php echo($_POST['hiddenuserlogin']); ?>">
                <input type="hidden" size="100" name="hiddenislogged" value="<?php echo($_POST['hiddenislogged']); ?>">
                <input type="hidden" size="100" name="hiddensaphir_qrcodes" value="<?php echo($hiddensaphir_qrcodes); ?>">
                <input type="hidden" size="100" name="hiddenuserlastname" value="<?php echo($_POST['hiddenuserlastname']); ?>">
                <input type="hidden" size="100" name="hiddenuserfirstname" value="<?php echo($_POST['hiddenuserfirstname']); ?>">
                <input type="hidden" size="100" name="hiddenuserid" value="<?php echo($_POST['hiddenuserid']); ?>">
                <input type="hidden" size="100" name="hiddenuserrole" value="<?php echo($_POST['hiddenuserrole']); ?>">
                <input type="hidden" size="100" name="nombreAjout" value="<?php echo($nombreDeLignes); ?>">
            </div>
            </form>
    </body>
</html>
