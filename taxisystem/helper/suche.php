<?php


require_once 'conf.php';

function processForm($data) {
    global $conn;
    $querySozialversicherungsnummer = "SELECT CASE WHEN bzr.ter_ende is null 
    THEN  concat(bzr.ter_beginn,' ') 
    ELSE concat(bzr.ter_beginn,' ',bzr.ter_ende) 
    END as Zeitraum,
    concat(per.per_vname,' ',per.per_nname) as Patient,
    concat(per.per_svnr,'/',per.per_geburt) as SVNr,
    dia.dia_name as Diagnose
    from behandlungszeitraum bzr
    left join person per on bzr.per_id = per.per_id
    left join diagnose dia on bzr.dia_id = dia.dia_id
    where concat(per.per_svnr,'/',per.per_geburt) Like ?";
    
    $suchbegriff = $data['suchbegriff'] ?? '';
    $searchTerm = "%$suchbegriff%";
    $startDatumVorhanden = isset($data['date-start']) && !empty($data['date-start']);
    $endDatumVorhanden = isset($data['date-end']) && !empty($data['date-end']);
    $behandlungsbeginnVorhanden = isset($data['suche-in']) && $data['suche-in'] != 'keineAngabe';
    if($behandlungsbeginnVorhanden){
        $Zusatz = " and bzr.ter_beginn >= ?";
        $querySozialversicherungsnummer = $querySozialversicherungsnummer . $Zusatz;
        $startDatum = $data['date-start'];
        $auswahl = $data['suche-in'];
        $data['auswahl'] = $auswahl;
        if($auswahl == 'letzterMonat'){
            $today = new DateTime('first day of this month');
            $today->sub(new DateInterval('P1M'));
            $startDatum = $today->format('Y-m-d');
            $endDatum = new DateTime($startDatum);
            $endDatum -> modify('last day of this month');
            $endDatum = $endDatum->format('Y-m-d');
            $data['enddatum'] = $endDatum;
            
        }
        if($auswahl == 'laufenderMonat'){
            $today = new DateTime('first day of this month');
            $startDatum = $today->format('Y-m-d');
            $endDatum = new DateTime($startDatum);
            $endDatum -> modify('last day of this month');
            $endDatum = $endDatum->format('Y-m-d');
            $data['enddatum'] = $endDatum;
        }
        if($auswahl == 'jahresMonat'){
            $anzahlMonate = $data['wunschMonatNummer'] -1;
            $firstDayOfYear = new DateTime('first day of January this year');
            $period = 'P' . $anzahlMonate . 'M';
            $firstDayOfYear -> add(new DateInterval($period));
            $startDatum = $firstDayOfYear -> format('Y-m-d');
            $endDatum = new DateTime($startDatum);
            $endDatum -> modify('last day of this month');
            $endDatum = $endDatum->format('Y-m-d');
            $data['enddatum'] = $endDatum;
        }

        $data['startdatum'] = $startDatum;
        $_GET = $data;
        $stmt = $conn->prepare($querySozialversicherungsnummer);
        $stmt->execute([$searchTerm,$startDatum]);
        return $stmt;
    }
    if($startDatumVorhanden && $endDatumVorhanden){
       $Zusatz = "and bzr.ter_beginn >= ? and bzr.ter_ende <= ?";
       $querySozialversicherungsnummer = $querySozialversicherungsnummer . $Zusatz;
       $startDatum = $data['date-start'];
       $endDatum = $data['date-end'];
       $stmt = $conn->prepare($querySozialversicherungsnummer);
       $stmt->execute([$searchTerm,$startDatum,$endDatum]);
    }
    else if($startDatumVorhanden && !$endDatumVorhanden){
        $Zusatz = "and bzr.ter_beginn >= ?";
        $querySozialversicherungsnummer = $querySozialversicherungsnummer . $Zusatz;
        $startDatum = $data['date-start'];
        $stmt = $conn->prepare($querySozialversicherungsnummer);
        $stmt->execute([$searchTerm,$startDatum]);
    }
    else if(!$startDatumVorhanden && $endDatumVorhanden){
        $Zusatz = "and bzr.ter_ende <= ?";
        $querySozialversicherungsnummer = $querySozialversicherungsnummer . $Zusatz;
        $endDatum = $data['date-end'];
        $stmt = $conn->prepare($querySozialversicherungsnummer);
        $stmt->execute([$searchTerm,$endDatum]);
    }else{
        $stmt = $conn->prepare($querySozialversicherungsnummer);
        $stmt->execute([$searchTerm]);
    }

    return $stmt;
}