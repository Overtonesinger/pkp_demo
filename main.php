<?php        // Author: Filip Rydlo
require_once(dirname(__FILE__) . '/vendor/autoload.php');

//define('WSDL', 'https://pg.eet.cz/eet/services/EETServiceSOAP/v3/?wsdl');  // Does NOT tolerate trailing '/' after "v3"! Remove it!
define('ENDPOINT_LOCATION', 'https://pg.eet.cz/eet/services/EETServiceSOAP/v3/');  // hint: location is the URL of the SOAP server
define('ENDPOINT_URI',      'http://fs.mfcr.cz/eet/schema/v3');  // hint: uri is the target namespace of the SOAP service

$path_to_wsdl_DIR= dirname(__FILE__) . DIRECTORY_SEPARATOR . 'wsdl' . DIRECTORY_SEPARATOR;
define('WSDL_LOCAL',	'EETServiceSOAP.wsdl');  // usage: $path_to_wsdl_DIR . WSDL_LOCAL
define('EXAMPLE_XML',   'CZ1212121218.valid.v3.xml');	// usage: $path_to_wsdl_DIR . EXAMPLE_XML

//==== Define TEST-DATA ====
$dic_popl=   'CZ1212121218';  // VAT ID (of a virtual TEST-organization)
$id_provoz=  '273';           // franchise ID
$id_pokl=    '/5546/RO24';    // cash desk ID
$porad_cis=  '0/6460/ZQ42';   // bill serial number
$dat_trzby=  '2016-08-05T00:30:12+02:00';  // DateTime in "ISO 8601":   "yyyy-mm-ddThh:mm:ss±hh:mm"
$celk_trzba= '34113.00';

//---- Compose the plaintext to be signed
$plaintext = $dic_popl  .'|'. $id_provoz .'|'. $id_pokl .'|'. $porad_cis .'|'. $dat_trzby .'|'. $celk_trzba;
echo $plaintext ."\n";
