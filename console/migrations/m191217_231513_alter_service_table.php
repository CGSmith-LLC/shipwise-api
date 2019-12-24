<?php

use yii\db\Migration;

/**
 * Class m191217_231513_alter_service_table
 *
 * This migration is to add new columns to `service` table and populate with data.
 *
 */
class m191217_231513_alter_service_table extends Migration
{

    public $tableName = '{{%service}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        /**
         * Add new columns
         */
        $this->addColumn($this->tableName, 'shipwise_code', $this
            ->string(50)
            ->notNull()
            ->comment('ShipWise service code'));

        $this->addColumn($this->tableName, 'carrier_code', $this
            ->string(50)
            ->notNull()
            ->comment('Service code name as used by carrier\'s API'));

        /**
         * Update all existing services in db table
         */
        $this->update($this->tableName, [
            'shipwise_code' => 'to-be-defined',
            'carrier_code'  => 'to-be-defined',
        ]);

        /**
         * Remove a duplicate
         */
        $this->delete($this->tableName, ['id' => 15]); // FedEx Express Saver duplicate

        /**
         * Update existing FedEx and UPS services with its corresponding shipwise and carrier codes.
         */
        $mapping = [
            // FedEx
            '14' => ['FedExGround', 'FEDEX_GROUND'],
            '16' => ['FedEx2Day', 'FEDEX_2_DAY'],
            '17' => ['FedEx2DayAM', 'FEDEX_2_DAY_AM'],
            '18' => ['FedExFirstOvernight', 'FIRST_OVERNIGHT'],
            '19' => ['FedExPriorityOvernight', 'PRIORITY_OVERNIGHT'],
            '20' => ['FedExStandardOvernight', 'STANDARD_OVERNIGHT'],
            '27' => ['FedExGroundHome', 'GROUND_HOME_DELIVERY'],
            '28' => ['FedExExpressSaver', 'FEDEX_EXPRESS_SAVER'],
            '65' => ['FedEx1DayFreight', 'FEDEX_1_DAY_FREIGHT'],
            '66' => ['FedEx2DayFreight', 'FEDEX_2_DAY_FREIGHT'],
            '67' => ['FedEx3DayFreight', 'FEDEX_3_DAY_FREIGHT'],
            // UPS
            '21' => ['UPSGround', '03'],
            '22' => ['UPS3DaySelect', '12'],
            '23' => ['UPS2ndDayAir', '02'],
            '24' => ['UPS2ndDayAirAM', '59'],
            '25' => ['UPSNextDayAirSaver', '13'],
            '26' => ['UPSNextDayAir', '01'],
            '30' => ['UPSNextDayAirEarlyAM', '14'],
            '61' => ['UPSWorldwideExpressPlus', '54'],
            '62' => ['UPSWorldwideExpedited', '08'],
            '63' => ['UPSStandard', '11'],
            '64' => ['UPSWorldwideExpress', '07'],
        ];

        foreach ($mapping as $id => $codes) {
            $this->updateServiceCodes((int)$id, $codes[0], $codes[1]);
        }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'shipwise_code');
        $this->dropColumn($this->tableName, 'carrier_code');
    }

    /**
     * @param int    $id
     * @param string $shipwiseCode
     * @param string $carrierCode
     */
    private function updateServiceCodes($id, $shipwiseCode, $carrierCode)
    {
        return $this->update($this->tableName, [
            'shipwise_code' => $shipwiseCode,
            'carrier_code'  => $carrierCode,
        ], ['id' => $id]);
    }
}
