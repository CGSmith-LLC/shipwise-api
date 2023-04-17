<?php

namespace common\components;

use yii\base\Component;
use yii\base\Exception;
use Stripe\StripeClient;

/**
 * Class StripeComponent
 * @package common\components
 */
class StripeComponent extends Component
{
    public string $publicKey;
    public string $privateKey;
    public string $pricingTableId;
    public string $customerPortalUrl;
    public string $webhookKey;
    public string $webhookSigningSecret;

    public StripeClient $client;

    /**
     * @throws Exception
     */
    public function init()
    {
        parent::init();

        if (!$this->publicKey) {
            throw new Exception('Stripe component needs `publicKey` set for initializing.');
        }

        if (!$this->privateKey) {
            throw new Exception('Stripe component needs `privateKey` set for initializing.');
        }

        if (!$this->pricingTableId) {
            throw new Exception('Stripe component needs `pricingTableId` set for initializing.');
        }

        if (!$this->customerPortalUrl) {
            throw new Exception('Stripe component needs `customerPortalUrl` set for initializing.');
        }

        if (!$this->webhookKey) {
            throw new Exception('Stripe component needs `webhookKey` set for initializing.');
        }

        if (!$this->webhookSigningSecret) {
            throw new Exception('Stripe component needs `webhookSigningSecret` set for initializing.');
        }

        $this->client = new StripeClient($this->privateKey);
    }
}
