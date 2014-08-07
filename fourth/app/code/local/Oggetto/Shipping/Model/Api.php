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
 * Oggetto API model
 *
 * @category   Oggetto
 * @package    Oggetto_Shipping
 * @subpackage Model
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_Shipping_Model_Api
{
    const RESPONSE_STATUS_SUCCESS = 'success';
    const RESPONSE_STATUS_ERROR = 'error';

    private $_apiUrl;
    private $_httpClient;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_apiUrl = 'http://new.oggy.co/shipping/api/rest.php';
    }

    /**
     * Get clean http client
     *
     * @return Zend_Http_Client
     */
    protected function _getClient()
    {
        if (is_null($this->_httpClient)) {
            $this->_httpClient = new Varien_Http_Client($this->_apiUrl);
        }

        return $this->_httpClient->resetParameters(true);
    }

    /**
     * Make request to API with parameters and get result
     *
     * @param array $params Request parameters
     * @return mixed Request results
     */
    protected function _makeRequest($params)
    {
        try {
            $client = $this->_getClient();

            $client->setParameterGet($params);

            $response = $client->request(Varien_Http_Client::GET);

            if ($response->getStatus() == 200) {
                $response = Mage::helper('core')->jsonDecode($response->getBody());
                return $response;
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
        return [];
    }

    /**
     * Get prices
     *
     * @param string $orig Original
     * @param string $dest Destination
     * @return array|string Prices for two methods
     */
    public function getPrices($orig, $dest) {
        $response =  $this->_makeRequest([
            'from_country'  => $orig['country'],
            'from_region'   => $orig['region'],
            'from_city'     => $orig['city'],
            'to_country'    => $dest['country'],
            'to_region'     => $dest['region'],
            'to_city'       => $dest['city']
        ]);
        if (empty($response)) {
            return [];
        }
        if ($response['status'] == self::RESPONSE_STATUS_SUCCESS) {
            return $response['prices'];
        } else {
            return $response['message'];
        }
    }
}

