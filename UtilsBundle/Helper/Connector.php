<?php

namespace SG\UtilsBundle\Helper;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Yaml\Yaml;

class Connector
{
    var $host;
    var $user;
    var $mdp;
    var $name;
    
    var $mysqli;
    
    public function __construct(){
        
        $configDirectories = array($_SERVER['DOCUMENT_ROOT'].'/app/config');
        $locator = new FileLocator($configDirectories);
        $ressource = $locator->locate('parameters.yml', null, false);
        
        $configValues = Yaml::parse(file_get_contents($ressource[0]));

        $this->host = $configValues['parameters']['database_host'];
        $this->user = $configValues['parameters']['database_user'];
        $this->mdp = $configValues['parameters']['database_password'];
        $this->name = $configValues['parameters']['database_name'];
        
        
        $this->mysqli = new \mysqli($this->host, $this->user, $this->mdp, $this->name);
        if ($this->mysqli->connect_errno) {
            echo "Echec lors de la connexion à MySQL : (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
            exit;
        }
    }
    
    /**
    * $req : SQL request
    **/
    public function query($req) {
        $res = $this->mysqli->query($req);
        
        if (gettype($res) != "boolean")
        {
            $results_array = array();
            while ($row = $res->fetch_assoc()) {
                $results_array[] = $row;
            }
            
            return $results_array; 
        }
             
    }
    
    /**
    * $nametable : String value
    * $array : array("nameColumn1" => "value1", "nameColumn2" => "value2" , ...)
    * $id : array("id_columns" => ID)
    **/
    public function update($nametable,$array,$id)
    {
        $chaine = "";

        $bindChaine = "";
        $bindparam = "";
        foreach ($array as $key => $value)
        {
            if ($chaine == "")
            {
                $chaine .= $key.' = ?';

                $bindChaine .= '$array[\''.$key.'\']';
            }
            else
            {
                $chaine .= ', '.$key.' = ?';

                $bindChaine .= ',$array[\''.$key.'\']';
            }
            $bindparam .= 's';
        }

        $req = "UPDATE ".$nametable." SET ".$chaine." WHERE ".array_keys($id)[0]."='".array_values($id)[0]."'";
        $stmt = $this->mysqli->prepare($req);

        if (!$stmt)
            echo "Echec de la préparation : (" . $this->mysqli->errno . ") " . $this->mysqli->error;

        $execute = '$stmt->bind_param("'.$bindparam.'", '.$bindChaine.');';

        eval($execute);
        $stmt->execute();
    }
    
    /**
    * $nametable : String value
    * $array : array("nameColumn1" => "value1", "nameColumn2" => "value2" , ...)
    **/
    public function insert($nametable,$array)
    {
        $chaine = "";
        $interro = "";
        $bindChaine = "";
        $bindparam = "";
        foreach ($array as $key => $value)
        {
            if ($chaine == "")
            {
                $chaine .= $key;
                $interro .= '?';
                
                $bindChaine .= '$array[\''.$key.'\']';
            }
            else
            {
                $chaine .= ','.$key;
                $interro .= ',?';
                
                $bindChaine .= ',$array[\''.$key.'\']';
            }
            $bindparam .= 's';
        }
        
        $req = "INSERT INTO ".$nametable."(".$chaine.") VALUES (".$interro.")";
        $stmt = $this->mysqli->prepare($req);
        
        if (!$stmt)
            echo "Echec de la préparation : (" . $this->mysqli->errno . ") " . $this->mysqli->error;
        
        $execute = '$stmt->bind_param("'.$bindparam.'", '.$bindChaine.');';

        eval($execute);
        $stmt->execute();
    }
    
    /**
    * $nametable : String value
    * $id : array("id_columns" => ID)
    **/
    public function delete($nametable,$id)
    {
        $req = "DELETE FROM ".$nametable." WHERE ".array_keys($id)[0]." = ?";
        $stmt = $this->mysqli->prepare($req);
        $stmt->bind_param('i', array_values($id)[0]);
        $stmt->execute(); 
        $stmt->close();
    }
    
    public function getLastId() {
        return $this->mysqli->insert_id;
    }
    
    
}
