<?php
require __DIR__ . '/counter/counter.php';

// add IP address if it doesn't exist in list
addUniqueIP();

// print unique visitors
echo "Visiteurs : " . getUniqueVisitor();
