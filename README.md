# sax_mondial

Pour éxécuter les développements réalisés merci de suivre les instructions ci dessous
Il est possible de lancer le script shell "exec.sh" avec la commande "./exec.sh". Celui ci va lancer tous les exercices. Si vous voulez lancer les commandes manuellement il faut :
/!\Se placer dans le répertoire racine du projet /!\


### Configuration obligatoire
- PHP 7.0.25

## XSLT
-Commande : time java -jar Xslt/saxon9he.jar -xsl:Xslt/ex1.xsl -s:Mondial2015/XML/mondial.xml -o:out/ex1.xml

## SAX
-Commande : time php em_sax.php

## DOM
-Commande : time php ex2_without_xpath.php ou time php ex2_with_xpath.php

## Simple XML
-Commande : time php em_simple_xml.php
