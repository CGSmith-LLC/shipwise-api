<?php


namespace common\components;


use Solarium\Client;
use Solarium\Core\Client\Adapter\Curl;
use Symfony\Component\EventDispatcher\EventDispatcher;

class SolrService extends \yii\base\Component
{

    public $options = [];
    /** @var \Solarium\Core\Client\Client */
    public $client;

    public function init()
    {
        $this->client = new Client((new Curl()), (new EventDispatcher()), $this->options);

        parent::init();
    }

    public function search($query, $customerIds, $offset, $rows)
    {
        $select = $this->client->createSelect();

        if (empty($query)) {
            $select->setQuery('*:*');
        } else {
            $select->setQuery('_text_:' . $query);
        }
        if (!empty($customerIds)) {
            $customerIdsOr = implode(' OR ', $customerIds);
            $select->createFilterQuery('customer_ids')->setQuery('customer_id_i:('. $customerIdsOr .')');
        }
        $select->setStart($offset)->setRows($rows);
        $select->addSort('created_date_dt', $select::SORT_DESC);

        $resultset = $this->client->select($select);
        return $resultset;
    }

    public function createDocument(array $orders)
    {
        $update = $this->client->createUpdate();
        $docs = [];
        foreach ($orders as $order) {

            $doc = $update->createDocument();
            $doc->id = $order->id;
            $doc->customer_id_i = $order->customer_id;
            $doc->created_date_dt = $order->created_date;
            if ($order->status) {
                $doc->status_id_i = $order->status_id;
                $doc->status_id_t = $order->status->name;
            }
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
            $docs[] = $doc;
        }

        $update->addDocuments($docs);
        $update->addCommit();
        $this->client->update($update);
    }

}