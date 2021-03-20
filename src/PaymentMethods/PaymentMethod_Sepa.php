<?php

namespace Schrattenholz\Payment;
use SilverStripe\Security\Permission;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Forms\FieldList;
use Silverstripe\Forms\TextareaField;
use Silverstripe\Forms\EmailField;
use Silverstripe\Forms\LiteralField;
use Silverstripe\Forms\CheckboxField;
use Silverstripe\Forms\ConfirmedPasswordField;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\OptionsetField;
use UndefinedOffset\NoCaptcha\Forms\NocaptchaField;
use Silverstripe\Security\Group;
use SilverStripe\Security\Member;
use SilverStripe\Core\Injector\Injector;
use Psr\Log\LoggerInterface;
use Schrattenholz\Order\OrderConfig;
class PaymentMethod_SEPA extends PaymentMethod
{
	private static $db = array (
		'PublicKey'=>'Text'
	);
	private static $singular_name="SEPA-Lastschrift";
	private static $plural_name="SEPA-Lastschrift";
	private static $table_name="Payment_PaymentMethod_SEPA";
	private static $defaults = [
        'Title' => 'SEPA-Lastschrift',
		'Content'=>'Wir ziehen unsere Rechnung im Lastschriftverfahren von Deinem Konto ein.',
		'Template'=>'Schrattenholz\\Payment\\Templates\\PaymentMethod_SEPA'
    ];
	public function HasAdditinoalFields(){
		return true;
	}
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
class PaymentMethod_SEPA_Member_Extension extends DataExtension {
	private static $db=[
		"IBAN"=>"Text"
	];
	public function updateCMSFields(FieldList $fields) {
		$iban=new TextareaField('IBAN', 'IBAN');
		$iban->setRows(5);
		$fields->addFieldToTab("Root.Bezahlung",$iban);
	  }
	public function onBeforeWrite(){
		$iban=Member::get()->byID($this->owner->ID)->IBAN;
		//Injector::inst()->get(LoggerInterface::class)->error("field=".$this->owner->getField("IBAN").'----onBeforeWrite  db='.$iban);
		if($this->owner->getField("IBAN")!=$iban){
			$this->owner->setField("IBAN",$this->generateRSA($this->owner->getField("IBAN")));
			
		}
		parent::onBeforeWrite();
	}
	private function generateRSA($data){
		Injector::inst()->get(LoggerInterface::class)->error('----generateRSA'.OrderConfig::get()->First()->PublicKey);
		$encrypted_data="";
		openssl_public_encrypt($data,$encrypted_data,OrderConfig::get()->First()->PublicKey);
		return $encrypted_data;
	}
}
?>
