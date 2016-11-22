# Data Manager - Bundle pour Symfony 3
Bundle Symfony, ORM pour les bases de données MySQL flexible et léger. Peut être utilisé en remplacement de Doctrine.

## Methode Diponible

#### public function query($req);
>Lance une requete SQL, retourne le résultat sous la forme d'un tableau associatif

>$req : SQL request

#### public function update($nametable,$array,$id);
Met à jour un enregistrement dans la table
$nametable : String value
$array : array("nameColumn1" => "value1", "nameColumn2" => "value2" , ...)
$id : array("id_columns" => ID)

#### public function insert($nametable,$array);
Insère un nouveau enregistrement dans la table
$nametable : String value
$array : array("nameColumn1" => "value1", "nameColumn2" => "value2" , ...)

#### public function delete($nametable,$id);
Supprimer un enregistrement par l'ID
$nametable : String value
$id : array("id_columns" => ID)

#### public function getLastId();
Récupère le derniere ID inséré

