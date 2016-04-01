<?php

/*
 * All rights reserved.
 * 
 * Copyright (c) 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
 */

    namespace SycamoreTest;

    error_reporting(E_ALL | E_STRICT);
    chdir(__DIR__);
    
    require dirname(__DIR__) . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "sycamore.constants.php";
    
    require "./Bootstrap.php";
    require "./TestHelpers.php";
    
    Bootstrap::init();
    Bootstrap::chroot();