<?php
if (session_id() == '') {
    session_start();
}
include_once  'helper/suche.php';
include_once 'helper/form_functions.php';
include_once 'helper/database_functions.php';

global $conn;
$startkilometer = "";
$endkilometer = "";
$fahrtbeginn = "";
$fahrtende = "";
$tableName = 'fahrt';
$message = "";
$validation =true;
$site = $_GET['site'];
$parts = explode("?", $site);

if(!empty($parts))
{
    $paramValue = getUrlParam($parts[1]);
    
    $startkilometer = getValue($conn,$tableName,'Startkilometerstand','FahrtID',$paramValue);
    $endkilometer = getValue($conn,$tableName,'Endkilometerstand','FahrtID',$paramValue);
    $fahrtbeginn = getValue($conn,$tableName,'Fahrtbeginn','FahrtID',$paramValue);
    $fahrtende = getValue($conn,$tableName,'Fahrtende','FahrtID',$paramValue);
    if($fahrtende == null){
        $jetzt = new DateTime();    
        $fahrtende = $jetzt -> format('Y-m-d H:i:s');
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Sammle und verarbeite hier die Formulardaten
    //Hier bekomme ich den Wert von dem Input-Attribut
    
    //Hier bekomme ich den Wert von dem Plz-Attribut
    $endkilometer = getPostParameter("endkilometer","");
    $fahrtbeginn = getPostParameter("fahrtbeginn","");
    $fahrtende = getPostParameter("fahrtende","");


    //Der name der Spalte als Key-Attribut, der Value von name als Value-Atribut. Das ist ein Zwischenschritt, damit man die Daten in die Datenbank speichern kann.
    
    $endkilometerValueDb = ['Endkilometerstand' => $endkilometer];
    $fahrtbeginnValueDb = ['Fahrtbeginn' => $fahrtbeginn];
    $fahrtendeValueDb = ['Fahrtende' => $fahrtende];

    //Vorbereitung zur Speicherung HIER JEWEILIGE ID EINGEBEN
    $fahrtSuchBedienung = ['FahrtID = ' . $paramValue];
    //PLZ bleibt gleich, Ort verändert sich HIER AKTUELLE DATEN EINGEBEN

   // update fahrt set (startkilometer) values (100) where fahrt_id = ?
    $message = validateForm($startkilometer,$endkilometer,$fahrtbeginn, $fahrtende);
    $validation = $message == "";
    if($validation){
        $updateData =  $endkilometerValueDb +  $fahrtbeginnValueDb + $fahrtendeValueDb;
    updateRecord($conn, $tableName, $updateData, $fahrtSuchBedienung);
    //PLZ bleibt eigentlich gleich, wird hier aber nocheinmal zugewiesen.
    //showAlertSuccess("Fahrt ist bereits vorhanden. $tableName wurde aktualisiert.");
   
    //UPDATE RECORD DUPLIZIEREN WENN MAN MEHRERE TABELLEN BRAUCHT und bei tablename die entsprechende Tabelle reinschreiben
    header('Location: index');
    }   
}

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

<div class="card border-0 p-4 container d-flex align-items-center flex-column mt-4 gap-4 shadow">
    <h2>Formular Taxi</h2>
    <form action="" method="post">
        <div class="row m-3">
            <label class="col-md-3" for="suchbegriff">Startkilometer</label>
            <input class="col-md-9 rounded p-1" type="text" value="<?php echo htmlspecialchars($startkilometer); ?>" class="form-control" id="startkilometer" name="startkilometer" disabled>
        </div>
        <div class="row m-3">
            <label class="col-md-3" for="suchbegriff">Endkilometer</label>
            <input class="col-md-9 rounded p-1" type="text" value="<?php echo htmlspecialchars($endkilometer); ?>" class="form-control" id="endkilometer" name="endkilometer" >
        </div>
        <h3 class="mt-3 ">Fahrtzeitraum</h3>
        <div class="mt-3">
        <div class="row m-3">
            <label class="col-md-3" for="suchbegriff">Fahrtbeginn:</label>
            <input type="datetime" value="<?php echo htmlspecialchars($fahrtbeginn); ?>" class="col-md-9 p-1 rounded" id="fahrtbeginn" name="fahrtbeginn">
        </div>
        <div class="row m-3">
            <label class="col-md-3" for="suchbegriff">Fahrtende:</label>
            <input type="datetime" value="<?php echo htmlspecialchars($fahrtende); ?>" class="col-md-9 p-1 rounded" id="fahrtende" name="fahrtende">
        </div>
        <div class="mt-3">
            <button type="submit" class="btn btn-primary">Anzeigen</button>
        </div>
        </div>
    </form>

<div class="card">
    <?php if(!$validation) echo showAlertWarning($message) ?>
</div>

</body>
</html>