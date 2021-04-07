<?php 

namespace Schrattenholz\Payment;


use Silverstripe\ORM\DataExtension;

//Extends OrderProfileFeature_ClientContainer

class Payment_OrderProfileFeature_ClientContainer_Extension extends DataExtension {
	private static $db=[
		"IBAN"=>"Text",
		"IBAN_Hint"=>"Varchar(50)",
		"IBAN_Hash"=>"Text",
		"BIC"=>"Text",
		"BIC_Hint"=>"Varchar(20)",
		"BIC_HASH"=>"Text"
	];
}
