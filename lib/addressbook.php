<?php
ini_set('display_errors', 1);
require_once '../nusoap_lib/nusoap.php';
include_once '../functions/Contact.class.php';
$server = new soap_server();
$namespace = "http://localhost/addressbook/lib/addressbook.php";
$server->configureWSDL('addressbook');
//, 'urn:addressbook_wsdl'
$server->wsdl->schemaTargetNamespace = $namespace;
$server->soap_defencoding = 'UTF-8';


$server->wsdl->addComplexType('Sample',
    'complexType',
    'struct',
    'all',
    '',
    array(
            'first_name' => array('name' => 'first_name', 'type' => 'xsd:string'),
            'error' => array('name' => 'error', 'type' => 'xsd:string'),
        )
);

$server->register('Contact.getName',
    array('id'=>'xsd:int'),
    array('return' => 'tns:Sample'),
      $namespace,
        false,
//    'addressbook_wsdl',
//    'addressbook_wsdl#Contact.getName',
    'rpc',
    'encoded',
    'Update contact'
);

// 
//$server->wsdl->addComplexType('Contact',
//    'complexType',
//    'struct',
//    'all',
//    '',
//    array(
//            'id' => array('name' => 'id', 'type' => 'xsd:int'),
//            'first_name' => array('name' => 'first_name', 'type' => 'xsd:string'),
//            'last_name' => array('name' => 'last_name', 'type' => 'xsd:string'),
//            'email' => array('name' => 'email', 'type' => 'xsd:string'),
//            'phone_number' => array('name' => 'phone_number', 'type' => 'xsd:string')
//    )
//);
//$server->wsdl->addComplexType('AddressBook',
//    'complexType',
//    'array',
//    '',
//    'SOAP-ENC:Array',
//    array(),
//    array(
//            array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:Contact[]')
//    ),
//    'tns:Contact'
//);
//$server->register('searchAddressBook',
//    array('query' => 'xsd:string'),
//    array('return' => 'tns:AddressBook'),
//    'addressbook_wsdl',
//    'addressbook_wsdl#search',
//    'rpc',
//    'encoded',
//    'Returns matching contacts'
//);
//$server->register('updateContact',
//    array('new_contact' => 'tns:Contact'),
//    array('return' => 'xsd:boolean'),
//    'addressbook_wsdl',
//    'addressbook_wsdl#updateContact',
//    'rpc',
//    'encoded',
//    'Update contact'
//);
//$server->register('deleteContact',
//    array('new_contact' => 'tns:Contact'),
//    array('return' => 'xsd:boolean'),
//    'addressbook_wsdl',
//    'addressbook_wsdl#deleteContact',
//    'rpc',
//    'encoded',
//    'Delete contact'
//);
//$server->register('insertContact',
//    array('new_contact' => 'tns:Contact'),
//    array('return' => 'xsd:boolean'),
//    'addressbook_wsdl',
//    'addressbook_wsdl#insertContact',
//    'rpc',
//    'encoded',
//    'Insert contact'
//);
$request = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
$server->service($request);
