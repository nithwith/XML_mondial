
# XML_mondial

## Théo MARTY & Théo LACROIX

Pour éxécuter les développements réalisés merci de suivre les instructions ci dessous
Il est possible de lancer le script shell "exec.sh" avec la commande `./exec.sh`
Celui ci va lancer tous les exercices du projet de Web des Données.

Pour lancer le script du distanciel de Web des Données et des Connaissances : `./execXquery.sh`
Si vous voulez lancer les commandes manuellement il faut :

**Se placer dans le répertoire racine du projet**
--


### Configuration obligatoire
- PHP 7.0.25
- Java
## XSLT
-Commande : `time java -jar Xslt/saxon9he.jar -xsl:Xslt/ex1.xsl -s:Mondial2015/XML/mondial.xml -o:out/ex1.xml`

-Résultat : `out/ex1.xml`

## SAX
-Commande : `time php em_sax.php`

-Résultat : `out/result_sax.xml`

## DOM
-Commande : `time php ex2_without_xpath.php ou time php ex2_with_xpath.php`

-Résultat : `out/ex2_without_xpath.xml`

## Simple XML
-Commande : `time php em_simple_xml.php`

-Résultat : `out/result_simple_xml.xml`

## Xquery
-Commande : `java -cp BaseX90.jar org.basex.BaseX -o out/xquery.xml Xquery/em_xquery.xq`

-Résultat : `out/xquery.xml`
