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

  function startElement($nom, $att) {
    switch($nom) {
  		case 'country' :

        $pays = new Pays();
        $pays->_car_code = $att['car_code'];
        $pays->_superficie = $att['car_code'];

        $this->liste_pays[] = $pays;

        echo "$car_code \n";
  			break;
  		case 'titre' :
        $this->texte = '';
        break;
  		default :;
  	}
  }

  function endElement($nom) {

  }

  function characters($txt) {
    $txt = trim($txt);
    if (!(empty($txt))) $this->texte .= $txt;
  }

  function startDocument() {
    echo "<?xml version='1.0' encoding='UTF-8' ?>\n"; //<?php
		echo "<!DOCTYPE espace-maritime SYSTEM 'em.dtd'>\n";
		echo "<em>\n";
  }
  function endDocument() {
    echo "</em>\n";

    foreach ($this->liste_pays as $pays) {
      echo "$pays->_car_code \n";
    }
  }
}

$xml = file_get_contents('Mondial2015/XML/mondial.xml');
$sax = new SaxParser(new MySaxHandler());
try {
	$sax->parse($xml);
}catch(SAXException $e){
	echo "\n",$e;
}catch(Exception $e) {
	echo "Default exception >>", $e;
}?>
