<?php


namespace common\services\ecommerce;


use common\models\Integration;
use common\models\IntegrationHookdeck;

abstract class EcommerceService extends BaseService
{
    public string|null $last_success_run = null;
    public int $page = 1;
    public int $perPage = 100;
    protected bool $canCreateWebhooks = false;
    public Integration $integration;
    public IntegrationHookdeck $hookdeck;

    public abstract function applyMeta(array $metadata);

    public function canCreateWebhooks()
    {
        return $this->canCreateWebhooks;
    }

    /**
     * Create hookdeck webhooks for the service to use
     */
    public function createHookdeck()
    {
        $this->hookdeck = new IntegrationHookdeck();
        $this->hookdeck->setAttributes([
            'source_name' => 'c' . $this->integration->customer_id . '-i' . $this->integration->id,
            'integration_id' => $this->integration->id,
        ]);
        $this->hookdeck->save(false);
    }

    public function getFullOrderDataIfNecessary($unparsedOrder)
    {
        return $unparsedOrder;
    }

}