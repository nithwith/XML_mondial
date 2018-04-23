<liste-pays>
{
for $x in doc("../XML_mondial/Mondial2015/XML/mondial.xml")/mondial/country

order by $x/name
return <pays id-p="{data($x/@car_code)}"/>
}
</liste-pays>

