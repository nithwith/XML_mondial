<?php
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
  public $_source; //car_code
}

class Sea
{
  public $_id;
  public $_name;
  public $_type;
  public $_countrys;
}

if (file_exists('../Mondial2015/XML/mondial.xml')) {
    $xml = simplexml_load_file('../Mondial2015/XML/mondial.xml');
}

$list_country = array();
$list_river = array();
$list_sea = array();
// $country_temp;
// $river_temp;
// $sea_temp;

//PARSING

foreach ($xml->country as $country) {
  $country_temp = new Country();
  $country_temp->_name = $country->name;
  $country_temp->_car_code =  $country->attributes()["car_code"];
  $country_temp->_population =  end($country->population);
  $country_temp->_area = $country->attributes()["area"];
  $list_country[] = $country_temp;
}

foreach ($xml->river as $river) {
  $river_temp = new River();
  $river_temp->_id =  $river->attributes()["id"];
  $river_temp->_name =  $river->name;
  $river_temp->_length =  $river->length;
  $river_temp->_countrys = $river->attributes()["country"];
  $river_temp->_source = $river->source->attributes()["country"];

  if(isset($river->to))
  {
    $river_temp->_flow =  $river->to->attributes()[1];
    if($river->to->attributes()[0] == "sea"){
      $list_river[] = $river_temp;
    }
  }
}

foreach ($xml->sea as $sea) {
  $sea_temp = new Sea();
  $sea_temp->_name = $sea->name;
  $sea_temp->_id =  $sea->attributes()["id"];
  $sea_temp->_name =  $sea->name;
  $sea_temp->_countrys = $sea->attributes()["country"];
  if(strpos($sea_temp->_name,"Sea")){$sea_temp->_type ="mer";}
  elseif(strpos($sea_temp->_name,"Ocean")){$sea_temp->_type ="océan";}
  else{$sea_temp->_type ="inconnu";}
  $list_sea[] = $sea_temp;
}

  display($list_country, $list_river, $list_sea);




function display($list_country, $list_river, $list_sea){

  $list_country_selected = select_country($list_river, $list_sea);

  echo "select :".count($list_country_selected). "\n";
  echo "country :".count($list_country). "\n";
  echo "river :".count($list_river). "\n";



  $file = fopen('result_simple.xml', 'a+');
  ftruncate($file,0);

  fputs($file, "<?xml version='1.0' encoding='UTF-8' ?>\n");
  fputs($file, "<!DOCTYPE espace-maritime SYSTEM 'em.dtd'>\n");
  fputs($file, "<em>\n");
  fputs($file, "\t<liste-pays>\n");

  foreach ($list_country as $country) {
    if(in_array($country->_car_code, $list_country_selected)){

      if(country_have_river($country->_car_code, $list_river)){

        fputs($file, "\t\t<pays id-p=\"{$country->_car_code}\" nom-p=\"{$country->_name}\" superficie=\"{$country->_area}\" nbhab=\"{$country->_population}\">\n");

        foreach ($list_river as $river) {

          if(strcmp($country->_car_code, $river->_source) == 0){
            fputs($file, "\t\t\t<fleuve id-f=\"{$river->_id}\"
             nom-f=\"{$river->_name}\"
             longueur=\"{$river->_length}\"
             se-jette=\"{$river->_flow}\">\n");
            $country_list = explode(" ", $river->_countrys);
            if(count($country_list) == 1){
              fputs($file, "\t\t\t\t<parcourt id-pays=\"{$country_list[0]}\" distance=\"{$river->_length}\"/>\n");
            }
            else{
              foreach ($country_list as $c) {
                fputs($file, "\t\t\t\t<parcourt id-pays=\"{$c}\" distance=\"inconnu\"/>\n");
              }
            }
            fputs($file, "\t\t\t</fleuve>\n");
          }
        }
        fputs($file, "\t\t</pays>\n");
      }
      else{
        fputs($file, "\t\t<pays id-p=\"{$country->_car_code}\" nom-p=\"{$country->_name}\" superficie=\"{$country->_area}\" nbhab=\"{$country->_population}\"/>\n");
      }
    }
  }
  fputs($file, "\t</liste-pays>\n\n");

  //Espaces martimes
  fputs($file, "\t<liste-espace-maritime>\n");


  foreach ($list_sea as $sea) {
    fputs($file, "\t\t<espace-maritime id-e=\"{$sea->_id}\" nom-e=\"{$sea->_name}\" type=\"{$sea->_type}\">\n");

    $country_list = explode(" ", $sea->_countrys);

    foreach ($country_list as $country){
      fputs($file, "\t\t\t<cotoie id-p=\"{$country}\"/>\n");
    }
    fputs($file, "\t\t</espace-maritime>\n");
  }
  fputs($file, "\t</liste-espace-maritime>\n");
  fputs($file, "</em>\n");
}


function country_have_river($car_code, $list_river) {
  foreach ($list_river as $river) {
    if(strcmp($car_code, $river->_source) == 0){
        return True;
        break;
    }
  }
  return False;
}

//Séléction des pays
function select_country($list_river, $list_sea){
  //Pour être affiché un pays doit avoir 2 caractéristique
  //Doit être traverssé par un fleuve
  //Doit faire parti d'un espace maritime
  $list_country_selected = array();

  foreach ($list_river as $river) {
    $countrys_of_river = explode(" ", $river->_countrys);
    foreach ($countrys_of_river as $c) {
      $list_country_selected[] = $c;
    }
  }

  foreach ($list_sea as $sea) {
    $countrys_of_sea = explode(" ", $sea->_countrys);
    foreach ($countrys_of_sea as $c) {
      $list_country_selected[] = $c;
    }
  }
  $list_country_selected = array_unique($list_country_selected);
  return $list_country_selected;
}

?>
