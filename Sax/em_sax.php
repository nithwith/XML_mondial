<?php header('Content-type: text/xml');
include_once('Sax4PHP/Sax4PHP.php');

class Pays
{
  public $_nom;
  public $_car_code;
  public $_superficie;
  public $_nbhab;
}

class Fleuve
{
  public $_id;
  public $_nom;
  public $_longueur;
  public $_sejette;
}

class MySaxHandler extends DefaultHandler {

  public $liste_pays = array();
  public $pays_temp;
  public $is_country = False;
  public $is_country_name = False;
  public $is_country_population = False;

  function startElement($nom, $att) {
    switch($nom) {
  		case 'country' :
        $this->is_country = True;
        $this->pays_temp = new Pays();
        $this->pays_temp->_car_code = $att['car_code'];
        $this->pays_temp->_superficie = $att['area'];


  			break;

  		case 'name' :
        if($this->is_country){
          if(empty($this->pays_temp->_nom))
          $this->is_country_name = True;
        }
        break;

      case 'population' :
        if($this->is_country){
          $this->is_country_population = True;
        }
        break;
  		default :;
  	}
  }

  function endElement($nom) {
    switch($nom) {
  		case 'country':

        $this->liste_pays[] = $this->pays_temp;
        break;
      case 'name':
        $this->is_country_name=False;
        break;
      case 'population_growth' :
        $this->is_country=False;
        break;
    }
  }

  function characters($txt) {
    if($this->is_country_name){
      $this->pays_temp->_nom = $txt;
      $this->is_country_name=False;
    }
    if($this->is_country_population){
      $this->pays_temp->_nbhab = $txt;
      $this->is_country_population=False;
    }
  }

  function startDocument() {
    echo "<?xml version='1.0' encoding='UTF-8' ?>\n"; //<?php
		echo "<!DOCTYPE espace-maritime SYSTEM 'em.dtd'>\n";
		echo "<em>\n";
  }
  function endDocument() {
    echo "</em>\n";

    foreach ($this->liste_pays as $pays) {
      //echo "<pays ip_p={$pays->_car_code} nom_p={$pays->_nom} superficie={$pays->_superficie} nbhab={$pays->_nbhab}>\n \n" ;
      echo "carcode={$pays->_car_code}  nom_p={$pays->_nom} pop ={$pays->_nbhab}\n";
    }
  }
}

$xml = file_get_contents('../Mondial2015/XML/mondial.xml');
$sax = new SaxParser(new MySaxHandler());
try {
	$sax->parse($xml);
}catch(SAXException $e){
	echo "\n",$e;
}catch(Exception $e) {
	echo "Default exception >>", $e;
}?>
