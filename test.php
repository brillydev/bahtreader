<?php
    
    require('./bahtreader');

    $reader = new BathReader;
    echo $reader->$read('1,234,567.89');
?>