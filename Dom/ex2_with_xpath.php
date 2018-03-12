<?php
$doc = new DOMDocument();
$doc->load("Mondial2015/XML/mondial.xml");

$imp = new DOMImplementation;
$dtd = $imp->createDocumentType('em', '', 'em.dtd');
$resultXML = new DOMDocument('1.0', 'utf-8');
$resultXML->appendChild($dtd);


// AutoIndent le code
$resultXML->preserveWhiteSpace = false;
$resultXML->formatOutput = true;

// Création de la racine
$racine = $resultXML->createElement('em');

//objet pour les requetes xpath
$xpath = new DOMXpath($doc);



//Création de la liste des noeuds de tous les pays
$listePays = createCountries($resultXML, $xpath);
//On ajoute tous les pays au document final
$racine->appendChild($listePays);

//Création de la liste des espace maritimes
$listeMaritime = createMaritime($resultXML, $xpath);
//On ajoute les noeuds au document final
$racine->appendChild($listeMaritime);


//Création des pays
function createCountries($doctmp, $xpath){
  $ret = $doctmp->createElement('liste-pays');
  //Requete liste pays filtered
  $req_pays_filtered = "/mondial/country[/mondial/river[to/@watertype = 'sea']/@country = @car_code  or /mondial/sea/@country =  @car_code]";

  $req_rivers_filtered = '/mondial/river[to/@watertype = "sea"]';
  $req_country = '/mondial/country';
  $req_sea = '/mondial/sea/@country';

  $liste_country = $xpath->query($req_pays_filtered);
  $liste_country_filtered = array();
  $liste_river_filtered = $xpath->query($req_rivers_filtered);

  $tmp_countries_filtered = "";
  //On parcourt tous les rivers filtré sur watertype = 'sea'
  ///On ajoute tous les ID des country trouvés dans la variable qui va permettre le filtrage des pays
  foreach ($xpath->query($req_rivers_filtered . '/@country') as $river_id) {
    $tmp_countries_filtered = $tmp_countries_filtered . $river_id->value . " ";
  }

  //On parcourt toutes les mers pour récupérer les ID des country qui ont des mers
  //On ajoute tous les ID dans la même variable globale que pour les rivières
  foreach ($xpath->query($req_sea) as $sea) {
    $tmp_countries_filtered = $tmp_countries_filtered . $sea->value . " ";
  }

  //On filtre les pays
  $liste_country_tmp = explode(" ", $tmp_countries_filtered);
  foreach ($xpath->query($req_country) as $key) {
    if (in_array($key->getAttribute('car_code'), $liste_country_tmp)) {
      array_push($liste_country_filtered,$key);
    }
  }

  //Création des pays
  foreach ($liste_country_filtered as $pays) {
    $cntry =  $doctmp->createElement('pays');
    $cntry->setAttribute('id-p', $pays->getAttribute('car_code'));

    //On parcourt ses childNodes pour les différents attributs
    foreach ( $pays->childNodes as $key ) {
      if($key->nodeName == "name") {
        $cntry->setAttribute('nom-p', $key->nodeValue);
        $cntry->setAttribute('superficie', $pays->getAttribute('area'));
      }
      if ($key->nodeName == "population") {
        $cntry->setAttribute('nbhab', $key->nodeValue);
      }
    }

    //On créer les fleuves
    foreach ($liste_river_filtered as $river) {
      $fleuve = $doctmp->createElement('fleuve');
      $fleuve->setAttribute('id-f', $river->getAttribute('id'));
      foreach ($river->childNodes as $elem) {
        if ($elem->nodeName == "name") {
          $fleuve->setAttribute('nom-f', $elem->nodeValue);
        }
        if ($elem->nodeName == "to") {
          $fleuve->setAttribute('se-jette',$elem->getAttribute('water'));
        }
        if ($elem->nodeName == "length") {
          $fleuve->setAttribute('longueur', $elem->nodeValue);
        }
        if ($elem->nodeName == "source" && $elem->getAttribute('country') == $pays->getAttribute('car_code')) {
          $splited = explode(" ", $river->getAttribute('country'));

          //on ne peux pas réutuliser $liste_country car il est déjà utilisé (étrange ????)
          //Surement du au processeur xpath, les résuktats des requetes query ne sont finalement pas stockées dans un tableau
          //Mais reste finalement un objet qui est exécuté directement dans le foreach (je pense?)
          foreach ($xpath->query($req_country) as $country) {
            foreach ($splited as $id) {
              if ($id == $country->getAttribute('car_code')) {
                $cnt = $doctmp->createElement('parcourt');
                $cnt->setAttribute('id-pays', $id);
                if (sizeof($splited) == 1) {
                  $cnt->setAttribute('distance', $fleuve->getAttribute('longueur'));
                } else {
                  $cnt->setAttribute('distance', 'inconnu');
                }
                $fleuve->appendChild($cnt);
              }
            }
          }
          $cntry->appendChild($fleuve);
        }
      }
    }
    $ret->appendChild($cntry);
  }
  return $ret;
}

//Création des espaces maritimes
function createMaritime($doctmp, $xpath){
  $ret = $doctmp->createElement('liste-espace-maritime');
  $req_rivers_filtered = '/mondial/river[to/@watertype = "sea"]';
  $req_country = '/mondial/country';
  $req_sea = '/mondial/sea';
  $liste_country = $xpath->query($req_country);
  $liste_river_filtered = $xpath->query($req_rivers_filtered);
  $liste_sea = $xpath->query($req_sea);

  foreach ($liste_sea as $sea) {
    $template_sea = $doctmp->createElement('espace-maritime');
    $template_sea->setAttribute('id-e', $sea->getAttribute('id'));
    foreach ($sea->childNodes as $node_sea) {
      if ($node_sea->nodeName == "type") {
        $template_sea->setAttribute('type',$node_sea->nodeValue);
      } else {
        $template_sea->setAttribute('type','inconnu');
      }
      if ($node_sea->nodeName == "name") {
        $template_sea->setAttribute('nom-e',$node_sea->nodeValue);
      }
    }

    $splited = explode(" ",$sea->getAttribute('country'));
    foreach ($liste_country as $country) {
      foreach ($splited as $id) {
        if ($id == $country->getAttribute('car_code')) {
          $cnt = $doctmp->createElement('cotoie');
          $cnt->setAttribute('id-p', $id);
          $template_sea->appendChild($cnt);
        }
      }
    }
    $ret->appendChild($template_sea);
  }

  return $ret;
}

// Sauvegarde du fichier xml
$resultXML->appendChild($racine);
$resultXML->save('out/ex2_with_xpath.xml');
?>
