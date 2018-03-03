<?php header('Content-type: text/xml');
include_once('Sax4PHP/Sax4PHP.php');

class MySaxHandler extends DefaultHandler {

  //Global variables
  public $list_country = array();


  function startElement($nom, $att) {
    switch($nom) {
  		case 'cotoie' :
        $this->list_country[] = $att['id-p'];
  			break;

  		default :;
  	}
  }

  function endElement($nom) {

  }

  function startDocument() {

  }
  function endDocument() {

    $result = array_unique($this->list_country);

    foreach ($result as $c) {
      echo $c;
      echo "\n";
    }
    echo count($result);
  }

}

$xml = file_get_contents('espace-maritime.xml');

$sax = new SaxParser(new MySaxHandler());
try {
	$sax->parse($xml);
}catch(SAXException $e){
	echo "\n",$e;
}catch(Exception $e) {
	echo "Default exception >>", $e;
}



?>
