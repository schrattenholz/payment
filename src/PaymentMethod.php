<?php

namespace Schrattenholz\Payment;

use SilverStripe\ORM\DataObject;
use Silverstripe\Forms\TextField;
use Silverstripe\Forms\NumericField;
use Silverstripe\Forms\CheckboxField;
use Silverstripe\Forms\LiteralField;
use Silverstripe\Forms\DropdownField;
use Silverstripe\Forms\HiddenField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TabSet;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Security\Permission;
use SilverStripe\View\SSViewer;
use SilverStripe\View\ThemeResourceLoader;
use Schrattenholz\Delivery\DeliveryType;
use Psr\Log\LoggerInterface;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\View\ArrayData;
class PaymentMethod extends DataObject
{
	private static $singular_name="Bezahlart";
	private static $plural_name="Bezahlart";
	private static $table_name="Payment_PaymentMethod";
	private static $default_sort=['SortOrder'];
	private static $db = array (
		'Title'=>'Varchar(255)',
		'Content'=>'HTMLText',
		'Template'=>'Text',
		'SortOrder'=>'Int'
	);
	/*private static $many_many_extraFields =[
		'Delivery_ZIPCodes'=>[
			'Latidude'=>'Float',
			'Longitude'=>'Float',
			'State'=>'Varchar(255)',
			'Community'=>'Varchar(255)'
			]
	];*/
	private static $belongs_many_many=[
		'DeliveryTypes'=>DeliveryType::class,
	];
	private static $summary_fields = [
		'Title' => 'Paymentmethode'
    ];
   private static $searchable_fields = [
      'Title'
   ];
	public function renderTemplate($basketID){
		return $this->renderWith(ThemeResourceLoader::inst()->findTemplate(
				$this->Template,
				SSViewer::config()->uninherited('themes')
			));
		//return $paginatedProducts;
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
	public function validatePayment($basket,$data){
		return true;
	}
	public function SaveToBasket($basket,$data){
		Injector::inst()->get(LoggerInterface::class)->error('PaymentMethod SaveToBasket');
		return $basket;
	}
}
?>
