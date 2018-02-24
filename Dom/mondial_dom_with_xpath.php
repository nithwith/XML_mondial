<?php

$doc = new DOMDocument();
$doc->load("../Mondial2015/XML/mondial.xml");
$xpath = new DOMXpath($doc);

//requête xpath pour écrêmer les résultats
$liste_pays = $xpath->query('/mondial/country[encompassed[@continent = "asia" and @percentage < 100]]');

$imp = new DOMImplementation;
$dtd = $imp->createDocumentType('liste-pays', '', 'liste-pays.dtd');
$xml = $imp->createDocument("", "", $dtd);
$xml->encoding = 'UTF-8';
$xml->standalone = false;
$xml_liste_pays = $xml->createElement("liste-pays");
$xml->appendChild($xml_liste_pays);

if (!empty($doc)){

	//On parcourt les pays
	foreach($liste_pays as $pays){

		$xml_pays = $xml->createElement("pays");
		$tmp_country_name = "";
		$capital_id = $pays->getAttribute("capital");
		$tmp_capital_name = "";
		$tmp_asia_percentage = 0;
		$tmp_other_percentage = 0;
		if($pays->hasChildNodes()){

			//On parcourt les caractéristiques du pays
			foreach($pays->childNodes as $caract){
				if($caract->nodeType != XML_TEXT_NODE){
					//on récupère le nom du pays
					if($caract->tagName == "name"){
						$tmp_country_name = $caract->textContent;
					}
					//On récupère la nom de la capitale dans les villes des provinces du pays
					if($caract->tagName == "province"){
						//On parcourt les villes de la province
						foreach($caract->childNodes as $node){
							if($node->nodeType != XML_TEXT_NODE){
								if($node->tagName == "city"){
									if($capital_id == $node->getAttribute("id")){
										$tmp_capital_name = $node->firstChild->nextSibling->textContent;
									}
								}
							}
						}
					}
					//On récupère les proportions des continents dans lesquels se trouve le pays
					if($caract->tagName == "encompassed"){
						if($caract->getAttribute("continent") == "asia"){
							$tmp_asia_percentage = $caract->getAttribute("percentage");
						}
						else{
							$tmp_other_percentage += $caract->getAttribute("percentage");
						}
					}
				}
			}

			//on insère les résultats dans le docelement du pays
			$xml_country_name = new DOMAttr("nom", $tmp_country_name);
			$xml_capital_name = new DOMAttr("capitale", $tmp_capital_name);
			$xml_asia_percentage = new DOMAttr("proportion-asie", $tmp_asia_percentage);
			$xml_other_percentage = new DOMAttr("proportion-autres", $tmp_other_percentage);
			$xml_pays->appendChild($xml_country_name);
			$xml_pays->appendChild($xml_capital_name);
			$xml_pays->appendChild($xml_asia_percentage);
			$xml_pays->appendChild($xml_other_percentage);
		}
		$xml_liste_pays->appendChild($xml_pays);

	}

echo $xml->saveXML();
//On écrit le résultat dans le fichier XML
$xml->save("mondial.xml");
}

?>
