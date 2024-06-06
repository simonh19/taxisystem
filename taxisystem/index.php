<?php
if (session_id() == '') {
    session_start();
}
include 'helper/suche.php';
include 'helper/form_functions.php';
include 'helper/database_functions.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css">
    <script src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="node_modules/@popperjs/core/dist/umd/popper.min.js"></script>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">BS Linz 2</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup"
            aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
        <div class="navbar-nav">
            <a class="nav-item nav-link" href="index.php">Startseite</a>
        </div>
        <div class="navbar-nav">
            <a class="nav-item nav-link" href="index.php?site=fahrt-erstellen">Fahrt erstellen</a>
        </div>
    </div>
</nav>
<div class="card border-0 p-4 container d-flex align-items-center flex-column mt-4 gap-4">

    <?php
        $query = "Select concat (Vorname,' ',Nachname) as Fahrer,Fahrt.FahrtID, Modelname, concat( start_adr.Straße,' ', start_adr.Ort,' ',  start_adr.PLZ) as Startadresse,concat(ziel_adr.Straße,' ',ziel_adr.Ort,' ',ziel_adr.PLZ) as Zieladresse, Fahrt.Fahrtbeginn, Fahrt.Fahrtende
        from fahrt join FahrerInnen on FahrerInnen.FahrerID = Fahrt.FahrerInnen_FahrerID
        left join Adresse start_adr on fahrt.Startadresse = start_adr.AdresseID
        left join Adresse ziel_adr on fahrt.Zieladresse = ziel_adr.AdresseID
        left join eAutos on eAutos.AutoID = Fahrt.AutoID
        left join Modell on Modell.ModellID = eAutos.Modell_ModellID;";
        $stmt = executeQuery($conn,$query);
        if (isset($_GET["site"])) {
            $fullUrl = $_GET["site"];
            if (str_contains($fullUrl, "?")) {
                $separator = "?";
                $parts = explode($separator, $fullUrl);
                $_GET['urlParam'] = $parts;
                $site = $parts[0];
                include_once($site . ".php");
            } else {
                include_once($fullUrl . ".php");
            }
        } else {
            echo generateTableFromQuery($conn, $stmt,'FahrtID','fahrt');
        }
    ?>
</div>
</body>
</html>