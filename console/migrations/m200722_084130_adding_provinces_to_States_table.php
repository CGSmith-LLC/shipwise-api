<?php

use yii\db\Migration;

class m200722_084130_adding_provinces_to_States_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%states}}', [
            'name' => 'Newfoundland and Labrador',
            'abbreviation' => 'NL',
            'country' => 'CA'
        ]);

        $this->insert('{{%states}}', [
            'name' => 'Prince Edward Island',
            'abbreviation' => 'PE',
            'country' => 'CA'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'British Columbia',
            'abbreviation' => 'BC',
            'country' => 'CA'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Alberta',
            'abbreviation' => 'AB',
            'country' => 'CA'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Manitoba',
            'abbreviation' => 'MB',
            'country' => 'CA'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'New Brunswick',
            'abbreviation' => 'NB',
            'country' => 'CA'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Northwest Territories',
            'abbreviation' => 'NT',
            'country' => 'CA'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Nova Scotia',
            'abbreviation' => 'NS',
            'country' => 'CA'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Nunavut',
            'abbreviation' => 'NU',
            'country' => 'CA'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Ontario',
            'abbreviation' => 'ON',
            'country' => 'CA'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Quebec',
            'abbreviation' => 'QC',
            'country' => 'CA'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Saskatchewan',
            'abbreviation' => 'SK',
            'country' => 'CA'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Yukon',
            'abbreviation' => 'YT',
            'country' => 'CA'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Aguascalientes',
            'abbreviation' => 'AG',
            'country' => 'MX'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Baja California',
            'abbreviation' => 'BC',
            'country' => 'MX'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Baja California Sur',
            'abbreviation' => 'BS',
            'country' => 'MX'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Campeche',
            'abbreviation' => 'CM',
            'country' => 'MX'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Chiapas',
            'abbreviation' => 'CS',
            'country' => 'MX'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Chihuahua',
            'abbreviation' => 'CH',
            'country' => 'MX'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Coahuila',
            'abbreviation' => 'CO',
            'country' => 'MX'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Colima',
            'abbreviation' => 'CL',
            'country' => 'MX'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Mexico City',
            'abbreviation' => 'DF',
            'country' => 'MX'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Durango',
            'abbreviation' => 'DG',
            'country' => 'MX'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Guanajuato',
            'abbreviation' => 'GT',
            'country' => 'MX'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Guerrero',
            'abbreviation' => 'GR',
            'country' => 'MX'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Hidalgo',
            'abbreviation' => 'HG',
            'country' => 'MX'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Jalisco',
            'abbreviation' => 'JA',
            'country' => 'MX'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Mexico',
            'abbreviation' => 'EM',
            'country' => 'MX'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Michoacan',
            'abbreviation' => 'MI',
            'country' => 'MX'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Morelos',
            'abbreviation' => 'MO',
            'country' => 'MX'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Nayarit',
            'abbreviation' => 'NA',
            'country' => 'MX'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Nuevo Leon',
            'abbreviation' => 'NL',
            'country' => 'MX'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Oaxaca',
            'abbreviation' => 'OA',
            'country' => 'MX'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Puebla',
            'abbreviation' => 'PU',
            'country' => 'MX'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Queretaro',
            'abbreviation' => 'QT',
            'country' => 'MX'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Quintana Roo',
            'abbreviation' => 'QR',
            'country' => 'MX'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'San Luis Potosi',
            'abbreviation' => 'SL',
            'country' => 'MX'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Sinaloa',
            'abbreviation' => 'SI',
            'country' => 'MX'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Sonora',
            'abbreviation' => 'SO',
            'country' => 'MX'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Tabasco',
            'abbreviation' => 'TB',
            'country' => 'MX'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Tamaulipas',
            'abbreviation' => 'TM',
            'country' => 'MX'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Tlaxcala',
            'abbreviation' => 'TL',
            'country' => 'MX'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Veracruz',
            'abbreviation' => 'VE',
            'country' => 'MX'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Yucatan',
            'abbreviation' => 'YU',
            'country' => 'MX'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Zacatecas',
            'abbreviation' => 'ZA',
            'country' => 'MX'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Nord-Est',
            'abbreviation' => 'Nord-Est',
            'country' => 'RO'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Sud-Est',
            'abbreviation' => 'Sud-Est',
            'country' => 'RO'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Sud-Muntenia',
            'abbreviation' => 'Sud-Muntenia',
            'country' => 'RO'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Sud-Vest Olentia',
            'abbreviation' => 'Sud-Vest Ole',
            'country' => 'RO'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Vest',
            'abbreviation' => 'Vest',
            'country' => 'RO'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Nord-Vest',
            'abbreviation' => 'Nord-Vest',
            'country' => 'RO'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Centru',
            'abbreviation' => 'Centru',
            'country' => 'RO'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Bucuresti-Illfov',
            'abbreviation' => 'Bucuresti-Il',
            'country' => 'RO'
        ]);



    }
    public function safeDown()
    {

    }
}