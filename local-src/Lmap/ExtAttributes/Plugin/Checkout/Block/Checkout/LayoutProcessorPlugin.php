<?php
namespace Lmap\ExtAttributes\Plugin\Checkout\Block\Checkout;

class LayoutProcessorPlugin
{
    /**
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $subject
     * @param array $jsLayout
     * @return array
     */
    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array  $jsLayout
    ) {

        $customAttributeCode = 'suburb';
        $suburb= [
            'component' => 'Magento_Ui/js/form/element/abstract',
            'config' => [
                // customScope is used to group elements within a single form (e.g. they can be validated separately)
                'customScope' => 'shippingAddress.custom_attributes',
                'customEntry' => null,
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/input',
                'tooltip' => [
                    'description' => 'Suburb for Australian address',
                ],
                'id' => 'suburb'

            ],
            'dataScope' => 'shippingAddress.custom_attributes' . '.' . $customAttributeCode,
            'label' => __('Suburb'),
            'provider' => 'checkoutProvider',
            'sortOrder' => 100,
            'validation' => [
                'required-entry' => true
            ],
            'options' =>[],
            'filterBy' => null,
            'customEntry' => null,
            'visible' => true,
            'id' => 'suburb'
        ];
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children'][$customAttributeCode] =$suburb;
        //$jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
        //['payment']['children']['payments-list']['children'];

        return $jsLayout;
    }
}