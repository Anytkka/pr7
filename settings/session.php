<?php
    if(isset($_SESSION["IdSession"])) {
        $IdSession = $_SESSION["IdSession"];

        $DateNow = date(format: "Y-m-d H:i:s");
        $Sql = "UPDATE `session` SET `DateNow`='{$DateNow}' WHERE `Id` = {$IdSession}";
        $mysqli->query(query: $Sql);
    }
?>