<?php

namespace Schrattenholz\Payment;
use SilverStripe\Security\Permission;
class PaymentMethod_Collection extends PaymentMethod
{
	private static $singular_name="Bezahlung bei Abholung";
	private static $plural_name="Bezahlung bei Abholung";
	private static $table_name="Payment_PaymentMethod_Collection";
	private static $defaults = [
        'Title' => 'Bezahlung bei Abholung',
		'Content'=>'Du bezahlst direkt bei Abholung. Du kannst sowohl bar, als auch mit EC-Karte bei uns bezahlen.'
    ];
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
