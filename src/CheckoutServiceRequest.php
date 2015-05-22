<?php
/**
 * Copyright 2015 Klarna AB
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * File containing the class to perform a checkout service call
 *
 * PHP version 5.3
 *
 * @category  Payment
 * @package   KlarnaAPI
 * @author    Klarna <support@klarna.com>
 * @copyright 2014 Klarna AB
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://developers.klarna.com/
 */

/**
 * CheckoutService
 *
 * @category  Payment
 * @package   KlarnaAPI
 * @author    Klarna <support@klarna.com>
 * @copyright 2014 Klarna AB
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://developers.klarna.com/
 */
class CheckoutServiceRequest
{
    /**
     * @var ArrayAccess
     */
    protected $config;

    /**
     * @var array
     */
    protected $params;

    /**
     * @var string
     */
    protected $uri = 'https://api.klarna.com/touchpoint/checkout/';

    /**
     * @var string
     */
    protected $accept = 'application/vnd.klarna.touchpoint-checkout.payment-methods-v1+json';

    /**
     * Constructor
     *
     * @param ArrayAccess $config Configuration
     * @param array       $params Parameters used to build query.
     */
    public function __construct($config, $params)
    {
        $this->config = $config;
        $this->params = array_filter($params);

        if (isset($config['checkout_service_uri'])) {
            $this->uri = $config['checkout_service_uri'];
        }
    }

    /**
     * Create a checkout service response
     *
     * @param int    $code HTTP status code
     * @param string $body HTTP body
     *
     * @return CheckoutServiceResponse Checkout service response
     */
    public function createResponse($code, $body)
    {
        return new CheckoutServiceResponse($this, $code, $body);
    }

    /**
     * Get the url associated to this request
     *
     * @return string URL with query
     */
    public function getURL()
    {
        return $this->uri . '?'. http_build_query($this->params);
    }

    /**
     * Get the headers associated to this request
     *
     * @return array Array of headers strings
     */
    public function getHeaders()
    {
        $digest = Klarna::digest(
            Klarna::colon(
                $this->config['eid'],
                $this->params['currency'],
                $this->config['secret']
            )
        );

        return array(
            "Accept: {$this->accept}",
            "Authorization: xmlrpc-4.2 {$digest}"
        );
    }
}
