<?php header('Content-type: text/xml');
$doc = new DOMDocument();
$doc->load("mondial.xml");
$resultXML = new DOMDocument('1.0');
// AutoIndent le code
$resultXML->preserveWhiteSpace = false;
$resultXML->formatOutput = true;

// Création de la racine
$racine = $resultXML->createElement('em');
// $listePays = $resultXML->createElement('liste-pays');
$listePays = createCountries($doc->documentElement->childNodes, $resultXML);
$racine->appendChild($listePays);

$listeMaritime = $resultXML->createElement('liste-espace-maritime');
$racine->appendChild($listeMaritime);


function createCountries($elements, $doctmp) {
  $ret = $doctmp->createElement('liste-pays');
  foreach($elements as $node) {
    if ($node->nodeName == "country") {
      $cntry =  $doctmp->createElement('pays');
      $cntry->setAttribute('id-p', $node->getAttribute('car_code'));
      $cntry->setAttribute('nom-p', $node->data);
      $cntry->setAttribute('superficie', $node->getAttribute('area'));
      $cntry->setAttribute('nbhab', $node->getAttribute('car_code'));
      $ret->appendChild($cntry);
    }
  }
  return $ret;
}

function createMaritime($value=''){

}

// Sauvegarde du fichier xml
$resultXML->appendChild($racine);
$resultXML->save('result.xml');
?>
