<?php

namespace console\jobs\platforms\shopify;

use yii\base\BaseObject;
use yii\queue\RetryableJobInterface;
use PHPShopify\ShopifySDK;
use common\services\platforms\ShopifyService;

/**
 * Class UnregisterShopifyWebhookListenersJob
 * @package console\jobs\platforms\shopify
 */
class UnregisterShopifyWebhookListenersJob extends BaseObject implements RetryableJobInterface
{
    public string $shopUrl;
    public string $accessToken;

    protected ?string $domain = null;

    public function execute($queue): void
    {
        // We don't use the `ShopifyService` since the related `EcommerceIntegration` does not exist at this point any more:
        $shopify = new ShopifySDK([
            'ApiVersion' => ShopifyService::API_VERSION,
            'ShopUrl' => $this->shopUrl,
            'AccessToken' => $this->accessToken
        ]);

        $webhooksList = $shopify->Webhook()->get();

        if ($webhooksList) {
            foreach ($webhooksList as $webhook) {
                if (isset($webhook['id'])) {
                    $shopify->Webhook((int)$webhook['id'])->delete();
                }
            }
        }
    }

    public function canRetry($attempt, $error): bool
    {
        return ($attempt < 3);
    }

    public function getTtr(): int
    {
        return 5 * 60;
    }
}
