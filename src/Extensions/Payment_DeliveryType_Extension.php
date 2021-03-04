<?php 

namespace Schrattenholz\Payment;


use Silverstripe\ORM\DataExtension;
use SilverStripe\Forms\CheckboxSetField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\ORM\Queries\SQLUpdate;
use Kinglozzer\MultiSelectField\Forms\MultiSelectField;
use SilverStripe\Core\Injector\Injector;
use Psr\Log\LoggerInterface;

class Payment_DeliveryType_Extension extends DataExtension {
	private static $many_many=[
		"PaymentMethods"=>PaymentMethod::class
	];
	public function updateCMSFields($fields){
		$paymentMethods = MultiSelectField::create('PaymentMethods', 'Bezahlmethoden', $this->owner);
        $fields->addFieldToTab('Root.Main', $paymentMethods);
	}
}
