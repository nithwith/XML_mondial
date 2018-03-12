rm out/*.xml
echo ---------- SHELL d execution du TP ----------
echo ----- Lancement de l exercice 1 : XSL -----
echo ----- Fichier de sorti : ex1.xml -----
time java -jar Xslt/saxon9he.jar -xsl:Xslt/ex1.xsl -s:Mondial2015/XML/mondial.xml -o:out/ex1.xml
echo ----- Fin de l exercice 1 : XSL -----

echo ----- Lancement de l exercice 2 : DOM SANS XPATH -----
echo ----- Fichier de sorti : ex2_without_xpath.xml -----
time php Dom/ex2_without_xpath.php
echo ----- Fin de l exercice 2 : DOM SANS XPATH -----

echo ----- Lancement de l exercice 2 : DOM AVEC XPATH -----
echo ----- Fichier de sorti : ex2_with_xpath.xml -----
time php Dom/ex2_with_xpath.php
echo ----- Fin de l exercice 2 : DOM AVEC XPATH -----
