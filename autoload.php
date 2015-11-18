<?php

spl_autoload_register(function($className) {
    $fileName = __DIR__ .'/' .$className .'.php';
    if (file_exists($fileName)) {
        include($fileName);
    }
});
