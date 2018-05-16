declare namespace functx = "http://www.functx.com";
declare function functx:word-count
  ( $arg as xs:string? )  as xs:integer {

   count(tokenize($arg, '\W+')[. != ''])
 };
 declare function functx:escape-for-regex
  ( $arg as xs:string? )  as xs:string {

   replace($arg,
           '(\.|\[|\]|\\|\||\-|\^|\$|\?|\*|\+|\{|\}|\(|\))','\\$1')
 } ;
declare function functx:contains-word
  ( $arg as xs:string? ,
    $word as xs:string )  as xs:boolean {

   matches(upper-case($arg), concat('^(.*\W)?', upper-case(functx:escape-for-regex($word)),'(\W.*)?$'))
 } ;


<em>
<liste-pays>
{
let $source := doc("../Mondial2015/XML/mondial.xml")
for $pays in $source/mondial/country[/mondial/river[./to/@watertype eq 'sea']/tokenize(@country, '\s+') = @car_code  or /mondial/sea/tokenize(@country, '\s+') =  @car_code]
return <pays id-p="{data($pays/@car_code)}" nom-p="{data($pays/name)}" superficie="{data($pays/@area)}" nbhab="{data($pays/population[last()])}">
  {
  for $fleuve in $source/mondial/river[./to/@watertype eq 'sea' and source/@country eq $pays/@car_code]
  return <fleuve id-f="{data($fleuve/@id)}" nom-f="{data($fleuve/name)}" longueur="{data($fleuve/length)}" se-jette="{data($fleuve/to/@water)}">          
      {
      let $countrys := normalize-space($fleuve/@country)
      for $parcours in $source/mondial/country[functx:contains-word($countrys, @car_code)]    
      return 
        if (functx:word-count($fleuve/@country) = 1)
        then <parcours id-pays="{data($parcours/@car_code)}"  distance="{data($fleuve/length)}"/>
        else <parcours id-pays="{data($parcours/@car_code)}"  distance="inconnu"/>
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
return <espace-maritime id-e="{data($em/@id)}" nom-e="{data($em/name)}">
  {
  let $countrys := normalize-space($em/@country)
  for $cotoie in $source/mondial/country[contains($countrys, @car_code)]
  return <cotoie id-p="{data($cotoie/@car_code)}"/>
  }
  </espace-maritime>
}
</liste-espace-maritime>
</em>
