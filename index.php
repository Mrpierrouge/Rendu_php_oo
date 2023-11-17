<?php 
abstract class Utiles{
    static public function generateRandom($min, $max){
        return rand($min, $max);
    }
}

class Jeux{
    private static $ListeEnnemi = array();
    private static $ListePersonnages = array();
    private static $personnage;
    public static $levels;
    static private function createHero(){    //créer les 3 héros possible et les ajoutes a la liste des personnage jouables ($ListePersonnages)
        array_push(self::$ListePersonnages, new Hero("Seong Gi-hun", 15, 1, 2));
        array_push(self::$ListePersonnages, new Hero("Kang Sae-byeok", 25, 2, 1));
        array_push(self::$ListePersonnages, new Hero("Cho Sang-woo", 35, 3, 0));
    }
    static private function createEnnemi($nb){    //créer un nombre $nb d'ennemi a afronter et les ajoutes a la liste des ennemis ($ListeEnnemi)
        for ($i=0; $i < $nb; $i++) { 
            array_push(self::$ListeEnnemi, new Ennemi("Ennemi" . $i, Utiles::generateRandom(1, 20), Utiles::generateRandom(18,90)));
            
        }
    }
    static public function choixDifficulte(){       //Le choix de la difficulté se fait aléatoirement entre les 3 nvx, chacun correspondant a un nombres d'ennemis a vaincre
        $tirage = Utiles::generateRandom(1,3);
        if ($tirage == 1) {
            self::$levels = 5;
        }
        if ($tirage == 2) {
            self::$levels = 10;
        }
        if ($tirage == 3) {
            self::$levels = 20;
        }
    }
    static public function choixHero(){
        self::$personnage = self::$ListePersonnages[array_rand(self::$ListePersonnages)];
        echo "Vous incarnez ". self::$personnage->name ."<br>";
    }
    
    static private function rencontre($ennemi){
        while($ennemi->billes > 0 && self::$personnage->billes > 0){
            if(self::$personnage->combats($ennemi)){
                unset(self::$ListeEnnemi[array_search($ennemi, self::$ListeEnnemi)]);
            }
        }
        
    }
    
    
    static public function jouer(){
        self::choixDifficulte();                    //Je choisi la difficulté

        self::$ListeEnnemi = [];                    //je revide la lsite d'ennemi et en recréer un nombre correspondant a la difficulté
        self::createEnnemi(self::$levels);

        self::$ListePersonnages = [];               //je revide la liste des personnage jouables puis les recréer, j'en séléctionne alors un au hasard
        self::createHero();
        self::choixHero();
        
        while (self::$personnage->billes > 0 && count(self::$ListeEnnemi) > 0) {        //tant que le héro possède des billes et qu'il y a des ennemis a vaincres, je lance un match entre le héro et un ennemi aléatoire
            self::rencontre(self::$ListeEnnemi[array_rand(self::$ListeEnnemi)]);
        }
        if (count(self::$ListeEnnemi)== 0) {
            echo " Bravo, vous remportez le squid game !";
        }
    }
    
}

class Personnage{
    public $billes;
    public $name;

    public function getBilles(){
        return $this->billes;
    }
    public function getName(){
        return $this->name;
    }
}

class Hero extends Personnage{
    private $pari;
    private function GetPari(){
        if($this->pari == 0){
            return "Pair";
        }
        elseif($this->pari == 1){
            return "Impair";
        }
    }

    public function __construct($name, $billes, $bonus, $malus){
        $this->name = $name;
        $this->billes = $billes;
        $this->bonus = $bonus;
        $this->malus = $malus;
    }
    public function combats($ennemi){
        echo "vous avez $this->billes billes et l'adversaire $ennemi->billes<br>";

        $ennemi->ChoixBilles();
        $this->choixPari();

        echo "l'ennemi avait misé $ennemi->choixBilles billes <br>";

        if($this->checkPari($ennemi)){
            $this->billes += $ennemi->choixBilles + $this->bonus;
            $ennemi->billes = 0;
            echo " Victoire ! Vous gagnez $ennemi->choixBilles + $this->bonus billes et l'ennemi est éliminé<br>";
            return true;
        }
        else{
            $this->billes -= ($ennemi->choixBilles + $this->malus);
            $ennemi->billes += $ennemi->choixBilles;
            echo " Défaite !";
        }
        if($this->billes < 0){
            echo " Vous n'avez plus de billes";
        }
        else{
            echo "<br> Il vous reste $this->billes billes <br>";
        }
        
    }
    
    public function choixPari(){
        $this->pari = Utiles::generateRandom(0,1);

        echo "Vous avez parié " . $this->GetPari() . "<br>";
    }
    private function checkPari($ennemi){
        if (($ennemi->choixBilles)%2 == $this->pari){
            return true;
        }
        else {
            return false;
        }
    }
}
class Ennemi extends Personnage{
    public $choixBilles;
    public function __construct($name, $billes, $age){
        $this->name = $name;
        $this->billes = $billes;
        $this->age = $age;
    }
    public function ChoixBilles(){
        $this->choixBilles = Utiles::generateRandom(1,$this->billes);
    }
}



echo Jeux::jouer();
?>