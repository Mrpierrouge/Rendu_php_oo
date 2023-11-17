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
        self::$personnage = self::$ListePersonnages[array_rand(self::$ListePersonnages)];       //Le choix du héros se fait aléatoirement entre tout les héro dans la liste des personnages incarnables
        echo "Vous incarnez ". self::$personnage->name ."<br>";
    }
    
    static private function rencontre($ennemi){                         //la méthode rencontre prend un argument, l'ennemi que doit combattre le héro. 
        while($ennemi->billes > 0 && self::$personnage->billes > 0){    //tant que l'ennemi et le héro ont des billes, je lance la méthode combat de la class Hero
            if(self::$personnage->combats($ennemi)){                    //si cette méthode retourne true (donc si le héro gagne) alors je retire l'ennemi vaincu de la liste d'ennemi a vaincre
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
        }                                                                               // SI il n'y a plus d'ennemi a vaincre, alors le héro remporte le jeux
        if (count(self::$ListeEnnemi)== 0) {
            echo " Bravo, vous remportez le squid game !";
        }
    }
    
}

class Personnage{
    public $billes;
    public $name;

}

class Hero extends Personnage{
    private $pari;
    public function __construct($name, $billes, $bonus, $malus){
        $this->name = $name;
        $this->billes = $billes;
        $this->bonus = $bonus;
        $this->malus = $malus;
    }
    private function GetPari(){         //fonction pour afficher sous forme de string le pari qui est sous forme de int (0 ou 1)
        if($this->pari == 0){
            return "Pair";
        }
        elseif($this->pari == 1){
            return "Impair";
        }
    }

    public function combats($ennemi){       //fonction gérant une rencontre entre un ennemi et le héro.
        echo "vous avez $this->billes billes et l'adversaire $ennemi->billes<br>";          //j'affiche les billes des 2 opposants

        $ennemi->ChoixBilles();             //J 'effectue les choix nécessaire (nb de billes en jeu, tricher ou non et pari)

        if ($ennemi->age > 70) {
            echo "L'ennemi est plutot agé...";
            if ($this->choixTriche()) {         //Si l'ennemi a 70 ans et que le héro décide de tricher, je fait gagner au héro le nb de billes en jeu et fait passer le nb de billes de l'ennemi a 0, puis retourne True
                $this->billes += $ennemi->choixBilles;
                $ennemi->billes = 0;
                echo " Vous décidez de tricher et remportez $ennemi->choixBilles billes. L'ennemi est éliminé... <br>";
                return true;
            }
            else {
                echo "mais vous décidez de ne pas tricher <br>";
            }
        }
        $this->choixPari();

        echo "l'ennemi avait misé $ennemi->choixBilles billes <br>";    //les choix étant fait, j'affiche la réponse du nb de billes

        if($this->checkPari($ennemi)){              //Si le pari est le bon, je fait gagner au héro le nb de billes en jeu + son bonus et fait passer le nb de billes de l'ennemi a 0, puis retourne True
            $this->billes += $ennemi->choixBilles + $this->bonus;
            $ennemi->billes = 0;
            echo " Victoire ! Vous gagnez $ennemi->choixBilles + $this->bonus billes et l'ennemi est éliminé<br>";
            return true;
        }
        else{                                       //Si le pari n'est pas le bon, le héro perd le nb de billes en jeu + son malus, l'ennemi gagne le nb de billes en jeu
            $this->billes -= ($ennemi->choixBilles + $this->malus);
            $ennemi->billes += $ennemi->choixBilles;
            echo " Défaite !";
        }
        if($this->billes < 0){                      // J'affiche le nb de billes restantes au héro
            echo " Vous n'avez plus de billes";
        }
        else{
            echo "<br> Il vous reste $this->billes billes <br>";
        }
        
    }
    public function choixTriche(){
        if (Utiles::generateRandom(0,1) == 1) {     //1 chance sur 2 de tricher
            return true;
        }
    }
    public function choixPari(){        //le choix du pari est fait de manière aléatoire entre 0 et 1 (pair ou impair). J'affiche ensuite le pari fait
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
    public function ChoixBilles(){          //choisi un nb de billes a mettre en jeu aléatoire entre 1 et le nb de billes posséder
        $this->choixBilles = Utiles::generateRandom(1,$this->billes);
    }
}


echo Jeux::jouer();
?>