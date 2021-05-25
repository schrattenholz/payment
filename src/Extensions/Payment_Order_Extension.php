<?php

namespace Schrattenholz\Payment;

use Schrattenholz\Order\OrderExtension;
use Schrattenholz\Order\Product;
use Schrattenholz\Order\OrderConfig;
use Schrattenholz\OrderProfileFeature\OrderProfileFeature_Basket;
use Silverstripe\ORM\DataExtension;
use SilverStripe\Security\Security;
use Silverstripe\Security\Group;
use Silverstripe\Forms\TextField;
use Silverstripe\Forms\OptionsetField;
use Silverstripe\Forms\ConfirmedPasswordField ;
use Silverstripe\ORM\ArrayList;
use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormAction;
use Schrattenholz\OrderSale\OrderSale_ClientContainer;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Security\Member;
use SilverStripe\Security\MemberAuthenticator\MemberAuthenticator;
use SilverStripe\Security\IdentityStore;
use SilverStripe\Control\Controller;
use Psr\Log\LoggerInterface;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Control\Email\Email;
use SilverStripe\View\ArrayData;
use SilverStripe\View\Requirements;
use SilverStripe\View\SSViewer;
use SilverStripe\View\ThemeResourceLoader;
use SilverStripe\SiteConfig\SiteConfig;
use Schrattenholz\Order\Preis;
class Payment_Order_Extension extends DataExtension{

private static $allowed_actions = array (
		'RelatedPaymentMethods',
		'getPaymentMethods'

	);
	public function makeOrder_EmailToSeller($vars){
		$order=$vars->Order;
		$checkoutAddress=$vars->CheckoutAddress;
		Injector::inst()->get(LoggerInterface::class)->error('payment_Order_Extension.php makeOrder_EmailToOwner PaymentMethodID='.$order->PaymentMethodID);
		$paymentMethod=PaymentMethod::get()->byID($order->PaymentMethodID);
		//returns $email 
		$emailToSeller= $paymentMethod->EmailToSeller($vars->Email,$vars->CheckoutAddress);
	}
	public function makeOrder_ClientOrder($vars){
		$basket=$vars->Basket;
		$order=$vars->Order;
		$order->PaymentMethodID=$basket->PaymentMethodID;
		$order->SEPA_Confirmation=$basket->SEPA_Confirmation;
	}
	public function setCheckoutDelivery_SaveToBasket($vars){
		$returnValues=new ArrayList(['Status'=>'error','Message'=>false,'Value'=>false]);
		$basket=$vars->Basket;
		$data=$vars->Data;
		$returnValues=$vars->ReturnValues;
		$basket->PaymentMethodID=$data->PaymentMethodID;
		$paymentMethod=PaymentMethod::get()->byID($data->PaymentMethodID);
		Injector::inst()->get(LoggerInterface::class)->error('Payment_Order_Extension validatePayment:'.$paymentMethod->validatePayment($basket,$data));
		$validationMessage=$paymentMethod->validatePayment($basket,$data);
		if($validationMessage=="good"){
			
			$basket=$paymentMethod->SaveToBasket($basket,$data);
		}else{
			Injector::inst()->get(LoggerInterface::class)->error('Payment_Order_Extension validatePayment FEhler:'.$paymentMethod->validatePayment($basket,$data));
			$returnValues->Status="error";
			$returnValues->Message=$validationMessage;
			$returnValues->Value='';

		}
	}
	public function getPaymentMethods($data){
		$deliveryTypeID=$data['deliveryTypeID'];
		$paymentMethodID=$data['paymentMethodID'];
		$checkoutAddress=$this->owner->getCheckoutAddress();
		$data=new ArrayData(['DeliveryTypeID'=>$deliveryTypeID,'PaymentMethodID'=>$paymentMethodID,'CheckoutAddress'=>$checkoutAddress]);
		
		return $this->owner->customise($data)->renderWith(ThemeResourceLoader::inst()->findTemplate(
				"Schrattenholz\\Payment\\Payment",
				SSViewer::config()->uninherited('themes')
			));
		//return $paginatedProducts;
	}
	public function RelatedPaymentMethods($deliveryTypeID=0,$papymentMethodID=0){
		
		if(isset($_GET['v'])){
			$priceBlockElementID=$_GET['v'];
		}
		$paymentMethods=PaymentMethod::get();
		if($deliveryTypeID){
			//Injector::inst()->get(LoggerInterface::class)->error('Payment\RelatedPaymentMethods id='.$deliveryTypeID);
			$paymentMethods=$paymentMethods->innerJoin('DeliveryType_PaymentMethods',"DTPM.Payment_PaymentMethodID=Payment_PaymentMethod.ID","DTPM")->where("DTPM.DeliveryTypeID=".$deliveryTypeID);
			//Injector::inst()->get(LoggerInterface::class)->error('Payment\RelatedPaymentMethods c='.$paymentMethods->Count());
		}
		/*if($papymentMethodID){
			$paymentMethods->filter('ID',$papymentMethodID);
		}*/
		return $paymentMethods;
	}
	 public function onAfterInit(){
		$vars = [
			"Link"=>$this->getOwner()->Link(),
			"ID"=>$this->owner->ID
		];
		Requirements::javascriptTemplate("schrattenholz/payment:javascript/payment.js",$vars);
	}
}