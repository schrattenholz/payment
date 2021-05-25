<?php
namespace Schrattenholz\Payment;

use Silverstripe\ORM\DataExtension;
use Schrattenholz\Order\OrderConfig;
use Schrattenholz\OrderProfileFeature\OrderCustomerGroup;
use SilverStripe\Forms\FieldList;
use Silverstripe\Forms\TextareaField;
class Payment_OrderConfig_Extension extends DataExtension{
	private static $db=[
		'PublicKey'=>'Text'
	];
	public function updateCMSFields(FieldList $fields) {
		$publicKey=new TextareaField('PublicKey', 'Öffentlicher Schlüssel (PublicKey)');
		$publicKey->setRows(5);
		$fields->addFieldToTab("Root.Bezahlung",$publicKey);
	  }
}