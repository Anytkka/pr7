<?php
    function writeToLog($message, $log_file_path = "../../log.txt") {
        $log_date = date("Y-m-d H:i:s");
        $log_ip = $_SERVER['REMOTE_ADDR'];
        
        $log_entry = "[$log_date] [IP: $log_ip] $message\n";
        
        $log_file = fopen($log_file_path, "a");
        if ($log_file) {
            fwrite($log_file, $log_entry);
            fclose($log_file);
            return true;
        }
        return false;
    }
?>