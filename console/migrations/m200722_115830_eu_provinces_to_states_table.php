<?php

use yii\db\Migration;

class m200722_115830_eu_provinces_to_states_table extends Migration
{
    public function safeUp()
    {
        $this->insert('{{%states}}', [
            'name' => 'La Coruna',
            'abbreviation' => 'C',
            'country' => 'ES'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Alava',
            'abbreviation' => 'VI',
            'country' => 'ES'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Albacete',
            'abbreviation' => 'AB',
            'country' => 'ES'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Alicante',
            'abbreviation' => 'A',
            'country' => 'ES'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Almeria',
            'abbreviation' => 'AL',
            'country' => 'ES'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Asturias',
            'abbreviation' => '0',
            'country' => 'ES'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Aliva',
            'abbreviation' => 'AV',
            'country' => 'ES'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Badajoz',
            'abbreviation' => 'BA',
            'country' => 'ES'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Balears',
            'abbreviation' => 'PM',
            'country' => 'ES'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Barcelona',
            'abbreviation' => 'B',
            'country' => 'ES'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Bizkaia',
            'abbreviation' => 'BI',
            'country' => 'ES'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Burgos',
            'abbreviation' => 'BU',
            'country' => 'ES'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Caceres',
            'abbreviation' => 'CC',
            'country' => 'ES'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Cadiz',
            'abbreviation' => 'CA',
            'country' => 'ES'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Cantabria',
            'abbreviation' => 'S',
            'country' => 'ES'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Castellon',
            'abbreviation' => 'CS',
            'country' => 'ES'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Ciudad Real',
            'abbreviation' => 'CR',
            'country' => 'ES'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Cordoba',
            'abbreviation' => 'CO',
            'country' => 'ES'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Cuenca',
            'abbreviation' => 'CU',
            'country' => 'ES'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Gipuzkoa',
            'abbreviation' => 'SS',
            'country' => 'ES'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Girona',
            'abbreviation' => 'GI',
            'country' => 'ES'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Granada',
            'abbreviation' => 'GR',
            'country' => 'ES'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Guadalajara',
            'abbreviation' => 'GU',
            'country' => 'ES'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Huelva',
            'abbreviation' => 'H',
            'country' => 'ES'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Huesca',
            'abbreviation' => 'HU',
            'country' => 'ES'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Jaen',
            'abbreviation' => 'J',
            'country' => 'ES'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'La Rioja',
            'abbreviation' => 'LO',
            'country' => 'ES'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Las Palmas',
            'abbreviation' => 'GC',
            'country' => 'ES'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Leon',
            'abbreviation' => 'LE',
            'country' => 'ES'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Lleida',
            'abbreviation' => 'L',
            'country' => 'ES'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Lugo',
            'abbreviation' => 'LU',
            'country' => 'ES'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Madrid',
            'abbreviation' => 'M',
            'country' => 'ES'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Malaga',
            'abbreviation' => 'MA',
            'country' => 'ES'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Murica',
            'abbreviation' => 'MU',
            'country' => 'ES'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Navarra',
            'abbreviation' => 'NA',
            'country' => 'ES'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Ourense',
            'abbreviation' => 'OR',
            'country' => 'ES'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Palencia',
            'abbreviation' => 'P',
            'country' => 'ES'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Pontevedra',
            'abbreviation' => 'PO',
            'country' => 'ES'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Salamanca',
            'abbreviation' => 'SA',
            'country' => 'ES'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Santa Cruz de Tenerife',
            'abbreviation' => 'TF',
            'country' => 'ES'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Segovia',
            'abbreviation' => 'SG',
            'country' => 'ES'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Sevilla',
            'abbreviation' => 'SE',
            'country' => 'ES'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Soria',
            'abbreviation' => 'SO',
            'country' => 'ES'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Tarragona',
            'abbreviation' => 'T',
            'country' => 'ES'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Teruel',
            'abbreviation' => 'TE',
            'country' => 'ES'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Toledo',
            'abbreviation' => 'TO',
            'country' => 'ES'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Valencia',
            'abbreviation' => 'V',
            'country' => 'ES'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Valladolid',
            'abbreviation' => 'VA',
            'country' => 'ES'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Zamora',
            'abbreviation' => 'ZA',
            'country' => 'ES'
        ]);
        $this->insert('{{%states}}', [
            'name' => 'Zaragoza',
            'abbreviation' => 'Z',
            'country' => 'ES'
        ]);

    }

    public function safeDown()
    {

    }
}