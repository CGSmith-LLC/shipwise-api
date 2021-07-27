<?php


namespace common\models;


class Fulfillment extends base\BaseFulfillment
{
    public function getAdapter()
    {
		$adaptername = "\\common\\adapters\\fulfillment\\" . $this->name . "Adapter";
    }

    /**
     */
    public function getService()
    {

    }
}