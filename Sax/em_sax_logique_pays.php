<?php header('Content-type: text/xml');
include_once('Sax4PHP/Sax4PHP.php');

class MySaxHandler extends DefaultHandler {

  //Global variables
  public $list_country = array();
  public $list_country2 = array();

  public $list_country_temp = array();

  public $river_flag = False;



  function startElement($nom, $att) {
    switch($nom) {
  		case 'river' :
        $this->river_flag = True;
        $countrys = explode(" ",$att['country']);
        foreach ($countrys as $value){
          $this->list_country_temp[] = $value;
        }

        case 'to' :
          if(isset($att['watertype']) && $att['watertype'] == "sea"){
            foreach ($this->list_country_temp as $value){
              $this->list_country[] = $value;
            }
          }
          break;


      case 'sea' :
        $countrys2 = explode(" ",$att['country']);
        foreach ($countrys2 as $value){
           $this->list_country2[] = $value;
        }
        break;



  		default :;
  	}
  }

  function endElement($nom) {
    switch($nom) {
  		case 'river' :
        $this->river_flag = False;
        unset($this->list_country_temp);
        $this->list_country_temp = array();
        break;
    }
  }

  function startDocument() {

  }
  function endDocument() {

    $this->list_country = array_unique($this->list_country);
    $this->list_country2 = array_unique($this->list_country2);

    $result = array_merge($this->list_country, $this->list_country2);

    $result = array_unique($result);

    $file = fopen('result_sax_country.xml', 'a+');

    foreach ($result as $c) {
      fputs($file, $c);
      fputs($file, "\n");
    }
    echo "Result : ";
    echo count($result);
    echo "\n";
    echo "Liste 1 : ";
    echo count($this->list_country);
    echo "\n";
    echo "Liste 2 : ";
    echo count($this->list_country2);
    echo "\n";
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
