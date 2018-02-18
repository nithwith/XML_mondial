<?php header('Content-type: text/xml');
include_once('Sax4PHP/Sax4PHP.php');

class Country
{
  public $_name;
  public $_car_code;
  public $_area;
  public $_population;
}

class River
{
  public $_id;
  public $_name;
  public $_length;
  public $_flow; //Se jette dans
  public $_countrys;
  public $_flow_in_sea;
  public $_source; //car_code
}


class MySaxHandler extends DefaultHandler {

  public $list_country = array();
  public $country_temp;
  public $is_country = False;
  public $is_country_name = False;
  public $is_country_population = False;

  public $list_river = array();
  public $river_temp;
  public $is_river = False;
  public $is_river_name = False;
  public $is_river_to = False;
  public $is_river_length = False;

  function startElement($nom, $att) {
    switch($nom) {
  		case 'country' :
        $this->is_country = True;
        $this->country_temp = new Country();
        $this->country_temp->_car_code = $att['car_code'];
        $this->country_temp->_area = $att['area'];
  			break;

  		case 'name' :
        if($this->is_country){
          if(empty($this->country_temp->_name))
          $this->is_country_name = True;
        }
        if($this->is_river){
          if(empty($this->river_temp->_name))
          $this->is_river_name = True;
        }
        break;

      case 'population' :
        if($this->is_country){
          $this->is_country_population = True;
        }
        break;

      case 'river' :
        $this->is_river = True;
        $this->river_temp = new Country();
        $this->river_temp->_id = $att['id'];
        $this->river_temp->_countrys = $att['country'];
        break;
      case 'to' :
        if($this->is_river){
          $this->river_temp->_flow_in_sea = $att['watertype'];
          $this->river_temp->_flow = $att['water'];
        }
        break;
      case 'length' :
      if($this->is_river){
        $this->is_river_length = True;
        }
        break;
      case 'source' :
        if($this->is_river){
          $this->river_temp->_source = $att['country'];
        }
        break;
  		default :;
  	}
  }

  function endElement($nom) {
    switch($nom) {
  		case 'country':
        $this->list_country[] = $this->country_temp;
        break;
      case 'name':
        $this->is_country_name=False;
        break;
      case 'population_growth' :
        $this->is_country=False;
        break;
      case 'river':
        $this->is_river=False;
        $this->list_river[] = $this->river_temp;
        break;
      case 'to':
        $this->is_river_to=False;
    }
  }

  function characters($txt) {
    if($this->is_country_name){
      $this->country_temp->_name = $txt;
      $this->is_country_name=False;
    }
    if($this->is_country_population){
      $this->country_temp->_population = $txt;
      $this->is_country_population=False;
    }

    if($this->is_river_name){
      $this->river_temp->_name = $txt;
      $this->is_river_name=False;
    }
    if($this->is_river_length){
      $this->river_temp->_length = $txt;
      $this->is_river_length=False;
    }
  }

  function startDocument() {
    echo "<?xml version='1.0' encoding='UTF-8' ?>\n"; //<?php
		echo "<!DOCTYPE espace-maritime SYSTEM 'em.dtd'>\n";
		echo "<em>\n";
  }
  function endDocument() {
    echo "</em>\n";

    foreach ($this->list_country as $country) {
      //echo "<country ip_p={$country->_car_code} nom_p={$country->_name} superficie={$country->_area} nbhab={$country->_population}>\n \n" ;
      echo "country : carcode={$country->_car_code} nom_p={$country->_name} sup={$country->_area} pop={$country->_population}\n";
    }
    foreach ($this->list_river as $river) {
      if(!empty($river->_flow_in_sea) && $river->_flow_in_sea == "sea" && !empty($river->_flow)){
        echo "river : id={$river->_id} nom={$river->_name} longueur={$river->_length} se-jette={$river->_flow} countrys={$river->_countrys} source={$river->_source}\n";
      }
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
