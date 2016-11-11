<?php        // Author: Filip Rydlo

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

//---- Sign plaintext using openssl_sign
$private_key= file_get_contents('./03.key.pem');
$public_cert= file_get_contents('./03.crt.pem');
$binary_signature = "";  //for call by ref.
// At least with PHP 5.2.2 / OpenSSL 0.9.8b (Fedora 7)  there seems to be no need to call openssl_get_privatekey or similar.
// Just pass the key as it is in *.pem format
openssl_sign($plaintext, $binary_signature, $private_key, OPENSSL_ALGO_SHA256);

//---- Verify the signature
$ok = openssl_verify($plaintext, $binary_signature, $public_cert, OPENSSL_ALGO_SHA256);
if($ok==1) print "Signature \033[32m OK \033[0m\n";  // Green "OK" in linux console
else print "Signature is \033[31m BAD ! \033[0m\n";  // Red "BAD"!

//---- Check if the calculated signature is SAME as expected
$base64_signature= base64_encode($binary_signature);
// PKP & BKP "verification codes" from the example XML: 'CZ1212121218.valid.v3.xml'
$PKP_code= 'D84gY6RlfUi8dWdhL1zn0LE0s+aqLohtIxY0y88GoG5Ak8pBEH3/Ff2aFW7H6fvRxDMKsvM/VIYtUQxoDEctVGMSU/JDf9Vd0eQwgfLm683p316Sa4BUnVrIsHzwMyYkjpn66I072G2AvOUP4X5UiIYtHTwyMVyp+N/zzay3D7Q619ylDb6puN2iIlLsu+GNSB9DvsQbiLXPH6iK0R9FpR15v2y+0Uhh8NNJKl7O8Us9jbgokrA9gze+erQbhmwTm2nn2+7JGrPDqhyhwWZNLUziGSbC99wJpkEnIs0das/4hFNE3DnLvv4MsXwWCLOUZty6t6DAijlCzQj7KFKw0g==';
// string-compare the expected  $BKP_code  to the calculated signature in base64 encoding 
if(strcmp($PKP_code, $base64_signature) !== 0) {
  print "Signature is DIFFERENT than expected!\n";
  print "Expected:\n$PKP_code\n\n";
  print "Actually got:\n$base64_signature\n";
}

//---- Calculate BKP too. And check it against expected value of $BKP
$BKP= '8F8ABFEB-B76E7064-343A1460-6C6E6D86-B0F99C24';  // digest="SHA1" encoding="base16"

// This WILL BE FUN!!!  Lets RUN IT and we will see!  :-)))
// FR
