<?php

namespace api\modules\v1\models\forms\shipment;

use common\models\Country;
use common\models\shipping\Shipment;
use yii\base\Model;

/**
 * @SWG\Definition(
 *     definition = "ShippingAddress",
 *     required   = { "country" },
 *     @SWG\Property(
 *            property = "country",
 *            type = "string",
 *            enum =
 *            {"US","CA","AU","FR","DE","IS","IE","IT","ES","SE","AT","BE","FI","CZ","DK","NO","GB","CH","NZ","RU","PT",
 *             "NL","IM","AF","AX","AL","DZ","AS","AD","AO","AI","AQ","AG","AR","AM","AW","AZ","BS","BH","BD","BB","BY",
 *             "BZ","BJ","BM","BT","BO","BQ","BA","BW","BV","BR","IO","BN","BG","BF","BI","KH","CM","CV","KY","CF","TD",
 *             "CL","CN","CX","CC","CO","KM","CG","CD","CK","CR","CI","HR","CU","CW","CY","DJ","DM","DO","EC","EG","SV",
 *             "GQ","ER","EE","ET","FK","FO","FJ","GF","PF","TF","GA","GM","GE","GH","GI","GR","GL","GD","GP","GU","GT",
 *             "GG","GN","GW","GY","HT","HM","VA","HN","HK","HU","IN","ID","IR","IQ","IL","JM","JP","JE","JO","KZ","KE",
 *             "KI","KP","KR","KW","KG","LA","LV","LB","LS","LR","LY","LI","LT","LU","MO","MK","MG","MW","MY","MV","ML",
 *             "MT","MH","MQ","MR","MU","YT","MX","FM","MD","MC","MN","ME","MS","MA","MZ","MM","NA","NR","NP","NC","NI",
 *             "NE","NG","NU","NF","MP","OM","PK","PW","PS","PA","PG","PY","PE","PH","PN","PL","PR","QA","RE","RO","RW",
 *             "BL","SH","KN","LC","MF","PM","VC","WS","SM","ST","SA","SN","RS","SC","SL","SG","SX","SK","SI","SB","SO",
 *             "ZA","GS","LK","SD","SR","SJ","SZ","SY","TW","TJ","TZ","TH","TL","TG","TK","TO","TT","TN","TR","TM","TC",
 *             "TV","UG","UA","AE","UM","UY","UZ","VU","VE","VN","VG","VI","WF","EH","YE","ZM","ZW"},
 *            description = "Country code ISO 3166-1 alpha-2"
 *        ),
 *      @SWG\Property(
 *            property = "city",
 *            type = "string",
 *            description = "City",
 *            minLength = 2,
 *            maxLength = 64
 *        ),
 *     @SWG\Property(
 *            property = "state",
 *            type = "string",
 *            description = "State / Province",
 *            minLength = 2,
 *            maxLength = 64
 *        ),
 *     @SWG\Property(
 *            property = "zip",
 *            type = "string",
 *            description = "ZIP / Postal Code",
 *            minLength = 1,
 *            maxLength = 16
 *        ),
 *     @SWG\Property(
 *            property = "type",
 *            type = "string",
 *            enum = {"residential","business"},
 *            description = "Type of address"
 *        ),
 * )
 */

/**
 * Class Address
 *
 * @package api\modules\v1\models\forms\shipment
 *
 * @property string $country
 * @property string $city
 * @property string $state
 * @property string $zip
 * @property string $type
 */
class Address extends Model
{

    /**
     * Country code ISO 3166-1 alpha-2
     * @see Country::getList() for list of codes
     *
     * @var string
     */
    public $country;

    /** @var string */
    public $city;

    /**
     * State / Province
     * @var string
     */
    public $state;

    /**
     * Zip / Postal code
     * @var string
     */
    public $zip;

    /**
     * Type of address
     * @see Shipment::getAddressTypes() for list of codes
     *
     * @var string
     */
    public $type;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['country'], 'required', 'message' => '{attribute} is required.'],
            [
                'country',
                'in',
                'range'   => array_keys(Country::getList()),
                'message' => '{attribute} value is incorrect. Valid values are: ' .
                    implode(', ', array_keys(Country::getList())),
            ],
            [['city', 'state'], 'string', 'length' => [2, 64]],
            ['zip', 'string', 'length' => [1, 16]],
            [
                'type',
                'in',
                'range'   => array_keys(Shipment::getAddressTypes()),
                'message' => '{attribute} value is incorrect. Valid values are: ' .
                    implode(', ', array_keys(Shipment::getAddressTypes())),
            ],
        ];
    }
}