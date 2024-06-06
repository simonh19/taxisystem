<?php
if (session_id() == '') {
    session_start();
}
include_once  'helper/suche.php';
include_once 'helper/form_functions.php';
include_once 'helper/database_functions.php';

global $conn;
$fahrerVorname = "";
$fahrerNachname = "";
$kilometerstand = "";
$auto = "";
$modell = "";
$marke = "";
$startadresse = "";
$endadresse="";

$tableFahrt = 'fahrt';
$tableEauto = 'eautos';
$tableModell = 'modell';
$tableMarke = 'marke';
$tableFahrer = 'fahrerinnen';

$fahrerQuery = 'Select FahrerID as value, concat (Vorname," ",Nachname) as label
from fahrerinnen';
$stmt = $conn -> prepare($fahrerQuery);
$stmt -> execute();
$fahrerDaten = $stmt -> fetchall(PDO::FETCH_ASSOC);

$autoQuery = 'Select AutoID as value,concat(Markenname," ",Modelname," ",Kilometerstand,"km") as label
from eautos 
join modell on modell.ModellID = eautos.Modell_ModellID 
join marke on marke.MarkeID = eautos.Marke_MarkeID;';
$stmt = $conn -> prepare($autoQuery);
$stmt -> execute();
$autoDaten = $stmt -> fetchAll(PDO::FETCH_ASSOC);

$adresseQuery = 'Select AdresseID as value, concat(Straße," ",Ort," ",PLZ," ",Hausnummer) as label
from Adresse;';
$stmt = $conn -> prepare($adresseQuery);
$stmt -> execute();
$adresseDaten = $stmt -> fetchAll(PDO::FETCH_ASSOC);

/*$adresseQuery = '';
$stmt = $conn -> prepare($adresseQuery);
$stmt -> execute();
$autoDaten = $stmt -> fetchAll(PDO::FETCH_ASSOC);*/
$message ="";
$validation = true;
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sammle und verarbeite hier die Formulardaten
    //Hier bekomme ich den Wert von dem Input-Attribut
    $startadresse = getPostParameter("start");
    $zieladresse = getPostParameter("ziel");
    $autoId = getPostParameter("modell");
    $fahrerId = getPostParameter("fahrer");
    $mitKunden = 1;
    $startkilometerstand = getPostParameter("startkilometer");
    $endkilometerstand = getPostParameter("endkilometer");
    $fahrtbeginn = new DateTime();
    $fahrtbeginn = $fahrtbeginn->format('Y-m-d');
    /*Insert Statement*/
    $fahrtInsertQuery = 'Insert into Fahrt (Startadresse,Zieladresse,AutoID,FahrerInnen_FahrerID,MitKunden, 
    Startkilometerstand,Endkilometerstand,Fahrtbeginn) values(?,?,?,?,?,?,?,?)';

    $message = validateFormCreate($startkilometerstand,$endkilometerstand);
    $validation = $message == "";
    if($validation){
        $stmt = $conn -> prepare($fahrtInsertQuery);
        $insertParameter = [$startadresse,$zieladresse,$autoId,$fahrerId,$mitKunden,$startkilometerstand,$endkilometerstand,$fahrtbeginn];
        $stmt -> execute($insertParameter);
        $success = true;
   

    //Hier bekomme ich den Wert von dem Plz-Attribut
    $endkilometer = getPostParameter("endkilometer","");
    $fahrtbeginn = getPostParameter("fahrtbeginn","");
    $fahrtende = getPostParameter("fahrtende","");


    //Der name der Spalte als Key-Attribut, der Value von name als Value-Atribut. Das ist ein Zwischenschritt, damit man die Daten in die Datenbank speichern kann.
    
    $endkilometerValueDb = ['Endkilometerstand' => $endkilometer];
    $fahrtbeginnValueDb = ['Fahrtbeginn' => $fahrtbeginn];
    $fahrtendeValueDb = ['Fahrtende' => $fahrtende];

    //Vorbereitung zur Speicherung HIER JEWEILIGE ID EINGEBEN
    //$fahrtSuchBedienung = ['FahrtID = ' . $fahrtId];
    //PLZ bleibt gleich, Ort verändert sich HIER AKTUELLE DATEN EINGEBEN

   // update fahrt set (startkilometer) values (100) where fahrt_id = ?
   
   // $updateData =  $endkilometerValueDb +  $fahrtbeginnValueDb + $fahrtendeValueDb;
    //updateRecord($conn, $tableName, $updateData, $fahrtSuchBedienung);
    //PLZ bleibt eigentlich gleich, wird hier aber nocheinmal zugewiesen.
    //showAlertSuccess("Fahrt ist bereits vorhanden. $tableName wurde aktualisiert.");
    //$stateChanged = true;
    //UPDATE RECORD DUPLIZIEREN WENN MAN MEHRERE TABELLEN BRAUCHT und bei tablename die entsprechende Tabelle reinschreiben
    header('Location: index');
    }
    //exit();
}
?>

<div class="card border-0 p-4 container d-flex align-items-center flex-column mt-4 gap-4 shadow">
    <h2>Formular Fahrt erstellen</h2>
    <form action="" method="post">
        <div class="row m-3">
            <label class="col-md-5" for="fahrer">Fahrer</label>
            <?php echo createDropdown('fahrer',$fahrerDaten); ?>
        </div>
        <div class="row m-3">
            <label class="col-md-5" for="modell">Modell</label>
            <?php echo createDropdown('modell',$autoDaten); ?>
        </div>
        <div class="row m-3">
            <label class="col-md-5" for="startadresse">Startadresse</label>
            <?php echo createDropdown('start',$adresseDaten); ?>
        </div>
        <div class="row m-3">
            <label class="col-md-5" for="zieladresse">Zieladresse</label>
            <?php echo createDropdown('ziel',$adresseDaten); ?>
        </div>
        <div class="row m-3">
            <label class="col-md-6" for="startkilometer">Startkilometerstand</label>
            <input type="number" name ="startkilometer" class="form-control" placeholder="Startkilometerstand">
        </div>
        <div class="row m-3">
            <label class="col-md-6" for="endkilometer">Endkilometerstand</label>
            <input type="number" name="endkilometer" class="form-control" placeholder="Endkilometerstand">
        </div>
        <div class="mt-3 justify-content-end d-flex">
            <button type="submit" class="btn btn-success align-items-end">Erstellen</button>
        </div>
        </div>
    </form>
    <div class="card">
<?php if($validation && $success){
    echo showSuccess("Datensatz wurde erfolgreich eingetragen.");
}else if(!$validation){
    echo showAlertWarning($message);
}
?>
</div>