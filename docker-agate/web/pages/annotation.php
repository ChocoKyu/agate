<?php

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
    if(isset($_POST['nombreAjout'])) { $nombreDeLignes = $_POST['nombreAjout']; } else {$nombreDeLignes = 0;}

    // connextion BDD avec fichier infos du credential
    $credentials = fopen('../credentials/credentials.cred', 'rb');
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

    # requetes pour recuperer les contenus des selects
    $compartments = array();
    $measurements = array();
    $methods = array();
    $objects = array();
    $plants = array();
    $tools = array();
    $types = array();
    if ($dbconn) {
        $sql = "SELECT id, name FROM annotations_types";
        $res = pg_query($dbconn, $sql);
        $i = 0;
        while ($row = pg_fetch_row($res)) {
            $types[$i] = array();
            $types[$i]["id"] = $row[0];
            $types[$i]["name"] = $row[1];
            $i++;
        }
        $sql = "SELECT id, name FROM annotations_tools";
        $res = pg_query($dbconn, $sql);
        $j = 0;
        while ($row = pg_fetch_row($res)) {
            $tools[$j] = array();
            $tools[$j]["id"] = $row[0];
            $tools[$j]["name"] = $row[1];
            $j++;
        }
        $sql = "SELECT id, name FROM annotations_objects";
        $res = pg_query($dbconn, $sql);
        $k = 0;
        while ($row = pg_fetch_row($res)) {
            $objects[$k] = array();
            $objects[$k]["id"] = $row[0];
            $objects[$k]["name"] = $row[1];
            $k++;
        }
        $sql = "SELECT id, name FROM annotations_methods";
        $res = pg_query($dbconn, $sql);
        $l = 0;
        while ($row = pg_fetch_row($res)) {
            $methods[$l] = array();
            $methods[$l]["id"] = $row[0];
            $methods[$l]["name"] = $row[1];
            $l++;
        }
        $sql = "SELECT id, name FROM annotations_compartments";
        $res = pg_query($dbconn, $sql);
        $m = 0;
        while ($row = pg_fetch_row($res)) {
            $compartments[$m] = array();
            $compartments[$m]["id"] = $row[0];
            $compartments[$m]["name"] = $row[1];
            $m++;
        }
        $sql = "SELECT id, name, whole_image FROM annotations_measurements";
        $res = pg_query($dbconn, $sql);
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
        $sql = "SELECT id, name FROM annotations_plants";
        $res = pg_query($dbconn, $sql);
        $o = 0;
        while ($row = pg_fetch_row($res)) {
            $plants[$o] = array();
            $plants[$o]["id"] = $row[0];
            $plants[$o]["name"] = $row[1];
            $m++;
        }
    

        // envois des données dans la base de donnee
        if(isset($_POST['submitValider'])) {
            for($i = 0; $i < $nombreDeLignes+1; ++$i){
                $champs = array();
                array_push($champs, "userid");
                array_push($champs, "annotation_date");
                array_push($champs, "annotation_modification");
                $values = array();
                array_push($values, $userid);
                array_push($values, date('Y-m-d'));
                array_push($values, date('Y-m-d'));
                foreach($_POST as $key => $value) {
                    if(substr($key, -1) == strval($i) && $value != ""){
                        array_push($champs, str_replace("_$i", "", $key));
                        array_push($values, $value);
                    }
                }
                $query = "INSERT INTO annotations_images (".str_replace("'", "", implode("','",$champs)).") VALUES ('".implode("','",$values)."');";
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
                    <!-- affichage du bouton des parametres uniquement pour les membres de l'equipe ATIP -->
                    <?php if($userrole == "atip"){
                    //echo('<form class="nav-link" method="post" action="./settings.php">
                        //     <input type="submit" name="submitSettings" value="Gérer les paramètres" class="btn btn-outline-info"/>
                        //     <input type="hidden" size="100" name="hiddenuserlogin" value="'.$_POST['hiddenuserlogin'].'">
                        //     <input type="hidden" size="100" name="hiddenislogged" value="'.$_POST['hiddenislogged'].'">
                        //     <input type="hidden" size="100" name="hiddensaphir_qrcodes" value="'.$hiddensaphir_qrcodes.'">
                        //     <input type="hidden" size="100" name="hiddenuserlastname" value="'.$_POST['hiddenuserlastname'].'">
                        //     <input type="hidden" size="100" name="hiddenuserfirstname" value="'.$_POST['hiddenuserfirstname'].'">
                        //     <input type="hidden" size="100" name="hiddenuserid" value="'.$_POST['hiddenuserid'].'">
                        //     <input type="hidden" size="100" name="hiddenuserrole" value="'.$_POST['hiddenuserrole'].'">
                        // </form>');
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
        <h1 class="titleUnderNavbar">Ajouter une annotation</h1>
        <form class="nav-link centerDiv" method="post" action="">
            <label for="nombreAjout">Afficher des lignes d'annotation supplémentaire:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
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
                        <th>Date d'aquisition</th>
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
                    </tr>
                </thead>
                <tbody>
                    <?php for($i = 0; $i < $nombreDeLignes+1; ++$i){?>
                        <tr>
                            <td><input required type="date" name="acquisition_datetime_<?php echo($i); ?>" value="<?php date('Y-m-d') ?>" min="<?php date('Y-m-d') ?>" class="form-control"></td>
                            <td><input type="text" name="annotation_dirpath_<?php echo($i); ?>" id="annotation_dirpath_<?php echo($i); ?>" value="" class="form-control"></td>
                            <td><select name="compartment_<?php echo($i); ?>" id="compartment_<?php echo($i); ?>" class="form-select">
                                <option value="" disabled="disabled" selected="selected"></option>
                                <?php foreach ($compartments as $compartment){echo('<option value="'.$compartment["id"].'">'.$compartment["name"].'</option>');}?>
                            </select></td>
                            <td><input type="number" name="days_after_sowing_<?php echo($i); ?>" id="days_after_sowing_<?php echo($i); ?>" value="" class="form-control"></td>
                            <td><input type="text" name="dirpath_<?php echo($i); ?>" id="dirpath_<?php echo($i); ?>" value="" class="form-control"></td>
                            <td><input required type="text" name="format_<?php echo($i); ?>" id="format_<?php echo($i); ?>" value="" class="form-control"></td>
                            <td><input type="number" name="growing_degree_days_<?php echo($i); ?>" id="growing_degree_days_<?php echo($i); ?>" value="" class="form-control"></td>
                            <td><select required name="measurement_<?php echo($i); ?>"  id="measurement_<?php echo($i); ?>" class="form-select">
                                <option value="" disabled="disabled" selected="selected"></option>
                                <?php foreach ($measurements as $measurement){echo('<option value="'.$measurement["id"].'">'.$measurement["name"].'</option>');}?>
                            </select></td>
                            <td><select required name="method_<?php echo($i); ?>" id="method_<?php echo($i); ?>" class="form-select">
                                <option value="" disabled="disabled" selected="selected"></option>
                                <?php foreach ($methods as $method){echo('<option value="'.$method["id"].'">'.$method["name"].'</option>');}?>
                            </select></td>
                            <td><input required type="text" name="name_<?php echo($i); ?>" id="name_<?php echo($i); ?>" value="" class="form-control"></td>
                            <td><select required name="object_<?php echo($i); ?>" id="object_<?php echo($i); ?>" class="form-select">
                                <option value="" disabled="disabled" selected="selected"></option>
                                <?php foreach ($objects as $object){echo('<option value="'.$object["id"].'">'.$object["name"].'</option>');}?>
                            </select></td>
                            <td><input required type="text" name="pot_<?php echo($i); ?>" id="pot_<?php echo($i); ?>" value="" class="form-control"></td>
                            <td><select required name="tool_<?php echo($i); ?>" id="tool_<?php echo($i); ?>" class="form-select">
                                <option value="" disabled="disabled" selected="selected"></option>
                                <?php foreach ($tools as $tool){echo('<option value="'.$tool["id"].'">'.$tool["name"].'</option>');}?>
                            </select></td>
                            <td><select required name="type_<?php echo($i); ?>" id="type_<?php echo($i); ?>" class="form-select">
                                <option value="" disabled="disabled" selected="selected"></option>
                                <?php foreach ($types as $type){echo('<option value="'.$type["id"].'">'.$type["name"].'</option>');}?>
                            </select></td>
                            <td><input required type="number" name="taskid_<?php echo($i); ?>" id="taskid_<?php echo($i); ?>" value="" class="form-control"></td>
                            <!--  select des plantes de la bdd -->
                            <td><select name="plant_<?php echo($i); ?>" id="plant_<?php echo($i); ?>" class="form-select">
                                <option value="" disabled="disabled" selected="selected"></option>
                                <?php foreach ($plants as $plant){echo('<option value="'.$plant["id"].'">'.$plant["name"].'</option>');}?>
                            </select></td>
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