<?php

class GrandTest {
    public $nom;
}

class Test extends GrandTest {
    public function __construct($billes) {
        $this->billes = $billes;
    }
}

$Moi = new Test(25);
$Moi->nom;
$numbers = array(1, 2, 3, 4, 5, 6);
array_push($numbers, 1);
var_dump($numbers);