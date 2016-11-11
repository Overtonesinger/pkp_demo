<?php        // Author: Filip Rydlo
require_once(dirname(__FILE__) . '/vendor/autoload.php');

//define('WSDL', 'https://pg.eet.cz/eet/services/EETServiceSOAP/v3/?wsdl');  // Does NOT tolerate trailing '/' after "v3"! Remove it!
define('ENDPOINT_LOCATION', 'https://pg.eet.cz/eet/services/EETServiceSOAP/v3/');  // hint: location is the URL of the SOAP server
define('ENDPOINT_URI',      'http://fs.mfcr.cz/eet/schema/v3');  // hint: uri is the target namespace of the SOAP service

$path_to_wsdl_DIR= dirname(__FILE__) . DIRECTORY_SEPARATOR . 'wsdl' . DIRECTORY_SEPARATOR;
define('WSDL_LOCAL',	'EETServiceSOAP.wsdl');  // usage: $path_to_wsdl_DIR . WSDL_LOCAL
define('EXAMPLE_XML',   'CZ1212121218.valid.v3.xml');	// usage: $path_to_wsdl_DIR . EXAMPLE_XML


//-------- simple helper functions for improved code readability ---------
function humanReadable_BKP($BKP_hex)
{
  if (strlen($BKP_hex) != 40)  // boundary check: Unexpected length?  Then: No transformation!
      return $BKP_hex;
  $seg1= substr($BKP_hex,  0, 8);
  $seg2= substr($BKP_hex,  8, 8);
  $seg3= substr($BKP_hex, 16, 8);
  $seg4= substr($BKP_hex, 24, 8);
  $seg5= substr($BKP_hex, 32, 8);
  return $seg1 .'-'. $seg2 .'-'. $seg3 .'-'. $seg4 .'-'. $seg5;
}

//------------------------------------------------------------------------


//==== Define TEST-DATA ====
$dic_popl=   'CZ1212121218';  // VAT ID (of a virtual TEST-organization)
$id_provoz=  '273';           // franchise ID
$id_pokl=    '/5546/RO24';    // cash desk ID
$porad_cis=  '0/6460/ZQ42';   // bill serial number
$dat_trzby=  '2016-08-05T00:30:12+02:00';  // DateTime in "ISO 8601":   "yyyy-mm-ddThh:mm:ss±hh:mm"
$celk_trzba= '34113.00';

//---- Compose the plaintext to be signed
$plaintext = $dic_popl  .'|'. $id_provoz .'|'. $id_pokl .'|'. $porad_cis .'|'. $dat_trzby .'|'. $celk_trzba;
echo "plaintext: ". $plaintext ."\n\n";

// PKP & BKP "verification codes" from the example XML: 'CZ1212121218.valid.v3.xml'
$PKP_code= 'D84gY6RlfUi8dWdhL1zn0LE0s+aqLohtIxY0y88GoG5Ak8pBEH3/Ff2aFW7H6fvRxDMKsvM/VIYtUQxoDEctVGMSU/JDf9Vd0eQwgfLm683p316Sa4BUnVrIsHzwMyYkjpn66I072G2AvOUP4X5UiIYtHTwyMVyp+N/zzay3D7Q619ylDb6puN2iIlLsu+GNSB9DvsQbiLXPH6iK0R9FpR15v2y+0Uhh8NNJKl7O8Us9jbgokrA9gze+erQbhmwTm2nn2+7JGrPDqhyhwWZNLUziGSbC99wJpkEnIs0das/4hFNE3DnLvv4MsXwWCLOUZty6t6DAijlCzQj7KFKw0g==';
$BKP=      '8F8ABFEB-B76E7064-343A1460-6C6E6D86-B0F99C24';  // digest="SHA1" encoding="base16"
