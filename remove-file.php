<?php
    require_once 'lib/Util.class.php';
    unlink(Util::getRootDir() . $_POST['file']);