<?php header('Content-type: text/xml');
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

//Variables tabs globales
$allCountries = array();
$filteredCountries = array();
$filteredRivers = array();
$allSeas = array();
$filtereds_ids = "";

$listeMaritime = $resultXML->createElement('liste-espace-maritime');
$listePays = $resultXML->createElement('liste-pays');

// //OPTIMISATION -- Enregistrement de tous les noeuds importants dans un tableau spécifique
foreach ($doc->documentElement->childNodes as $node) {
  switch ($node->nodeName) {
    case 'country':
    array_push($allCountries, $node);
    break;
    case 'river':
    foreach ($node->childNodes as $child) {
      //On filtre le tableau des rivières si elle ont comme watertype 'sea'
      if ($child->nodeName == 'to' && $child->getAttribute('watertype') == 'sea') {
        array_push($filteredRivers,$node);
        //On rempli un string contenant tous les id des rivières filtrées
        $filtereds_ids = $filtereds_ids . $node->getAttribute('country') . " ";
      }
    }
    break;
    case 'sea':
    // on concatène au id des pays filtrés toutes les mers
    $filtereds_ids = $filtereds_ids . $node->getAttribute('country') . " ";
    array_push($allSeas,$node);
    break;
    default:
    # code...
    break;
  }
}


$split = explode(" ",$filtereds_ids);
foreach ($allCountries as $country) {
  // Test pour remplir le tableau contenan tous les nodes des pays filtrés avec les filtres pour les pays
  if (in_array($country->getAttribute('car_code'), $split)) {
    array_push($filteredCountries,$country);
  }
}

//Création de la liste des noeuds de tous les pays
$listePays = createCountries($filteredCountries,$allCountries,$filteredRivers, $resultXML);
//On ajoute tous les pays au document final
$racine->appendChild($listePays);

//Création de la liste des espace maritimes
$listeMaritime = createMaritime($allSeas,$allCountries, $resultXML);
//On ajoute les noeuds au document final
$racine->appendChild($listeMaritime);

function createCountries($filteredCountries,$countries, $rivers, $doctmp) {
  $ret = $doctmp->createElement('liste-pays');
  foreach ($filteredCountries as $node) {
    $cntry =  $doctmp->createElement('pays');
    $cntry->setAttribute('id-p', $node->getAttribute('car_code'));
    //On parcourt tous les noeuds fils des noeuds country
    foreach ( $node->childNodes as $key ) {
      if($key->nodeName == "name") {
        $cntry->setAttribute('nom-p', $key->nodeValue);
        $cntry->setAttribute('superficie', $node->getAttribute('area'));
      }
      if ($key->nodeName == "population") {
        $cntry->setAttribute('nbhab', $key->nodeValue);
      }
    }

    //On parcourt tous les noeuds initiaux du document pour avoir les river
    foreach ($rivers as $river) {
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
        if ($elem->nodeName == "source" && $elem->getAttribute('country') == $node->getAttribute('car_code')) {
          $splited = explode(" ", $river->getAttribute('country'));
          foreach ($countries as $country) {
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
    //On ajoute chaque pays dans la variable ret
    $ret->appendChild($cntry);
  }

  return $ret;
}

function createMaritime($seas,$countries, $doctmp){
  $ret = $doctmp->createElement('liste-espace-maritime');

  foreach ($seas as $sea) {
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
    foreach ($countries as $country) {
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
$resultXML->save('out/ex2_without_xpath.xml');
?>
