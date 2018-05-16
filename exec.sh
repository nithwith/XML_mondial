echo ---------- SHELL d execution du TP ----------
echo ----- Lancement de l exercice 1 : XSL -----
echo ----- Fichier de sorti : ex1.xml -----
time java -jar Xslt/saxon9he.jar -xsl:Xslt/ex1.xsl -s:Mondial2015/XML/mondial.xml -o:out/ex1.xml
echo ----- Fin de l exercice 1 : XSL -----

echo ----- Lancement de l exercice 2  -----
echo ----- DOM SANS XPATH -----
echo ----- Fichier de sorti : ex2_without_xpath.xml -----
time php Dom/ex2_without_xpath.php

echo ----- DOM AVEC XPATH -----
echo ----- Fichier de sorti : ex2_with_xpath.xml -----
time php Dom/ex2_with_xpath.php

echo ----- SAX -----
echo ----- Fichier de sorti : result_sax.xml -----
time php Sax/em_sax.php

echo ----- Fin de l exercice 2  -----

echo ----- Lancement de l exercice 3  -----
echo ----- Fichier de sorti : result_simple_xml.xml -----
time php SimpleXML/em_simple_xml.php

echo ----- Fin de l exercice 3  -----
