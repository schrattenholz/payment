<?php

namespace Schrattenholz\Payment;
use SilverStripe\Core\Extension;
use Schrattenholz\OrderProfileFeature\OrderCustomerGroup;

class Payment_OrderProfileFeature_OrderCostumerGroup_Extension extends Extension{
	private static $belongs_many_many=[		
			'PaymentMethods'=>PaymentMethod::class
	];
}


?>
