<?php
system("figlet JnckFamz");
require "./cc.class.php";
echo ">> BIN : "; $bin = trim(fgets(STDIN)); // BIN
echo ">> Total : "; $total = trim(fgets(STDIN)); // TOTAL in Numeric
echo ">> RESULT <<"; 
echo "\n";
echo "==============================================";
echo "\n";
$check = $total; // BOOLEAN 1 or 0
$cc = new creditCardGenerator($bin, $total, $check);
$cc->getCC();
?>
