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

    // fonctions de formatage et nettoyage de données
    function format($str){
        $str = str_replace('"', '', $str);
        $str = str_replace("'", '', $str);
        $str = str_replace('(', '', $str);
        $str = str_replace(')', '', $str);

        return $str;
    }
    function clean($str){
        $str = str_replace(',,', ",'',", $str);
        $str = explode(',', $str);
        return $str;
    }
	
	function formatDate($str){
        if($str != ''){
            $str = explode('-', $str)[2] ."/". explode('-', $str)[1] ."/". explode('-', $str)[0];
        }
        return $str;
    }

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
    $dbname2 = $credential_dict['$dbname2 '];
    $dbname0 = $credential_dict['$dbname0 '];
    $dbuser = $credential_dict['$dbuser '];
    $dbuserpass = $credential_dict['$dbuserpass '];
    $DBatip = "host=$hostname port=$port dbname=$dbname2 user=$dbuser password=$dbuserpass";
    $DBpheno = "host=$hostname port=$port dbname=$dbname0 user=$dbuser password=$dbuserpass";
    $DBconnAtip = pg_connect($DBatip);

    function getUsernameFromUserid($userid, $DBpheno){
        $username = "";
        $DBconnPheno = pg_connect($DBpheno);
        $sql = "SELECT  firstname, lastname FROM userinfos WHERE userid = '".$userid."';";
        $res = pg_query($DBconnPheno, $sql);
        while ($row = pg_fetch_row($res)) {
            $username = $row[0]. " ". $row[1];
        }
        return $username;
    }

    $measurements = array();
    $sql = "SELECT id, name, whole_image FROM annotations_measurements";
    $res = pg_query($DBconnAtip, $sql);
    $n = 0;
    while ($row = pg_fetch_row($res)) {
        $measurements[$n] = array();
        $measurements[$n]["id"] = $row[0];
        if($row[2] == 't'){
            $arg = $row[1] . " image entière";
        }else{ $arg = $row[1];}
        $measurements[$n]["name"] = $arg;
        $n++;
    }

    // function statistiques($plantID, $measurements, $DBatip){
    //     $DBconnAtip = pg_connect($DBatip);
    //     $mesures = array();

    //     foreach( $measurements as $mesure){
    //         $select = "SELECT COUNT(*) FROM annotations_images WHERE measurement='".$mesure['id']."' AND plant='".$plantID."'";
    //         if ($DBconnAtip) {
    //             $res = pg_query($DBconnAtip, $select);
    //             while ($row = pg_fetch_row($res)) {
    //                 $mesures[$mesure['id']] = $row[0];
    //             }
    //         }
    //     }
    //     return $mesures;
    // }

    $research = array();
    $research['plantes'] = array();
    $research['annotations'] = array();

    // PARTIE PLANTES
    $select = '(name, latin_name, specie, competition, genotype, h20_condition, k_condition, n_condition, p_condition, s_condition, userid, date, modification, id)';
    $plantes = array();
    if ($DBconnAtip) {
        $sql = "SELECT  ".$select." FROM annotations_plants ORDER BY id;";
        $res = pg_query($DBconnAtip, $sql);
        
        while ($row = pg_fetch_row($res)) {
            array_push($plantes, $row[0]);
        }
    }
    $research['plantes'] = $plantes;
    // PARTIE STATISTIQUES
    

    // PARTIE ANNOTATIONS
    $select = '(annotations_images.annotation_dirpath, annotations_compartments.name, annotations_images.days_after_sowing, annotations_images.dirpath, annotations_images.format, annotations_images.growing_degree_days, annotations_measurements.name, annotations_methods.name, annotations_images.name, annotations_objects.name, annotations_images.pot, annotations_tools.name, annotations_types.name, annotations_images.taskid, annotations_plants.name, annotations_images.userid, annotations_images.annotation_date, annotations_images.annotation_modification)';
    $annotations = array();
    if ($DBconnAtip) {
        $sql = "SELECT  ".$select." FROM annotations_images LEFT JOIN annotations_compartments ON annotations_images.compartment = annotations_compartments.id  LEFT JOIN annotations_measurements ON annotations_images.measurement = annotations_measurements.id LEFT JOIN annotations_tools ON annotations_images.tool = annotations_tools.id LEFT JOIN annotations_types ON annotations_images.type = annotations_types.id LEFT JOIN annotations_methods ON annotations_images.method = annotations_methods.id LEFT JOIN annotations_objects ON annotations_images.object = annotations_objects.id LEFT JOIN annotations_plants ON annotations_images.plant = annotations_plants.id ORDER BY annotations_images.id;";
        $res = pg_query($DBconnAtip, $sql);
        
        while ($row = pg_fetch_row($res)) {
            array_push($annotations, $row[0]);
        }
    }
    $research['annotations'] = $annotations;

?>

<!DOCTYPE html>
<html lang='fr-FR'>
    <head>
        <meta charset="UTF-8">
        <title>Annotations</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
        <script src="https://kit.fontawesome.com/9a39daeedf.js" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="../styles/research.css"/>

        <!-- <script type="text/javascript">
            function drawChart(mesure1, mesure2, mesure3) {
                var stats = false;
                for (const element of arguments) {
                    if(element != 0){
                        stats = true;
                    }
                }  
                if(stats == false){
                    window.alert('Aucunes annotations pour cette plante');
                }
                else{
                    // window.alert(mesure1 +' '+ mesure2 +' '+ mesure3);
                    window.open('./stats.php', '_blank');
                }
            }
        </script> -->
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
                    <form class="nav-link" method="post" action="./plant.php">
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
        <h1 class="titleUnderNavbar">Recherche</h1>
        <h2>Plantes</h2>
        <div class="table-wrapper">
            <div class="table-scroll">
            <table cellpadding="5" cellspacing="5" class="table table-striped">
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
                            <!-- <th>Statistiques</th> -->
                            <th>Ajouté par</th>
                            <th>Ajouté le</th>
                            <th>Modifié le</th>
                        </tr>
                    </thead>
                    <tbody>
                            <?php
                            foreach( $research['plantes'] as $infos){
                                $infos = clean($infos);
                                $mesures = statistiques(format($infos[13]), $measurements, $DBatip);
                                var_dump($mesures);
                                echo('<tr><td>'.format($infos[0]).'</td>');
                                echo('<td>'.format($infos[1]).'</td>');
                                echo('<td>'.format($infos[2]).'</td>');
                                echo('<td>'.format($infos[3]).'</td>');
                                echo('<td>'.format($infos[4]).'</td>');
                                echo('<td>'.format($infos[5]).'</td>');
                                echo('<td>'.format($infos[6]).'</td>');
                                echo('<td>'.format($infos[7]).'</td>');
                                echo('<td>'.format($infos[8]).'</td>');
                                echo('<td>'.format($infos[9]).'</td>');
                                // echo('<td><button type="submit" name="submitStats"  onclick="drawChart(\''.implode("','", $mesures).'\')" class="btn btn-outline-info saphir-btn"><i class="fas fa-clipboard-list"></i></button></td>');
                                echo('<td>'.getUsernameFromUserid($infos[10], $DBpheno).'</td>');
                                echo('<td>'.formatDate(format($infos[11])).'</td>');
                                echo('<td>'.formatDate(format($infos[12])).'</td></tr>');
                            }
                            ?>
                    </tbody>
                </table>
            </div>
            <h2>Annotations</h2>
        <div class="table-wrapper">
            <div class="table-scroll">
            <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Chemin d'accès d'annotation</th>
                            <th>Compartiment</th>
                            <th>Jours après semis</th>
                            <th>Chemin d'accès donnée originale</th>
                            <th>Format d'image</th>
                            <th>Temps thermique</th>
                            <th>Mesures</th>
                            <th>Methodes</th>
                            <th>Nom</th>
                            <th>Objet</th>
                            <th>Type de pot</th>
                            <th>Outil de mesure</th>
                            <th>Type de mesure</th>
                            <th>Numéro de tâche</th>
                            <th>Plante concerné</th>
                            <th>Ajouté par</th>
                            <th>Ajouté le</th>
                            <th>Modifié le</th>
                        </tr>
                    </thead>
                    <tbody>
                            <tr>
                            <?php
                            foreach( $research['annotations'] as $infos){
                                $infos = clean($infos);
                                echo('<tr><td>'.format($infos[0]).'</td>');
                                echo('<td>'.format($infos[1]).'</td>');
                                echo('<td>'.format($infos[2]).'</td>');
                                echo('<td>'.format($infos[3]).'</td>');
                                echo('<td>'.format($infos[4]).'</td>');
                                echo('<td>'.format($infos[5]).'</td>');
                                echo('<td>'.format($infos[6]).'</td>');
                                echo('<td>'.format($infos[7]).'</td>');
                                echo('<td>'.format($infos[8]).'</td>');
                                echo('<td>'.format($infos[9]).'</td>');
                                echo('<td>'.format($infos[10]).'</td>');
                                echo('<td>'.format($infos[11]).'</td>');
                                echo('<td>'.format($infos[12]).'</td>');
                                echo('<td>'.format($infos[13]).'</td>');
                                echo('<td>'.format($infos[14]).'</td>');
                                echo('<td>'.getUsernameFromUserid($infos[15], $DBpheno).'</td>');
                                echo('<td>'.formatDate(format($infos[16])).'</td>');
                                echo('<td>'.formatDate(format($infos[17])).'</td></tr>');
                            }
                            ?>
                            </tr>
                    </tbody>
                </table>
            </div>
        </div>
        </div>
    </body>
    
</html>