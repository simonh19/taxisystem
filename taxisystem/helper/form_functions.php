<?php

function showAlertWarning($message) {
    echo '<div class="alert alert-warning" role="alert">';
    echo $message;
    echo '</div>';
}

function createDropdown($name, $options, $selectedValue = null) {
    $html = "<select class='form-control' name='$name' id='$name'>";
    foreach ($options as $option) {
        $value = $option['value'];
        $label = $option['label'];
        $selected = ($value == $selectedValue) ? 'selected' : '';
        $html .= "<option value='$value' $selected>$label</option>";
    }
    $html .= "</select>";
    return $html;
}

//Immer wenn ich etwas von einem Formular(Post-Formular) haben will, dann verwende ich diese Funktion
function getPostParameter($paramName, $defaultValue = '') {
    return isset($_POST[$paramName]) ? trim($_POST[$paramName]) : $defaultValue;
}

function getUrlParam($urlParam)
{
    $separator = "=";
    if (str_contains($urlParam, $separator)) {
        $paramArray = explode($separator, $urlParam);
        return $paramArray[1];

    } else return [];
}

function getUrlParamName($urlParam)
{
    $separator = "=";
    if (str_contains($urlParam, $separator)) {
        $paramArray = explode($separator, $urlParam);
        return $paramArray[0];

    } else return [];
}

function executeQuery($conn, $query){
    $stmt = $conn->prepare($query);
    $stmt->execute();

    return $stmt;
}

function createRadioButtons($name, $options, $checkedValue = null)
{
    $html = '';
    foreach ($options as $value => $label) {
        $checked = ($value == $checkedValue) ? 'checked' : '';
        $html .= "<div class='form-check'>";
        $html .= "<input class='form-check-input' type='radio' name='$name' id='$value' value='$value' $checked>";
        $html .= "<label class='form-check-label' for='$value'>$label</label>";
        $html .= "</div>";
    }
    return $html;
}

function showSuccess($message) {
    echo '<div class="alert alert-success" role="alert">';
    echo $message;
    echo '</div>';
}
