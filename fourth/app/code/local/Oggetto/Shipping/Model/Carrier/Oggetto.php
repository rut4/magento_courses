<?php
/**
 * Oggetto Web shipping extension for Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade
 * the Oggetto Shipping module to newer versions in the future.
 * If you wish to customize the Oggetto Shipping module for your needs
 * please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Oggetto
 * @package    Oggetto_Shipping
 * @copyright  Copyright (C) 2014 Oggetto Web ltd (http://oggettoweb.com/)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Ems carrier model
 *
 * @category   Oggetto
 * @package    Oggetto_Shipping
 * @subpackage Model
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_Shipping_Model_Carrier_Oggetto extends Mage_Shipping_Model_Carrier_Abstract
{
    protected $_code = 'oggetto';

    /**
     * Get this module helper
     *
     * @return Oggetto_Shipping_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('oggettoshipping');
    }

    /**
     * Collect and get rates
     *
     * @param   Mage_Shipping_Model_Rate_Request $request
     * @return  Mage_Shipping_Model_Rate_Result|bool
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        $orig = [
            'country'   => $this->_helper()->getOriginCountryId(),
            'region'    => $this->_helper()->getOriginRegionId(),
            'city'      => $this->_helper()->getOriginCity()
        ];

        $dest = [
            'country'   => $request->getDestCountryId(),
            'region'    => $request->getDestRegionId(),
            'city'      => $request->getDestCity()
        ];

        $this->_translateLocationIdsToNames($orig);
        $this->_translateLocationIdsToNames($dest);

        /** @var Mage_Shipping_Model_Rate_Result $result */
        $result = Mage::getModel('shipping/rate_result');

        /** @var Oggetto_Shipping_Model_Api $api */
        $api = Mage::getModel('oggettoshipping/api');

        $prices = $api->getPrices($orig, $dest);

        /** @var Mage_Directory_Model_Currency $currency */
        $currency = Mage::getModel('directory/currency')->setData([
            'currency_code' => Mage::app()->getStore()->getCurrentCurrency()
        ]);
        $rateFromBaseToRub = $currency->getRate('RUB');

        foreach ($this->getAllowedMethods() as $method => $title) {
            $price = round($prices[$method] / $rateFromBaseToRub, 2);
            $result->append($this->_getRateMethod($method, $price));
        }

        return $result;
    }

    /**
     * Translate ids to names of country and region
     *
     * @param $address Array contain country and region
     */
    protected function _translateLocationIdsToNames($address)
    {
        foreach (['country', 'region'] as $location) {
            $model = Mage::getModel("directory/{$location}");
            $address[$location] = $model->load($address[$location])->getDefaultName();
        }
    }

    protected function _getRateMethod($method, $price)
    {
        /** @var Mage_Shipping_Model_Rate_Result_Method $rate */
        $rate = Mage::getModel('shipping/rate_result_method');

        $rate->setCarrier($this->_code);
        $rate->setCarrierTitle($this->getConfigData('title'));

        $rate->setMethod($method);
        $rate->setMethodTitle($this->getAllowedMethods()[$method]);

        $rate->setPrice($price);
        $rate->setCost($price);

        return $rate;
    }

    /**
     * Get allowed methods
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        return [
            'standard' => Mage::helper('shipping')->__('Standard'),
            'fast' => Mage::helper('shipping')->__('Fast')
        ];
    }
}