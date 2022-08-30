<?php

namespace console\controllers;

use Solarium;
use Symfony;
use frontend\models\Order;
use yii\console\Controller;


class SolrController extends Controller
{
    /**
     * Add all orders to the solr index
     */
    public function actionCreateIndex()
    {
        $adapter = new Solarium\Core\Client\Adapter\Curl();
        $eventDispatcher = new Symfony\Component\EventDispatcher\EventDispatcher();
        $config = array(
            'endpoint' => array(
                'localhost' => array(
                    'host' => 'solr',
                    'port' => 8983,
                    'path' => '/',
                    'core' => 'shipwise',
                )
            )
        );

        $client = new Solarium\Client($adapter, $eventDispatcher, $config);
        $query = $client->createPing();
        $client->ping($query);

        $orders_query = Order::find()
            ->with('address')
            ->with('address.state');

        $count = 0;
        foreach ($orders_query->batch(1000) as $orders) {
            $count += 1000;
            $this->stdout("Completed " . $count . PHP_EOL);
            $update = $client->createUpdate();
            $solr_documents = [];
            foreach ($orders as $order) {
                $doc = $update->createDocument();
                $doc->id = $order->id;
                $doc->customer_id_i = $order->customer_id;
                $doc->status_id_i = $order->status_id;
                if ($order->address) {
                    $doc->customer_name_t = $order->address->name;
                    $doc->customer_company_t = $order->address->company;
                    $doc->customer_address_t = $order->address->address1 . " " . $order->address->address2;
                    $doc->customer_city_t = $order->address->city;
                    if ($order->address->state) {
                        $doc->customer_state_name_t = $order->address->state->name;
                        $doc->customer_state_abb_t = $order->address->state->abbreviation;
                    }
                    $doc->customer_zip_t = $order->address->zip;
                    $doc->customer_email_t = $order->address->email;
                }
                $solr_documents[] = $doc;
            }
            $update->addDocuments($solr_documents);
            $update->addCommit();
            $client->update($update);
        }
    }
}
