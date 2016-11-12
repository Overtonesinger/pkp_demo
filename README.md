# pkp_demo
DEMO of PKP calculation in PHP5.6.27-0+deb8u1 (cli). PKP in this demo is computed from Plaintext taken from the EET XML SOAP-message example file 'CZ1212121218.valid.v3.xml'. Digital signature of Plaintext is created using EET (RSA2048 X509v3) TEST-certificate '01000003.p12' of virtual taxpayer CZ1212121218. The signature is deterministic, in 'PKCS#1 v1.5' format.
It is NOT using the 'PSS scheme' format.

# Requirements

pkp_demo requires PHP 5.3+ with ext/openssl
