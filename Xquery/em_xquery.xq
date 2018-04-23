<liste-pays>
{
for $x in doc("../Mondial2015/XML/mondial.xml")/mondial/country[/mondial/river[./to/@watertype eq 'sea']/tokenize(@country, '\s+') = @car_code  or /mondial/sea/tokenize(@country, '\s+') =  @car_code]
return <pays id-p="{data($x/@car_code)}" nom-p="{data($x/name)}" superficie="{data($x/@area)}" nhab="{data($x/population[last()])}"/>
}
</liste-pays>

