<?php

namespace Schrattenholz\Payment;

class PaymentMethod_SEPA extends PaymentMethod
{
	private static $db = array (
		'IBAN'=>'Varchar(255)',
		'BIC'=>'HTMLText',
		'Name'=>'Text',
		'SortOrder'=>'Int'
	);
	
	 public function canView($member = null) 
    {
        return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
    }

    public function canEdit($member = null) 
    {
        return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
    }

    public function canDelete($member = null) 
    {
        return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
    }

    public function canCreate($member = null, $context = []) 
    {
        return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
    }
}
?>
