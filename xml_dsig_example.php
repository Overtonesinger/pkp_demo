<?php
require(__DIR__ . '/vendor/autoload.php');

use RobRichards\XMLSecLibs\XMLSecurityDSig;
use RobRichards\XMLSecLibs\XMLSecurityKey;


// Constants
define('TEST3_PRIVATE_KEY', './03.key.pem');  // JE POUZE TESTOVACI! DIC "CZ1212121218"

// Load the XML to be signed
$doc = new DOMDocument();
$doc->load(__DIR__ . '/xml_tobe_signed.xml');

// Create a new Security object
$objDSig = new XMLSecurityDSig();
// Use the c14n exclusive canonicalization
$objDSig->setCanonicalMethod(XMLSecurityDSig::EXC_C14N);
// Sign using SHA-256
$objDSig->addReference(
		$doc,
		XMLSecurityDSig::SHA256,
		array('http://www.w3.org/2000/09/xmldsig#')
		);

// Create a new (private) Security key
$objKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256, array('type'=>'private'));
// Load the private key
$objKey->loadKey(TEST3_PRIVATE_KEY, TRUE);

// Sign the XML file
$objDSig->sign($objKey);

// Add the associated public key to the signature
$objDSig->add509Cert(file_get_contents(TEST4_PUBLIC_KEY));

// Append the signature to the XML
$objDSig->appendSignature($doc->documentElement);

// Save the signed XML
$doc->save(__DIR__ . '/zzz_SIGNED.xml');
