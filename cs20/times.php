<?php
$n = isset($_GET['n']) ? intval($_GET['n']) : 1;

for ($i = 1; $i <= 15; $i++) {
    echo "$i x $n = " . ($i * $n) . "<br>";
}
?>