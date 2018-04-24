declare namespace functx = "http://www.functx.com";
declare function functx:word-count
  ( $arg as xs:string? )  as xs:integer {

   count(tokenize($arg, '\W+')[. != ''])
 } ;
 
<em>
<liste-pays>
{
for $pays in doc("../Mondial2015/XML/mondial.xml")/mondial/country[/mondial/river[./to/@watertype eq 'sea']/tokenize(@country, '\s+') = @car_code  or /mondial/sea/tokenize(@country, '\s+') =  @car_code]
return <pays id-p="{data($pays/@car_code)}" nom-p="{data($pays/name)}" superficie="{data($pays/@area)}" nbhabitants="{data($pays/population[last()])}">
  {
  for $fleuve in doc("../Mondial2015/XML/mondial.xml")/mondial/river[./to/@watertype eq 'sea' and source/@country eq $pays/@car_code]
  return <fleuve id-f="{data($fleuve/@id)}" nom-f="{data($fleuve/name)}" longueur="{data($fleuve/length)}" se-jette="{data($fleuve/to/@water)}">          
      {
      let $countrys := normalize-space($fleuve/@country)
      for $parcours in doc("../Mondial2015/XML/mondial.xml")/mondial/country[contains($countrys, @car_code)]
      return 
        if (count($countrys) lt 1)
        then <parcours id-pays="{data($parcours/@car_code)}"  distance="{data($fleuve/length)}"/>
        else <parcours id-pays="{data($parcours/@car_code)}"  distance="inconnue"/>
      }
  </fleuve>
  }
  </pays>
}
</liste-pays>

<liste-espace-maritime>
{
for $em in doc("../Mondial2015/XML/mondial.xml")/mondial/sea
return <espace-maritime id-e="{data($em/@id)}" nom-e="{data($em/name)}">
  {
  let $countrys := normalize-space($em/@country)
  for $cotoie in doc("../Mondial2015/XML/mondial.xml")/mondial/country[contains($countrys, @car_code)]
  return <cotoie id-p="{data($cotoie/@car_code)}"/>
  }
  </espace-maritime>
}
</liste-espace-maritime>
</em>
