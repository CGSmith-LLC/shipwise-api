<?php

use yii\db\Migration;

/**
 * Class m200722_183521_add_countries_to_country_table
 */
class m200722_183521_add_countries_to_country_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $countries = \common\models\Country::getExistingList();

        foreach ($countries as $isoCode => $name) {
            $country = new \common\models\Country([
                'name' => $name,
                'abbreviation' => $isoCode,
            ]);
            $country->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200722_183521_add_countries_to_country_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200722_183521_add_countries_to_country_table cannot be reverted.\n";

        return false;
    }
    */
}
