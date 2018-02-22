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

class Sea
{
  public $_id;
  public $_name;
  public $_type;
  public $_countrys;
}

function country_have_river($country, $list_river_to_sea) {
    foreach ($list_river_to_sea as $river) {
      if($country->_car_code == $river->_source)
        return True;
    }
    return False;
}

class MySaxHandler extends DefaultHandler {

  //Global variables
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

  public $list_sea = array();
  public $sea_temp;
  public $is_sea = False;
  public $is_sea_name = False;



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
        if($this->is_sea){
            $this->is_sea_name = True;
        }
        break;

      case 'population' :
        if($this->is_country){
          $this->is_country_population = True;
        }
        break;

      case 'river' :
        $this->is_river = True;
        $this->river_temp = new River();
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

      case 'sea' :
        $this->is_sea=True;
        $this->sea_temp = new Sea();

        $this->sea_temp->_id = $att['id'];
        $this->sea_temp->_countrys = $att['country'];
        $this->sea_temp->_type = "inconnu";
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

      case 'sea':
        if($this->is_sea){
          $this->is_sea=False;
          $this->list_sea[] = $this->sea_temp;
        }

        break;
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

    if($this->is_sea_name){
      $this->sea_temp->_name = $txt;
      $this->is_sea_name=False;
    }
  }

  function startDocument() {

  }
  function endDocument() {


    //Séléction des fleuves
    $list_river_to_sea = array();

    foreach ($this->list_river as $river) {
      if(!empty($river->_flow_in_sea) && $river->_flow_in_sea == "sea" && !empty($river->_flow)){
        $list_river_to_sea[] = $river;
      }
    }

    //Séléction des pays
    //Il faut que le pays possède un fleuve qui ai une accès à la mer et pays qui ont un espace maritime
    $list_selected_country = array();

    foreach ($this->list_country as $country_selected) {
      bool addCountry = False;

      foreach ($list_river_to_sea as $river) {
        if($country_selected->_car_code == $river->_source){

        }

      if(){
        $list_selected_country[] = $country_selected;
      }
    }


    $file = fopen('result_sax.xml', 'a+');
    ftruncate($file,0);

    echo "<?xml version='1.0' encoding='UTF-8' ?>\n";
    fputs($file, "<?xml version='1.0' encoding='UTF-8' ?>\n");

		echo "<!DOCTYPE espace-maritime SYSTEM 'em.dtd'>\n";
    fputs($file, "<!DOCTYPE espace-maritime SYSTEM 'em.dtd'>\n");

    echo "<em>\n";
    fputs($file, "<em>\n");

    echo "\t<liste-pays>\n";
    fputs($file, "\t<liste-pays>\n");

    foreach ($this->list_country as $country) {
      if(country_have_river($country, $list_river_to_sea)){
        echo "\t\t<pays id-p=\"{$country->_car_code}\" nom-p=\"{$country->_name}\" superficie=\"{$country->_area}\" nbhab=\"{$country->_population}\">\n";
        fputs($file, "\t\t<pays id-p=\"{$country->_car_code}\" nom-p=\"{$country->_name}\" superficie=\"{$country->_area}\" nbhab=\"{$country->_population}\">\n");

        foreach ($list_river_to_sea as $river) {
          if($country->_car_code == $river->_source){

            echo "\t\t\t<fleuve id-f=\"{$river->_id}\" nom-f=\"{$river->_name}\" longueur=\"{$river->_length}\" se-jette=\"{$river->_flow}\">\n";
            fputs($file, "\t\t\t<fleuve id-f=\"{$river->_id}\" nom-f=\"{$river->_name}\" longueur=\"{$river->_length}\" se-jette=\"{$river->_flow}\">\n");

            $country_list = explode(" ", $river->_countrys);

            if(count($country_list) == 1){
              echo "\t\t\t\t<parcours id-pays=\"{$country_list[0]}\" distance=\"{$river->_length}\"/>\n";
              fputs($file, "\t\t\t\t<parcours id-pays=\"{$country_list[0]}\" distance=\"{$river->_length}\"/>\n");
            }
            else{
              foreach ($country_list as $c) {
                echo "\t\t\t\t<parcours id-pays=\"{$c}\" distance=\"inconnu\"/>\n";
                fputs($file, "\t\t\t\t<parcours id-pays=\"{$c}\" distance=\"inconnu\"/>\n");
              }
            }
            echo "\t\t\t</fleuve>\n";
            fputs($file, "\t\t\t</fleuve>\n");
          }
        }
        echo "\t\t</pays>\n";
        fputs($file, "\t\t</pays>\n");
      }
      else{
        echo "\t\t<pays id-p=\"{$country->_car_code}\" nom-p=\"{$country->_name}\" superficie=\"{$country->_area}\" nbhab=\"{$country->_population}\"/>\n";
        fputs($file, "\t\t<pays id-p=\"{$country->_car_code}\" nom-p=\"{$country->_name}\" superficie=\"{$country->_area}\" nbhab=\"{$country->_population}\"/>\n");
      }

    }
    echo "\t</liste-pays>\n";
    fputs($file, "\t</liste-pays>\n");

    //Espaces martimes
    echo "\t<liste-espace-maritime>\n";
    fputs($file, "\t<liste-espace-maritime>\n");

    foreach ($this->list_sea as $sea) {
      echo "\t\t<espace-maritime id-e=\"{$sea->_id}\" nom-e=\"{$sea->_name}\" type=\"{$sea->_type}\">\n";
      fputs($file, "\t\t<espace-maritime id-e=\"{$sea->_id}\" nom-e=\"{$sea->_name}\" type=\"{$sea->_type}\">\n");

      $country_list = explode(" ", $sea->_countrys);

      foreach ($country_list as $country){
        echo "\t\t\t<cotoie id-p=\"{$country}\"/>\n";
        fputs($file, "\t\t\t<cotoie id-p=\"{$country}\"/>\n");
      }
      echo "\t\t</espace-maritime>\n";
      fputs($file, "\t\t</espace-maritime>\n");
    }
    echo "\t</liste-espace-maritime>\n";
    fputs($file, "\t</liste-espace-maritime>\n");

    echo "</em>\n";
    fputs($file, "</em>\n");
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
}



?>
