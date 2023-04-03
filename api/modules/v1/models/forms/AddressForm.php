<?php

namespace api\modules\v1\models\forms;

use yii\base\Model;
use api\modules\v1\models\core\StateEx;

/**
 * @SWG\Definition(
 *     definition = "AddressForm",
 *     required   = { "name", "address1", "city", "state", "zip" },
 *     @SWG\Property(
 *            property = "name",
 *            type = "string",
 *            description = "Contact name",
 *            minLength = 2,
 *            maxLength = 64
 *        ),
 *     @SWG\Property(
 *            property = "company",
 *            type = "string",
 *            description = "Company name",
 *            minLength = 2,
 *            maxLength = 64
 *        ),
 *     @SWG\Property(
 *            property = "email",
 *            type = "string",
 *            description = "Email",
 *        ),
 *     @SWG\Property(
 *            property = "address1",
 *            type = "string",
 *            description = "Address line 1",
 *            minLength = 2,
 *            maxLength = 64
 *        ),
 *     @SWG\Property(
 *            property = "address2",
 *            type = "string",
 *            description = "Address line 2",
 *            minLength = 1,
 *            maxLength = 64
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
 *            description = "State can be passed as an abbreviation or string. This prevents you from using the enumerations on stateId.
 * Either `stateId` or `state` field required. It is not necessary to have both.",
 *            minLength = 2
 *     ),
 *     @SWG\Property(
 *            property = "stateId",
 *            type = "integer",
 *              enum = {1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,
 *                        21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,
 *                        37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53},
 *            description = "State ID
1 - Alabama
2 - Alaska
3 - Arizona
4 - Arkansas
5 - California
6 - Colorado
7 - Connecticut
8 - Delaware
9 - District of Columbia
10 - Florida
11 - Georgia
12 - Hawaii
13 - Idaho
14 - Illinois
15 - Indiana
16 - Iowa
17 - Kansas
18 - Kentucky
19 - Louisiana
20 - Maine
21 - Maryland
22 - Massachusetts
23 - Michigan
24 - Minnesota
25 - Mississippi
26 - Missouri
27 - Montana
28 - Nebraska
29 - Nevada
30 - New Hampshire
31 - New Jersey
32 - New Mexico
33 - New York
34 - North Carolina
35 - North Dakota
36 - Ohio
37 - Oklahoma
38 - Oregon
39 - Pennsylvania
40 - Puerto Rico
41 - Rhode Island
42 - South Carolina
43 - South Dakota
44 - Tennessee
45 - Texas
46 - Utah
47 - Vermont
48 - Virginia
49 - Washington
50 - West Virginia
51 - Wisconsin
52 - Wyoming
53 - Armed Forces Americas",
 *        ),
 *     @SWG\Property(
 *            property = "zip",
 *            type = "string",
 *            description = "ZIP / Postal Code",
 *            minLength = 1,
 *            maxLength = 16
 *        ),
 *     @SWG\Property(
 *            property = "phone",
 *            type = "string",
 *            description = "Phone number",
 *            minLength = 2,
 *            maxLength = 32
 *        ),
 *     @SWG\Property(
 *            property = "notes",
 *            type = "string",
 *            description = "Notes",
 *            minLength = 2,
 *            maxLength = 600
 *        ),
 * )
 */

/**
 * Class AddressForm
 *
 * @package api\modules\v1\models\forms
 */
class AddressForm extends Model
{
    final const SCENARIO_DEFAULT = 'default'; // ship to address
    final const SCENARIO_FROM = 'from';  // ship from address

    /** @var string */
    public $name;
    /** @var string */
    public $company;
    /** @var string */
    public $email;
    /** @var string */
    public $address1;
    /** @var string */
    public $address2;
    /** @var string */
    public $city;
    /** @var int */
    public $stateId;
    /** @var string */
    public $state;
    /** @var string */
    public $zip;
    /** @var string */
    public $phone;
    /** @var string */
    public $notes;
    /** @var string */
    public $country;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'address1', 'city', 'zip', 'phone'], 'required', 'message' => '{attribute} is required.', 'on' => self::SCENARIO_DEFAULT],
            [['name'], 'required', 'on' => self::SCENARIO_FROM],
            [['name', 'company', 'address1', 'city'], 'string', 'length' => [2, 64]],
            ['email', 'email'],
            ['address2', 'string', 'length' => [1, 64]],
            ['zip', 'string', 'length' => [1, 16]],
            ['phone', 'string', 'length' => [2, 32]],
            ['notes', 'string', 'length' => [2, 600]],
            [['country'],'string', 'max' => 2],
            ['stateId', 'integer'],
            [
                'stateId',
                'in',
                'range' => StateEx::getIdsAsArray(),
                'message' => '{attribute} value is incorrect. Valid values are: ' .
                    implode(', ', StateEx::getIdsAsArray()),
            ],
            [['state'], 'safe'],
        ];
    }
}