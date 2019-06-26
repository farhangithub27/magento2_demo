<?php

/**
 * References :
 * https://devdocs.magento.com/guides/v2.3/extension-dev-guide/declarative-schema/data-patches.html
 * https://magento.stackexchange.com/questions/260408/magento2-new-customer-address-attribute-wont-save-to-database
 *
 * BOOK: Magento 2 Development Quick Start Guide page 52 EAV Entity List
 * The SELECT entity_type_code, entity_model FROM eav_entity_type; query
 * indicates that the following Magento entities are from an EAV model:
 *
 * customer : Magento\Customer\Model\ResourceModel\Customer
 * customer_address : Magento\Customer\Model\ResourceModel\Address
 * catalog_category : Magento\Catalog\Model\ResourceModel\Category
 * catalog_product : Magento\Catalog\Model\ResourceModel\Product
 * order : Magento\Sales\Model\ResourceModel\Order
 * invoice : Magento\Sales\Model\ResourceModel\Order\Invoice
 * creditmemo : Magento\Sales\Model\ResourceModel\Order\Creditmemo
 * shipment : Magento\Sales\Model\ResourceModel\Order\Shipment
 *
 * However, not all of them use the EAV model to its full extent as given by
 * following query. What this means is that only first four models in Magento Open Source really use EAV models
 * for managing their attributes and storing data vertically through EAV tables. The rest are
 * all flat tables, as all attributes and their values are in a single table.
 *
 * select distinct ea.entity_type_id,eet.entity_model
 * from eav_attribute as ea
 * inner join eav_entity_type as eet on
 * ea.entity_type_id = eet.entity_type_id;
 *
 * customer : Magento\Customer\Model\ResourceModel\Customer
 * customer_address : Magento\Customer\Model\ResourceModel\Address
 * catalog_category : Magento\Catalog\Model\ResourceModel\Category
 * catalog_product : Magento\Catalog\Model\ResourceModel\Product
 *
 */

namespace Lmap\ExtAttributes\Setup\Patch\Data;


use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;

use Magento\Customer\Api\AddressMetadataInterface;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Quote\Setup\QuoteSetupFactory;
use Magento\Sales\Setup\SalesSetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory;


use Magento\Sales\Setup\SalesSetup;
use Psr\Log\LoggerInterface;

/**
 */
class AddressSuburbPatch implements DataPatchInterface, PatchRevertableInterface
{
    /**
     * Attribute Code of the Custom Attribute
     */
    const CUSTOM_ATTRIBUTE_CODE = 'suburb';

    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var \Magento\Framework\Setup\ModuleContextInterface
     */
    private $context;

    /**
     * @var \Magento\Customer\Setup\CustomerSetupFactory
     */
    private $customerSetupFactory;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\SetFactory
     */
    private $attributeSetFactory;


    /**
     * @var \Magento\Quote\Setup\QuoteSetupFactory
     */
    private $quoteSetupFactory;


    /**
     * @var Magento\Sales\Setup\SalesSetupFactory
     */
    private $salesSetupFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CustomerSetupFactory $customerSetupFactory,
        QuoteSetupFactory $quoteSetupFactory,
        SalesSetupFactory $salesSetupFactory,
        SetFactory $attributeSetFactory,
        LoggerInterface $logger
    )
    {
        /**
         * If before, we pass $setup as argument in install/upgrade function, from now we start
         * inject it with DI. If you want to use setup, you can inject it, with the same way as here
         */
        $this->moduleDataSetup = $moduleDataSetup;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->quoteSetupFactory = $quoteSetupFactory;
        $this->salesSetupFactory = $salesSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        //The code that you want to apply in the patch
        //Please note, that one patch is responsible only for one setup version
        //So one UpgradeData can consist of few data patches

        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);

        /**
         * Two customer entities are defined in CustomerSetup.php (CustomerSetupFactory) @ line 128.
         * NOTE: Cannot use Customer Model since Customer Model doesn't have customer_address entity
         * Following tables are related with Customer EAV Entity after checking foreign keys
         * customer_address_entity
         * customer_address_entity_varchar (as we are adding a text filed 'suburb' which getting stored as varchar automatically.
         * Although suburb fieild is input is defined as text. This means input can ben text but field will be varchar of size 255
         * since text field is way larger than varchar).
         * eav_attribute
         * eav_entity_type
         * customer_eav_attribute
         * patch_list -> to remove applied patches to reapply them.
         * customer_form -> list of all forms related with customer entity
         */
        $customerSetup->addAttribute(AddressMetadataInterface::ENTITY_TYPE_ADDRESS, self::CUSTOM_ATTRIBUTE_CODE, [
            'input' => 'text',
            'label' => 'Suburb',
            'required' => 1,
            'position' => 105,
            'sort_order' => 105,
            'system'=> 0,
            'visible' => 0,
            'visible_on_front' => 1,
            'user_defined' => 1
        ]);

        // Fetching attribute set ID and attribute group ID
        $customerEntity = $customerSetup->getEavConfig()->getEntityType(AddressMetadataInterface::ENTITY_TYPE_ADDRESS);
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);


        $attribute = $customerSetup->getEavConfig()->getAttribute(
            AddressMetadataInterface::ENTITY_TYPE_ADDRESS,
            self::CUSTOM_ATTRIBUTE_CODE
        );


        /** TODO
         * NOTE Reference Mage2TV "Specifying the customer attribute set and used_in_forms properties"
         * Below two forms given in customer_form_table are for frontend customer forms which cutomer can use to change
         * its particulars from his/her account.
         * 'customer_account_create',
         * 'customer_account_edit'
         * However, these forms if added below in addData command it will only display the suburb field for
         * magento commerce while for community we have to modify templates to to display at frontend.
         * Becareful above forms are related to custoemr entity not customer_address entity.
         * However, Following same concept for frontend address fields we might need to change templates.
         * Frontend Customer related forms are
         * 'customer_address_edit',
         * 'customer_register_address'
         * For community edition these two frontend forms will take care of saving process but not enough for rendering
         * input fields on the frontend.
         */

        $attribute->addData([
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms' =>
                    [
                        'adminhtml_customer_address',
                        'customer_address_edit',
                        'customer_register_address',
                    ]
            ]
        );
        // In order to not to guess what the serialization rule is used define validation rules are below instead of
        // adding it to line 91 addAttribute.
        $attribute->setData('validate_rules',[
            'min_text_length' =>1,
            'max_text_length' => 15
        ]);
        $attribute->save();
        //$attribute->getResource()->save($attribute);
        //getResource is deprecated.

        $quoteSetup = $this->quoteSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $quoteSetup->addAttribute('quote_address', self::CUSTOM_ATTRIBUTE_CODE, [
            'input' => 'text',
            'label' => 'Suburb',
            'required' => 1,
            'position' => 105,
            'sort_order' => 105,
            'system'=> 0,
            'visible' => 1,
            'user_defined' => 1
        ]);

        $salesSetup = $this->salesSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $salesSetup->addAttribute('order_address', self::CUSTOM_ATTRIBUTE_CODE, [
            'input' => 'text',
            'label' => 'Suburb',
            'required' => 1,
            'position' => 105,
            'sort_order' => 105,
            'system'=> 0,
            'visible' => 1,
            'user_defined' => 1
        ]);

        $this->logger->debug('Script working');
        // OLD WAY of Adding Attributes to Flat Tables.
        // Adding suburb field to quote and sales_orders tables.
        /*
        $connection = $this->moduleDataSetup->getConnection();
        $connection->addColumn(
            $this->moduleDataSetup->getTable('quote_address'),
            'suburb',
            [
                'type' => 'varchar',
                'nullable' => false,
                'length' => 255,
                'comment' => 'Suburb for Australian Address'
            ]
        );

        $connection->addColumn(
            $this->moduleDataSetup->getTable('sales_order_address'),
            'suburb',
            [
                'type' => 'varchar',
                'nullable' => false,
                'length' => 255,
                'comment' => 'Suburb for Australian Address'
            ]
        );
        */
        $this->moduleDataSetup->getConnection()->endSetup();

    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        /**
         * This is dependency to another patch. Dependency should be applied first
         * One patch can have few dependencies
         * Patches do not have versions, so if in old approach with Install/Ugrade data scripts you used
         * versions, right now you need to point from patch with higher version to patch with lower version
         * But please, note, that some of your patches can be independent and can be installed in any sequence
         * So use dependencies only if this important for you
         */
        return [
            //SomeDependency::class
        ];
    }

    public function revert()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        //Here should go code that will revert all operations from `apply` method
        //Please note, that some operations, like removing data from column, that is in role of foreign key reference
        //is dangerous, because it can trigger ON DELETE statement
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        /**
         * This internal Magento method, that means that some patches with time can change their names,
         * but changing name should not affect installation process, that's why if we will change name of the patch
         * we will add alias here
         */
        return [];
    }

    /**
     * Reference https://markshust.com/2019/02/19/create-product-attribute-data-patch-magento-2.3-declarative-schema/
     * {@inheritdoc}
     */
    //public static function getVersion()
    //{
    //    return '2.0.6';
    //}
}
