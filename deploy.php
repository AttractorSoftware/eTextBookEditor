<?php
    $print = '';
    $print .= 'Start deploy <br />';
    $print .= 'Current directory: '. shell_exec('echo $PWD') . '<br />';
    $print .= shell_exec('git reset --hard');
    $print .= shell_exec('git pull');
?>

<!Doctype html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Deploy</title>
    </head>
    <body>
        <?php print_r($print); ?>
    </body>
</html>