<?php      // Author: Filip Rydlo,     last updated: 12. 11. 2016

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
  return $seg1 .'-'. $seg2 .'-'. $seg3 .'-'. $seg4 .'-'. $seg5;  // Does NOT work. You see?
  //======= I missed strtoupper() in my app! So, THAT was the problem! =======

  //return strtoupper($seg1 .'-'. $seg2 .'-'. $seg3 .'-'. $seg4 .'-'. $seg5);  // This works well.
}
//------------------------------------------------------------------------

//==== TEST-DATA ====
$dic_popl=   'CZ1212121218';  // VAT ID (of a virtual TEST-organization)
$id_provoz=  '273';           // franchise ID
$id_pokl=    '/5546/RO24';    // cash desk ID
$porad_cis=  '0/6460/ZQ42';   // bill serial number
$dat_trzby=  '2016-08-05T00:30:12+02:00';  // DateTime in "ISO 8601":   "yyyy-mm-ddThh:mm:ss±hh:mm"
$celk_trzba= '34113.00';      // total revenue from *this* particular sale / trade... (the Total amount on this bill)

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
if($ok==1) print "PKP (signature) ...\033[32m OK \033[0m\n";  // Green "OK" in linux console
else print "PKP (signature) is \033[31m BAD ! \033[0m\n";  // Red "BAD"!

//---- Check if the calculated signature is SAME as expected
$base64_signature= base64_encode($binary_signature);
// PKP & BKP "verification codes" from the example XML: 'CZ1212121218.valid.v3.xml'
$PKP_code_expected= 'D84gY6RlfUi8dWdhL1zn0LE0s+aqLohtIxY0y88GoG5Ak8pBEH3/Ff2aFW7H6fvRxDMKsvM/VIYtUQxoDEctVGMSU/JDf9Vd0eQwgfLm683p316Sa4BUnVrIsHzwMyYkjpn66I072G2AvOUP4X5UiIYtHTwyMVyp+N/zzay3D7Q619ylDb6puN2iIlLsu+GNSB9DvsQbiLXPH6iK0R9FpR15v2y+0Uhh8NNJKl7O8Us9jbgokrA9gze+erQbhmwTm2nn2+7JGrPDqhyhwWZNLUziGSbC99wJpkEnIs0das/4hFNE3DnLvv4MsXwWCLOUZty6t6DAijlCzQj7KFKw0g==';
// string-compare the expected  $BKP_code  to the calculated signature in base64 encoding 
if(strcmp($PKP_code_expected, $base64_signature) !== 0) {
  print "PKP (signature) is DIFFERENT than expected!\n";
  print "expected:\n$PKP_code_expected\n\n";
  print " but got:\n$base64_signature\n\n";
}
else {
  print "PKP (signature) is the same as expected:\n\033[32m $base64_signature\033[0m\n\n";
}

// Now BPK (SHA1 hash of PKP)
$BKP_expected = '8F8ABFEB-B76E7064-343A1460-6C6E6D86-B0F99C24';  // digest="SHA1" encoding="base16"
//---- boundary check
if (strlen($binary_signature) != 256)  // bin 'SHA256withRSA' PKCS#1 v1.5 Compatible (using RSA2048 Key) signature MUST be 256 bytes!
{
  die("FATAL Error: Binary signature length is\033[31m NOT 256 bytes!\033[0m    It is ".strlen($binary_signature) ." bytes.\n");
}

//---- Let's calculate the BKP. And verify it against the expected value
$BKP_hex = hash('sha1', $binary_signature);
$BKP_humanReadable= humanReadable_BKP($BKP_hex);  // Vlozi po kazdych 8 HEX-znacich minus "-", jak to EET v3 protokol vyzaduje
if(strcmp($BKP_expected, $BKP_humanReadable) !== 0)
{
  print "BKP code is DIFFERENT than expected!\n\n";
  print "expected:\033[32m $BKP_expected\033[0m\n";
  print " created: $BKP_humanReadable\n\n";
  print "TEST:\033[31m FAIL!   /   SELHAL! \033[0m\n";
}
else
{
  print "BKP code (sha1 hash of PKP) is the same as expected:".
        "\n\033[32m $BKP_humanReadable\033[0m\n\nTEST: \033[32m OK \033[0m\n";
}
