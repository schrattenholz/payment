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
use PHP_IBAN\IBAN;
use Schrattenholz\OrderProfileFeature\OrderProfileFeature_ClientContainer;
use Schrattenholz\OrderProfileFeature\OrderProfileFeature_Basket;
use SilverStripe\View\ArrayData;
use SilverStripe\View\SSViewer;
use SilverStripe\View\ThemeResourceLoader;
use SilverStripe\Assets\File;
use SilverStripe\AssetAdmin\Forms\UploadField;
class PaymentMethod_SEPA extends PaymentMethod
{
	private static $db = array (
		'PublicKey'=>"Text",
		'PrivateKey'=>"Text"
	);
	private static $singular_name="SEPA-Lastschrift";
	private static $plural_name="SEPA-Lastschrift";
	private static $table_name="Payment_PaymentMethod_SEPA";
	private static $defaults = [
        'Title' => 'SEPA-Lastschrift',
		'Content'=>'Wir ziehen unsere Rechnung im Lastschriftverfahren von Deinem Konto ein.',
		'Template'=>'Schrattenholz\\Payment\\Templates\\PaymentMethod_SEPA'
    ];
	/*public function getCMSFields(){
		$fields=parent::getCMSFields();
		$publicKey=UploadField::create("PublicKey","Öffentlicher Schlüssel");
		$publicKey->setAllowedExtensions(array("pem"));
		$fields->addFieldToTab("Root.Main",$publicKey);
		return $fields;
	}*/
	public function HasAdditinoalFields(){
		return true;
	}
	public function renderTemplate($basketID){
		$basket=OrderProfileFeature_Basket::get()->byID($basketID);
		$clientContainer=OrderProfileFeature_ClientContainer::get()->byID($basket->ClientContainerID);
		Injector::inst()->get(LoggerInterface::class)->error('ClientContainerID='.$basket->ClientContainerID);
		// Wenn der Benutzer ein Benutzerkonto hat: Nach hinterlegten SEPA-Daten suchen
		$member=Member::get()->byID($clientContainer->ClientID);
		$save_sepa=false;
		if(isset($member)){
				Injector::inst()->get(LoggerInterface::class)->error('PaymentMethod SEPA renderTemplate Member SEPA holen und in ClientContainer speichern? member->SEPA='.$member->SEPA);
				$clientContainer->IBAN_Hint=$member->IBAN_Hint;
				$clientContainer->BIC_Hint=$member->BIC_Hint;
				$clientContainer->SEPA=$member->SEPA;
				$save_sepa=true;
				$clientContainer->write();
		}
		if(isset($data->SAVE_SEPA) && $data->SAVE_SEPA=="on"){
			$save_sepa=true;
		}
		$data=new ArrayData(['IBAN_Hint'=>$clientContainer->IBAN_Hint,'BIC_Hint'=>$clientContainer->BIC_Hint,'SAVE_SEPA'=>$save_sepa]);
		return $this->customise($data)->renderWith(ThemeResourceLoader::inst()->findTemplate(
				$this->Template,
				SSViewer::config()->uninherited('themes')
			));
		//return $paginatedProducts;
	}
	public function EmailToSeller($emailToSeller,$checkoutAddress){
		Injector::inst()->get(LoggerInterface::class)->error('PaymentMethod_Sepa.php EmailToSeller ');
		$emailToSeller->addAttachmentFromData($checkoutAddress->SEPA,"sepa_b64.txt","text/vcard");
		//$emailToOwner->addAttachmentFromData("TESTDATA");
		return $emailToSeller;
	}
	public function SaveToBasket($basket,$data){
		
		$basket->SEPA_Confirmation=true;
		$clientContainer=OrderProfileFeature_ClientContainer::get()->byID($basket->ClientContainerID);
		$sepa_changed=false;;
		if(isset($data->SAVE_SEPA) && $data->SAVE_SEPA=="on" && $basket->ClientContainer()->ClientID){
			Injector::inst()->get(LoggerInterface::class)->error('PaymentMethod SEPA SaveToBasket Kontodaten in Profil speichern? '. $data->SAVE_SEPA);
			$member=Member::get()->byID($basket->ClientContainer()->ClientID);
		}
		if($this->makeHint($data->IBAN,"XXXXXXXXXX",4,6)!=$basket->ClientContainer->IBAN_Hint){
			$sepa_changed=true;
			//IBAN wird gespeichert, bzw atualisiert
			//$clientContainer->IBAN=$this->encryptData($data->IBAN);
			$clientContainer->IBAN_Hint=$this->makeHint($data->IBAN,"XXXXXXXXXX",4,6);
			
			if(isset($member)){
				Injector::inst()->get(LoggerInterface::class)->error('IBAN in Profil speichern? ');
				//$member->IBAN=$this->encryptData("BEGIN:VCARD\nX-BANK-IBAN-NUMBER:".$data->IBAN."\nX-BANK-SWIFT-NUMBER:".$data->BIC);
				$member->IBAN_Hint=$this->makeHint($data->IBAN,"XXXXXXXXXX",4,6);
			}
		}
		if($this->makeHint($data->BIC,"XXXXXXX",2,4)!=$basket->ClientContainer->BIC_Hint){
			$sepa_changed=true;
			//BIC wird gespeichert, bzw atualisiert
			$clientContainer->BIC=$data->BIC;
			$clientContainer->BIC_Hint=$this->makeHint($data->BIC,"XXXXXXX",2,4);
			if(isset($member)){
				Injector::inst()->get(LoggerInterface::class)->error('BIC in Profil speichern? ');
				$member->BIC=$data->BIC;
				$member->BIC_Hint=$this->makeHint($data->BIC,"XXXXXXX",2,4);
			}
		}
		if($sepa_changed==true){
			$sepaData=$this->encryptData("BEGIN:VCARD\nX-BANK-IBAN-NUMBER:".$data->IBAN."\nX-BANK-SWIFT-NUMBER:".$data->BIC."\nEND:VCARD");
			$clientContainer->SEPA=$this->encryptData("BEGIN:VCARD\nX-BANK-IBAN-NUMBER:".$data->IBAN."\nX-BANK-SWIFT-NUMBER:".$data->BIC."\nEND:VCARD");
			$clientContainer->write();
			if(isset($member)){
				$member->SEPA=$sepaData;
				$member->write();
			}
		}
		return $basket;
	}
	private function makeHint($str,$replacement,$start,$end){
		$len=strlen($str);
		return substr_replace ($str,$replacement,$start,$len-$end);
	}
	public function validatePayment($basket,$data){
		//Injector::inst()->get(LoggerInterface::class)->error('PaymentMethod SEPA validatePayment verify: '.$this->verifyIBAN($data->IBAN));
		if($data->IBAN==$basket->ClientContainer->IBAN_Hint){
			//IBAN ist bereits gespeichert
		}else{
			//IBAN ist nicht gespeichert, bzw. wurder geaendert
			if(!$this->validateIBAN($data->IBAN)){
				return "Bitte überprüfe die eingegebene IBAN";
			}
		}
		if($data->BIC==$basket->ClientContainer->BIC_Hint){
			//BIC ist bereits gespeichert
		}else{
			//BIC ist nicht gespeichert, bzw. wurde geaendert
			if(!$this->validateBIC($data->BIC)){
				return "Bitte überprüfe die eingegebene BIC";
			}
		}
		
		return "good";
	}
	public function verifyIBAN($iban){
		
		//$ibanClass=new IBAN($iban);
		$verify=$this->validateIBAN($iban);
		if($verify){
			return "good";
		}else{
			return "error";
		}
		
	}
	public function validateBIC($bic){
		if( preg_match('/^[a-z]{6}[0-9a-z]{2}([0-9a-z]{3})?\z/i', strtolower($bic))){
			Injector::inst()->get(LoggerInterface::class)->error('PaymentMethod SEPA BIC OK: ');
			return true;
		}else{
			Injector::inst()->get(LoggerInterface::class)->error('PaymentMethod SEPA BIC FALSE: '.strtolower($bic));
			return false;
		}
	}
	public function validateIBAN($input)
    {
        $iban = strtolower($input);

        // The official min length is 5. Also prevents substringing too short input.
        $iban = strtolower(str_replace(' ','',$iban));
    $Countries = array('al'=>28,'ad'=>24,'at'=>20,'az'=>28,'bh'=>22,'be'=>16,'ba'=>20,'br'=>29,'bg'=>22,'cr'=>21,'hr'=>21,'cy'=>28,'cz'=>24,'dk'=>18,'do'=>28,'ee'=>20,'fo'=>18,'fi'=>18,'fr'=>27,'ge'=>22,'de'=>22,'gi'=>23,'gr'=>27,'gl'=>18,'gt'=>28,'hu'=>28,'is'=>26,'ie'=>22,'il'=>23,'it'=>27,'jo'=>30,'kz'=>20,'kw'=>30,'lv'=>21,'lb'=>28,'li'=>21,'lt'=>20,'lu'=>20,'mk'=>19,'mt'=>31,'mr'=>27,'mu'=>30,'mc'=>27,'md'=>24,'me'=>22,'nl'=>18,'no'=>15,'pk'=>24,'ps'=>29,'pl'=>28,'pt'=>25,'qa'=>29,'ro'=>24,'sm'=>27,'sa'=>24,'rs'=>22,'sk'=>24,'si'=>19,'es'=>24,'se'=>24,'ch'=>21,'tn'=>24,'tr'=>26,'ae'=>23,'gb'=>22,'vg'=>24);
    $Chars = array('a'=>10,'b'=>11,'c'=>12,'d'=>13,'e'=>14,'f'=>15,'g'=>16,'h'=>17,'i'=>18,'j'=>19,'k'=>20,'l'=>21,'m'=>22,'n'=>23,'o'=>24,'p'=>25,'q'=>26,'r'=>27,'s'=>28,'t'=>29,'u'=>30,'v'=>31,'w'=>32,'x'=>33,'y'=>34,'z'=>35);

    if(isset($Countries[substr($iban,0,2)]) && strlen($iban) == $Countries[substr($iban,0,2)]){

        $MovedChar = substr($iban, 4).substr($iban,0,4);
        $MovedCharArray = str_split($MovedChar);
        $NewString = "";

        foreach($MovedCharArray AS $key => $value){
            if(!is_numeric($MovedCharArray[$key])){
                $MovedCharArray[$key] = $Chars[$MovedCharArray[$key]];
            }
            $NewString .= $MovedCharArray[$key];
        }

        if(bcmod($NewString, '97') == 1)
        {
            return true;
        }
    }
    return false;

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
	private function encryptData($data){
		
		$config = array(
			"digest_alg" => "sha512",
			"private_key_bits" => 2048,
			"private_key_type" => OPENSSL_KEYTYPE_RSA,
		);
		//ENCRYPT 
		$keyfile="file://".dirname(__DIR__,5).DIRECTORY_SEPARATOR."app".DIRECTORY_SEPARATOR."sepaPubKey.pem"; //absolute path
		Injector::inst()->get(LoggerInterface::class)->error('pfad='."file://".__DIR__.DIRECTORY_SEPARATOR."solaPubKey.pem");
		Injector::inst()->get(LoggerInterface::class)->error('pfad='."file://".dirname(__DIR__,5).DIRECTORY_SEPARATOR."app".DIRECTORY_SEPARATOR."sepaPubKey.pem");
		$pubKey = openssl_pkey_get_details(openssl_pkey_get_public($keyfile))['key'];
		openssl_public_encrypt($data, $secret, $pubKey);
		
		//DECRYPT
		/*
		$keyfile="file://".__DIR__.DIRECTORY_SEPARATOR."solaPrvKey.pem"; //absolute path
		$privKey=openssl_pkey_get_private(openssl_pkey_get_private($keyfile));
		openssl_private_decrypt($secret, $decrypted, $privKey);
		Injector::inst()->get(LoggerInterface::class)->error('----pubKey= '.$decrypted);
		*/
		return $this->base64_encode_linebreak($secret);
	}
	function base64_encode_linebreak($data) {
		$data = base64_encode($data);
		$datalb = "";
		while (strlen($data) > 64) {
			$datalb .= substr($data, 0, 64) . "\n";
			$data = substr($data,64);
		}
		$datalb .= $data;
		return $datalb;
	} 
}
class PaymentMethod_SEPA_Member_Extension extends DataExtension {
	private static $db=[
		"SEPA"=>"Text",
		"IBAN_Hint"=>"Varchar(50)",
		"IBAN_Hash"=>"Text",
		"BIC_Hint"=>"Varchar(20)",
		"BIC_HASH"=>"Text"
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
				//$this->owner->setField("IBAN",$this->generateRSA($this->owner->getField("IBAN")));
			
		}
		parent::onBeforeWrite();
	}
}
class PaymentMethod_SEPA_ClientContainer_Extension extends DataExtension {
	private static $db=[
		"SEPA"=>"Text",
		"IBAN_Hint"=>"Varchar(50)",
		"IBAN_Hash"=>"Text",
		"BIC_Hint"=>"Varchar(20)",
		"BIC_HASH"=>"Text"
	];
}
?>
