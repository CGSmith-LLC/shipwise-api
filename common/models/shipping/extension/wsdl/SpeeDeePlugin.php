<?php

namespace common\models\shipping\extension;

use common\models\shipping\ShipmentPlugin;

class SpeeDeePlugin extends ShipmentPlugin
{
    /**
     * Plugin Name
     *
     * @var string Constant
     */
    const PLUGIN_NAME = "SpeeDee";

    private string $hostProd = '66.191.64.52';
    private string $hostDev = '';
    private string $ftpUser = '';
    private string $frpPassword = '';


    public function autoload($customerId = null)
    {
        // TODO: Implement autoload() method.
        // Shipper Id comes in from settings?
    }

    public function getPluginName()
    {
        return self::PLUGIN_NAME;
    }

    /**
     *
     * @return mixed|void
     */
    protected function ratePrepare()
    {
        // TODO: Implement ratePrepare() method.
    }

    protected function rateExecute()
    {
        // TODO: Implement rateExecute() method.
    }

    protected function rateProcess()
    {
        // TODO: Implement rateProcess() method.
    }

    protected function shipmentPrepare()
    {
        // TODO: Implement shipmentPrepare() method.
    }

    protected function shipmentExecute()
    {
        // TODO: Implement shipmentExecute() method.
    }

    protected function shipmentProcess()
    {
        // TODO: Implement shipmentProcess() method.
    }
}