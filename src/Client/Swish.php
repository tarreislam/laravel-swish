<?php

namespace Tarre\Swish\Client;

use Tarre\Swish\Exceptions\InvalidConfigurationOptionException;

class Swish
{
    public $base_uri = 'https://cpc.getswish.net/swish-cpcapi/api/v1';
    public $ca;
    public $key;
    public $merchant_number;

    public function __construct(array $config = null)
    {
        if (is_array($config)) {
            $this->loadConfig($config);
        } else {
            $this->loadConfig(config('swish'));
        }
    }

    /**
     * @param array $config
     * @throws InvalidConfigurationOptionException
     */
    public function loadConfig(array $config)
    {
        foreach ($config as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            } else {
                throw new InvalidConfigurationOptionException(sprintf('Unknown option "%s"', $key));
            }
        }
    }

    public function paymentRequest()
    {

    }

}