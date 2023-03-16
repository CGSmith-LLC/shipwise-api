<?php

namespace tests\acceptance;

use AcceptanceTester;

/**
 * Class NotSignedInCest
 */
class NotSignedInCest
{

    public static $urls = [
        '/',
        '/order',
        '/order/batch',
        '/order/update/1',
        '/order/view/1',
        '/order/carrier-services?carrierId=1',
        '/order/country-states?country=US',
        '/order/packing-slip?id=1',
        '/order/shipping-slip?id=1',
        '/report',
        '/country',
        '/country/create',
        '/country/view/1',
        '/country/update/1',
        '/subscription',
        '/subscription/create',
        '/subscription/view/1',
        '/subscription/update/1',
        '/subscription/delete/1',
        '/one-time-charge',
        '/one-time-charge/create',
        '/one-time-charge/view?id=1',
        '/one-time-charge/update?id=1',
        '/one-time-charge/delete?id=1',
        '/billing',
        '/billing/create',
        '/sku',
        '/sku/create',
        '/sku/view/1',
        '/sku/update/1',
        '/sku/delete/1',
        '/invoice',
        '/invoice/view/1',
        '/user/settings/account',
        '/user/admin',
        '/user/admin/create',
        '/user/admin/switch?id=1',
        '/user/admin/resend-password?id=1',
        '/user/admin/associate-customers?id=1',
        '/user/admin/block?id=1',
        '/user/admin/update?id=1',
        '/user/admin/delete?id=1',

        // POSTS
        '/order/delete/1',
        '/country/delete/1',
        '/order/bulk',
    ];

    public function _before(AcceptanceTester $i)
    {
    }

    // tests
    public function tryToTest(AcceptanceTester $i)
    {
        foreach (self::$urls as $url) {
            $i->amOnPage($url);
            $i->see('Sign in');
        }
    }

}
