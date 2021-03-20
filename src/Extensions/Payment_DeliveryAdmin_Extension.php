<?php

namespace Schrattenholz\Payment;
use SilverStripe\Core\Extension;
use Schrattenholz\Order\OrderConfig;
use Schrattenholz\Order\Unit;
use Schrattenholz\Order\Ingredient;
use Schrattenholz\Order\Addon;


use SilverStripe\Forms\DateField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\DropdownField;
use Terraformers\RichFilterHeader\Form\GridField\RichFilterHeader;
use SilverStripe\Forms\Form;
use SilverStripe\Core\Injector\Injector;
use Psr\Log\LoggerInterface;
use Symbiote\GridFieldExtensions\GridFieldAddNewMultiClass;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\Forms\GridField\GridFieldButtonRow;
use SilverStripe\Forms\GridField\GridFieldToolbarHeader;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\Forms\GridField\GridFieldDataColumns;
use SilverStripe\Forms\GridField\GridFieldEditButton;
use SilverStripe\Forms\GridField\GridFieldDetailForm;
use SilverStripe\Forms\GridField\GridFieldSortableHeader;
use SilverStripe\Forms\GridField\GridFieldPaginator;
use SilverStripe\Forms\GridField\GridFieldFilterHeader;

use Symbiote\GridFieldExtensions\GridFieldEditableColumns;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;

class Payment_DeliveryAdmin_Extension extends Extension
{
    private static $managed_models = [
		PaymentMethod::class
    ];

      public function updateEditForm(&$form) 
    {
		
		//$form = parent::getEditForm($id, $fields);
		 $gridField = $form->Fields()->fieldByName('Schrattenholz-Payment-PaymentMethod');
		
			
			if($gridField) {
				Injector::inst()->get(LoggerInterface::class)->error('-----------------____-----_____ Schrattenholz-Payment-PaymentMethod');
					$useExtendedConfig=true;
					
			$config = $gridField->getConfig();
			/*$config->addComponent(new GridFieldButtonRow('before'))
			->addComponent(new GridFieldDataColumns)
			//->addComponent(new GridFieldDeleteAction())
			->addComponent(new GridFieldEditButton())
			->addComponent(new GridFieldDetailForm())
			->addComponent(new GridFieldSortableHeader())
			->addComponent(new GridFieldFilterHeader())
			->addComponent(new GridFieldPaginator())
			//->addComponent(new GridFieldOrderableRows('SortID'))
		;*/
		 $multiClassConfig = new GridFieldAddNewMultiClass();
        $multiClassConfig->setClasses(
            array(
                'Schrattenholz\Payment\PaymentMethod_Collection',
				'Schrattenholz\Payment\PaymentMethod_SEPA',
				
            )
        );
		$config->addComponent($multiClassConfig);
		$config->removeComponentsByType('GridFieldAddNewButton');

					/*
					$dataColumns=$config->getComponentByType(GridFieldDataColumns::class);
					$dataColumns->setFieldCasting([
						"Created"=>"Date->Nice",
						"ShippingDate"=>"Date->Nice"
					]);
					$config->removeComponentsByType(GridFieldFilterHeader::class);
		
					$filter = new RichFilterHeader();
					$filter->setFilterConfig([
					'Created' => [
						'title' => 'Created',
						'filter' => 'GreaterThanOrEqualFilter',
					],
					'ShippingDate' => [
						'title' => 'ShippingDate',
						'filter' => 'PartialMatchFilter',
					],
					'DeliveryType.Title'=>[
						'title'=>'DeliveryType.Title',
						'filter'=>'PartialMatchFilter'
					],
					'ClientContainer.PhoneNumber' => [
						'title'=>'ClientContainer.PhoneNumber',
						'filter'=>'PartialMatchFilter',
					],
					'ClientContainer.Surname' => [
						'title'=>'ClientContainer.Surname',
						'filter'=>'PartialMatchFilter',
					],
					'ClientContainer.Email' => [
						'title'=>'ClientContainer.Email',
						'filter'=>'PartialMatchFilter',
					],
					'OrderStatus'=>[
						'title'=>'OrderStatus',
						'filter'=>'ExactMatchFilter'
					],
					'CollectionDay.Day'=>[
						'title'=>'CollectionDay.Day',
						'filter'=>'ExactMatchFilter'
					],
					'Route.Title'=>[
						'title'=>'Route.Title',
						'filter'=>'ExactMatchFilter'
					]
				])
				->setFilterFields([
					'Created' => DateField::create('', ''),
					'ShippingDate' => DateField::create('', ''),
					'ClientContainer.FirstName' => TextField::create(""),
					'ClientContainer.Surname' => TextField::create(""),
					'DeliveryType.Title' => TextField::create(""),
					'OrderStatus' => $orderStatus = DropdownField::create(
							'',
							'',
							singleton('Schrattenholz\OrderProfileFeature\OrderProfileFeature_ClientOrder')->dbObject('OrderStatus')->enumValues()
					 ),
					'CollectionDay.Day' => $collectionDay = DropdownField::create(
							'',
							'',
							singleton('Schrattenholz\Delivery\CollectionDay')->dbObject('Day')->enumValues()
					),
					'Route.Title' => $route = DropdownField::create(
							'',
							'',
							Route::get()->map('Title', 'Title')
					)
				]);
				$orderStatus->setHasEmptyDefault(true);
				$collectionDay->setHasEmptyDefault(true);
				$route->setHasEmptyDefault(true);
				$config->addComponent($filter, GridFieldPaginator::class);
				*/
			}
			
			return $form;
			
    }
}
	?>