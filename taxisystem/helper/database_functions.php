<?php

function validateForm($startkilometer,$endkilometer,$fahrtbeginn, $fahrtende){
    $kilometerCheck = $startkilometer >= 0 && $endkilometer > 0 && $endkilometer > $startkilometer;
    if(!$kilometerCheck){
        return "Kilometeranzahl ist ungültig";
    }
    $jetzt = new DateTime();
    $fahrtbeginnDt = new DateTime($fahrtbeginn);
    $fahrtendeDt = new DateTime($fahrtende);
    $fahrtCheck = $fahrtbeginnDt < $fahrtendeDt && $fahrtbeginnDt > $jetzt && $fahrtendeDt > $jetzt;
    if(!$fahrtCheck){
        return "Zeitraum ist ungültig";
    }
    return "";
}

function validateFormCreate($startkilometer,$endkilometer){
    $kilometerCheck = $startkilometer >= 0 && $endkilometer > 0 && $endkilometer > $startkilometer;
    if(!$kilometerCheck){
        return "Kilometeranzahl ist ungültig";
    }
    return "";
}


function getValues($conn, $table, $column) {
    $sql = "SELECT $column FROM $table order by $column";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    // wird die daten in einem array speichern
    $values = $stmt->fetchAll(PDO::FETCH_COLUMN);

    return $values;
}

function getValue($conn, $table, $column, $conditionColumn, $conditionValue) {
    $sql = "SELECT $column FROM $table WHERE $conditionColumn = :conditionValue LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':conditionValue', $conditionValue);
    $stmt->execute();
    $value = $stmt->fetch(PDO::FETCH_COLUMN);
    return $value;
}

function recordExists($conn, $table, $column, $value) {
    $sql = "SELECT COUNT(*) FROM $table WHERE $column = :value";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':value', $value);
    $stmt->execute();
    $count = $stmt->fetchColumn();
    return $count > 0;
}

//UPDATE
function updateRecord($conn, $table, $data, $conditions) {
    try {
        $query = "UPDATE $table SET ";
        $setPart = [];
        foreach ($data as $key => $value) {
            $setPart[] = "$key = :$key";
        }
        $query .= implode(', ', $setPart);
        $query .= ' WHERE ' . implode(' AND ', $conditions);
        $stmt = $conn->prepare($query);

        foreach ($data as $key => &$val) {
            $stmt->bindParam(":$key", $val);
        }
        $stmt->execute();
        return $stmt->rowCount();
    } catch(PDOException $e) {    
        echo showAlertWarning('Update Error: ' . $e->getMessage()); 
    }
}

function addRecord($conn, $table, $data) {
    $columns = implode(", ", array_keys($data));
    $placeholders = implode(", ", array_fill(0, count($data), '?'));

    $stmt = $conn->prepare("INSERT INTO $table ($columns) VALUES ($placeholders)");

    $stmt->execute(array_values($data));

    $lastId = $conn->lastInsertId();

    return $lastId;
}

//DELETE
function deleteRecord($conn,$table, $idColumn, $idValue)
{
    try {
        $deleteQuery = "DELETE FROM $table WHERE " . $idColumn . "=" .$idValue;

        $preparedStmt = $conn->prepare($deleteQuery);
        $preparedStmt->execute();

        return $preparedStmt->rowCount();
    } catch (PDOException $exception) {
        echo 'Database Delete Error: ' . $exception->getMessage();
    }
}

function generateTableFromQuery($conn, $stmt, $idColumnName, $tableName)
{

    // beginne mit dem erstellen der Tabelle
    $table = '<table class="table mt-3">';

    // Kopfzeile mit Spaltennamen aus der Query generieren
    $table .= '<thead><tr>';
    for ($i = 0; $i < $stmt->columnCount(); $i++) {
        $columnMeta = $stmt->getColumnMeta($i);
        if ($columnMeta['name'] != $idColumnName) {
            $table .= '<th>' . htmlspecialchars($columnMeta['name']) . '</th>';
        }
    }
    $table .= '</tr></thead>';

    // datenzeilen generieren
    $table .= '<tbody>';
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $table .= '<tr>';
        foreach ($row as $columnName => $cell) {
            if ($columnName != $idColumnName) {
                $cell = $cell ? $cell : '';
                $table .= '<td>' . htmlspecialchars($cell) . '</td>';
            }
        }

        // Bearbeiten und Löschen Buttons mit der ID des Eintrags HIER SEITE ANPASSEN
        $table .= "<td><a href='index.php?site=formular-einsteigen?edit_id=" . $row[$idColumnName] . "' class='btn btn-success'>Einsteigen</a></td>";
        $table .= "<td><a href='index.php?site=helper/delete?" . $idColumnName . "=" . $row[$idColumnName] . "?table=" . $tableName . "' class='btn btn-danger' onclick='return confirm(\"Wollen Sie den Eintrag wirklich löschen?\");'>Löschen</a></td>";

        $table .= '</tr>';
    }
    $table .= '</tbody>';

    // schließe die Tabelle
    $table .= '</table>';

    return $table;
}