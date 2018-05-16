declare namespace functx = "http://www.functx.com";
declare function functx:escape-for-regex
  ( $arg as xs:string? )  as xs:string {

   replace($arg,
           '(\.|\[|\]|\\|\||\-|\^|\$|\?|\*|\+|\{|\}|\(|\))','\\$1')
 } ;
<em>
<liste-pays>
{
let $source := doc("../Mondial2015/XML/mondial.xml")
for $pays in $source/mondial/country[/mondial/river[./to/@watertype eq 'sea']/tokenize(@country, '\s+') = @car_code  or /mondial/sea/tokenize(@country, '\s+') =  @car_code]

return <pays id-p="{data($pays/@car_code)}" nom-p="{data($pays/name)}" superficie="{data($pays/@area)}" nbhab="{data($pays/population[last()])}">
  {
  for $fleuve in $source/mondial/river[./to/@watertype eq 'sea' and source/@country eq $pays/@car_code]
  return <fleuve id-f="{data($fleuve/@id)}" nom-f="{data($fleuve/name)}"  se-jette="{data($fleuve/to/@water)}" longueur="{data($fleuve/length)}">          
      {
      let $allFleuve := tokenize($fleuve/@country," ")
      for $parcours in $allFleuve
      return 
      
        if (count($allFleuve) = 1) 
        then <parcourt id-pays="{$parcours}" distance="{data($fleuve/length)}"/>
        else <parcourt id-pays="{$parcours}"  distance="inconnu"/>
      }
  </fleuve>
  }
  </pays>
}
</liste-pays>

<liste-espace-maritime>
{
let $source := doc("../Mondial2015/XML/mondial.xml")
for $em in $source/mondial/sea
return <espace-maritime id-e="{data($em/@id)}" type="inconnu" nom-e="{data($em/name)}">
  {
   for $cotoie in tokenize($em/@country," ")
   return <cotoie id-p="{$cotoie}"/>
  }
  </espace-maritime>
}
</liste-espace-maritime>
</em>
