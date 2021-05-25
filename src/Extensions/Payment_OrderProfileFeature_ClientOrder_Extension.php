<?php 	

namespace Schrattenholz\Payment;


use Silverstripe\ORM\DataExtension;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\ArrayList;
use SilverStripe\View\ArrayData;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Security\IdentityStore;
use SilverStripe\Control\Email\Email;
use Schrattenholz\Order\Backend;
use SilverStripe\ORM\ValidationException;
use Psr\Log\LoggerInterface;
use SilverStripe\View\Requirements;
use SilverStripe\View\SSViewer;
use SilverStripe\View\ThemeResourceLoader;

use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\DateField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\DropdownField;

//Extends OrderProfileFeature_Basket & OrderProfileFeature_ClientOrder

class Payment_OrderProfileFeature_ClientOrder_Extension extends DataExtension {
	private static $db=[
		"SEPA_Confirmation"=>"Boolean",
	];
	private static $has_one=[
		"PaymentMethod"=>PaymentMethod::class,
	]; 
	/*private static $summary_fields=[
		"DeliveryType.Title"=>" Versandart",
		"CollectionDay.Day"=>" Abholtag",
		"Route.Title"=>" Lieferroute",
		"ShippingDate"=>"Abhol/Liefertermin"
	];*/
		/*public function updateCMSFields($fields){
		//$fields=parent::getCMSFields();
	
		$collectionDay=new DropdownField('CollectionDayID','Abholtag',CollectionDay::get()->map('ID','Title'));
		$collectionDay->setHasEmptyDefault(true);
		$route=new DropdownField('RouteID','Lieferroute',Route::get()->map('ID','Title'));
		$route->setHasEmptyDefault(true);
		$fields->addFieldToTab('Root.Main',new DropdownField('DeliveryTypeID','Lieferart',DeliveryType::get()->map('ID','Title')));
		$fields->addFieldToTab('Root.Main',new DateField('ShippingDate','Liefer/Abholdatum'));	
		$fields->addFieldToTab('Root.Main',$collectionDay);
		$fields->addFieldToTab('Root.Main',$route);
		
		//return $fields;
		
		
	}*/

}
