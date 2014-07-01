<?php
    $print = '';
    $print .= 'Start deploy <br />';
    $print .= 'Current directory: '. shell_exec('echo $PWD') . '<br />';
    $print .= shell_exec('git pull 2>&1');
    $print .= shell_exec('bash grunt-build.sh');
    print_r($print);
?>