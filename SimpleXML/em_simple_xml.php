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

result_dom($list_country, $list_river, $list_sea);

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

//Export avec DOM
function result_dom($list_country, $list_river, $list_sea){

  $imp = new DOMImplementation;
  $dtd = $imp->createDocumentType('em', '', 'em.dtd');
  $resultXML = new DOMDocument('1.0', 'utf-8');
  $resultXML->appendChild($dtd);

  // AutoIndent le code
  $resultXML->preserveWhiteSpace = false;
  $resultXML->formatOutput = true;

  // Création de la racine
  $racine = $resultXML->createElement('em');

  //Ajout du conten
  $racine->appendChild(create_content_dom($list_country, $list_river, $list_sea, $resultXML ));

  $resultXML->appendChild($racine);

  $resultXML->save('result_simple_xml_dom.xml');
}

function create_content_dom($list_country, $list_river, $list_sea, $doctmp ) {
  $res = $doctmp->createElement('liste-pays');

  $list_country_selected = select_country($list_river, $list_sea);

  foreach ($list_country as $c) {
    if(in_array($c->_car_code, $list_country_selected)){
      $country =  $doctmp->createElement('pays');

      $country->setAttribute('id-p', $c->_car_code);
      $country->setAttribute('nom-p', $c->_name);
      $country->setAttribute('superficie', $c->_area);
      $country->setAttribute('nbhab', $c->_population);

      foreach ($list_river as $r) {
        if(strcmp($c->_car_code, $r->_source) == 0){
          $fleuve = $doctmp->createElement('fleuve');

          $fleuve->setAttribute('id-f', $r->_id);
          $fleuve->setAttribute('nom-f', $r->_name);
          $fleuve->setAttribute('longueur', $r->_length);
          $fleuve->setAttribute('se-jette', $r->_flow);

          $splited = explode(" ", $r->_countrys);
            foreach ($splited as $car_code) {
                $parcourt = $doctmp->createElement('parcourt');
                $parcourt->setAttribute('id-pays', $car_code);

                if (sizeof($splited) == 1) {
                  $parcourt->setAttribute('distance', $r->_length);
                } else {
                  $parcourt->setAttribute('distance', 'inconnu');
                }
                $fleuve->appendChild($parcourt);
            }
            $country->appendChild($fleuve);
          }
        }
        $res->appendChild($country);
      }
    }

    $em = $doctmp->createElement('liste-espace-maritime');

    foreach ($list_sea as $s) {
      $sea = $doctmp->createElement('espace-maritime');

      $sea->setAttribute('id-e',$s->_id);
      $sea->setAttribute('nom-e',$s->_name);
      $sea->setAttribute('type',$s->_type);

      $splited = explode(" ",$s->_countrys);

      foreach ($splited as $id) {
          $cnt = $doctmp->createElement('cotoie');

          $cnt->setAttribute('id-p', $id);
          $sea->appendChild($cnt);
      }
      $em->appendChild($sea);
    }
    $res->appendChild($em);
  return $res;
}

?>
