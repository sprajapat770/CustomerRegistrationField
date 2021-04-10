<?php


namespace Magento360\CustomerRegistrationField\Setup;


use Magento\Customer\Model\Customer;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements \Magento\Framework\Setup\InstallDataInterface
{
    private $eavSetupFactory;

    private $eavConfig;

    private $attributeResource;

    /**
     * InstallData constructor.
     * @param \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Customer\Model\ResourceModel\Attribute $attributeResource
     */
    public function __construct(
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Customer\Model\ResourceModel\Attribute $attributeResource
    )
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig = $eavConfig;
        $this->attributeResource = $attributeResource;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $customField = "custom_company_field";
        $customFieldLabel = "Company Name";
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->removeAttribute(Customer::ENTITY, $customField);

        $attributeSetId = $eavSetup->getDefaultAttributeSetId(Customer::ENTITY);
        $attributeGroupId = $eavSetup->getDefaultAttributeGroupId(Customer::ENTITY);

        $eavSetup->addAttribute(Customer::ENTITY, $customField, [
            // Attribute parameters
            'type' => 'varchar',
            'label' => $customFieldLabel,
            'input' => 'text',
            'required' => true,
            'visible' => true,
            'user_defined' => true,
            'sort_order' => 990,
            'position' => 990,
            'system' => 0,
        ]);

        $attribute = $this->eavConfig->getAttribute(Customer::ENTITY, $customField);
        $attribute->setData('attribute_set_id', $attributeSetId);
        $attribute->setData('attribute_group_id', $attributeGroupId);

        /*
        //You can use this attribute in the following forms
        adminhtml_checkout
        adminhtml_customer // for admin page under customer edit account info
        adminhtml_customer_address
        customer_account_create // for store-front registration page
        customer_account_edit // for store-front after logged-in account edit page
        customer_address_edit
        customer_register_address
        */

        $attribute->setData('used_in_forms', [
            'adminhtml_customer',
            'customer_account_create',
            'customer_account_edit'
        ]);

        $this->attributeResource->save($attribute);
    }
}

