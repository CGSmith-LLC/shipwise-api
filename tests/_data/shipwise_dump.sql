# Base dump created from the db at 2023-03-23
# Migrations are applied to this state to create the actual dump for the test environment.

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE `addresses` (
  `id` int NOT NULL,
  `name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `address1` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `address2` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `city` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `state_id` int NOT NULL DEFAULT '0',
  `zip` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `phone` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `notes` varchar(600) DEFAULT NULL,
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `email` varchar(255) DEFAULT NULL,
  `company` varchar(64) DEFAULT NULL,
  `country` varchar(2) NOT NULL DEFAULT 'US'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `alias_children` (
  `id` int NOT NULL,
  `alias_id` int NOT NULL,
  `sku` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `alias_parent` (
  `id` int NOT NULL,
  `customer_id` int NOT NULL,
  `sku` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `api_consumer` (
  `id` int NOT NULL,
  `auth_key` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'API consumer key. Used for authentication',
  `last_activity` datetime DEFAULT NULL,
  `customer_id` int DEFAULT NULL,
  `status` smallint NOT NULL DEFAULT '1' COMMENT 'API consumer status. 1:active, 0:inactive',
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `superuser` smallint NOT NULL DEFAULT '0' COMMENT 'API superuser status. 1:active, 0:inactive',
  `label` varchar(128) DEFAULT 'Shipwise',
  `encrypted_secret` mediumtext,
  `facility_id` smallint NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='List of API consumers';

CREATE TABLE `auth_assignment` (
  `item_name` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci NOT NULL,
  `user_id` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci NOT NULL,
  `created_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

CREATE TABLE `auth_item` (
  `name` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci NOT NULL,
  `type` smallint NOT NULL,
  `description` text CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci,
  `rule_name` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci DEFAULT NULL,
  `data` blob,
  `created_at` int DEFAULT NULL,
  `updated_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

CREATE TABLE `auth_item_child` (
  `parent` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci NOT NULL,
  `child` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

CREATE TABLE `auth_rule` (
  `name` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci NOT NULL,
  `data` blob,
  `created_at` int DEFAULT NULL,
  `updated_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

CREATE TABLE `batch` (
  `id` int NOT NULL,
  `name` varchar(128) NOT NULL,
  `customer_id` int NOT NULL,
  `created_date` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `batch_item` (
  `id` int NOT NULL,
  `batch_id` int NOT NULL,
  `order_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `behavior` (
  `id` int NOT NULL,
  `customer_id` int NOT NULL,
  `integration_id` int NOT NULL,
  `name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` int NOT NULL,
  `order` int NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  `behavior` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `behavior_meta` (
  `id` int NOT NULL,
  `customer_id` int DEFAULT NULL,
  `key` varchar(128) CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci NOT NULL COMMENT 'Behavior key defining a variable',
  `value` varchar(128) CHARACTER SET utf8mb3 COLLATE utf8_unicode_ci NOT NULL COMMENT 'Behavior value assigning the variable',
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci COMMENT='List of behavior meta data';

CREATE TABLE `bulk_action` (
  `id` int NOT NULL,
  `code` varchar(60) NOT NULL COMMENT 'Code of the bulk action',
  `name` varchar(120) NOT NULL COMMENT 'Name of the bulk action',
  `status` tinyint(1) DEFAULT '0' COMMENT 'Current status. 0:processing, 1:completed, 2:error',
  `print_mode` tinyint DEFAULT NULL COMMENT 'Printing mode. 1: qz plugin, 2: pdf file',
  `created_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Created timestamp',
  `created_by` int DEFAULT NULL COMMENT 'ID of the user who created/triggered bulk action'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Bulk actions';

CREATE TABLE `bulk_item` (
  `id` int NOT NULL,
  `bulk_action_id` int NOT NULL COMMENT 'Ref to Bulk Action',
  `order_id` int DEFAULT NULL COMMENT 'Ref to Order',
  `job` varchar(255) DEFAULT NULL COMMENT 'Job name',
  `queue_id` varchar(60) DEFAULT NULL COMMENT 'Queue message ID if any',
  `base64_filedata` longtext COMMENT 'File encoded in base64',
  `base64_filetype` varchar(6) DEFAULT NULL COMMENT 'Type of encoded file: PDF, PNG.',
  `errors` longtext COMMENT 'Processing error messages encoded in JSON',
  `status` tinyint(1) DEFAULT '0' COMMENT 'Current status. 0:queued, 1:done, 2:error'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Bulk action items (orders)';

CREATE TABLE `carrier` (
  `id` int NOT NULL,
  `name` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `carrier` (`id`, `name`) VALUES
(1, 'FedEx'),
(2, 'UPS'),
(3, 'USPS'),
(4, 'DHL'),
(5, 'Amazon UPS'),
(6, 'Amazon FedEx'),
(7, 'UDS'),
(9, 'SpeeDee'),
(10, 'OnTrac'),
(11, 'Amazon USPS');

CREATE TABLE `coldco_holiday` (
  `id` int NOT NULL,
  `holiday` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `coldco_zip_lookup` (
  `zip` varchar(9) NOT NULL,
  `ground_transit_time` int NOT NULL,
  `service` int NOT NULL,
  `saturday_delivery` int NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `country` (
  `id` int NOT NULL,
  `name` varchar(64) NOT NULL,
  `abbreviation` varchar(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `country` (`id`, `name`, `abbreviation`) VALUES
(1, 'United States', 'US'),
(2, 'Canada', 'CA'),
(3, 'Australia', 'AU'),
(4, 'France', 'FR'),
(5, 'Germany', 'DE'),
(6, 'Iceland', 'IS'),
(7, 'Ireland', 'IE'),
(8, 'Italy', 'IT'),
(9, 'Spain', 'ES'),
(10, 'Sweden', 'SE'),
(11, 'Austria', 'AT'),
(12, 'Belgium', 'BE'),
(13, 'Finland', 'FI'),
(14, 'Czech Republic', 'CZ'),
(15, 'Denmark', 'DK'),
(16, 'Norway', 'NO'),
(17, 'United Kingdom', 'GB'),
(18, 'Switzerland', 'CH'),
(19, 'New Zealand', 'NZ'),
(20, 'Russian Federation', 'RU'),
(21, 'Portugal', 'PT'),
(22, 'Netherlands', 'NL'),
(23, 'Isle of Man', 'IM'),
(24, 'Afghanistan', 'AF'),
(25, 'Aland Islands ', 'AX'),
(26, 'Albania', 'AL'),
(27, 'Algeria', 'DZ'),
(28, 'American Samoa', 'AS'),
(29, 'Andorra', 'AD'),
(30, 'Angola', 'AO'),
(31, 'Anguilla', 'AI'),
(32, 'Antarctica', 'AQ'),
(33, 'Antigua and Barbuda', 'AG'),
(34, 'Argentina', 'AR'),
(35, 'Armenia', 'AM'),
(36, 'Aruba', 'AW'),
(37, 'Azerbaijan', 'AZ'),
(38, 'Bahamas', 'BS'),
(39, 'Bahrain', 'BH'),
(40, 'Bangladesh', 'BD'),
(41, 'Barbados', 'BB'),
(42, 'Belarus', 'BY'),
(43, 'Belize', 'BZ'),
(44, 'Benin', 'BJ'),
(45, 'Bermuda', 'BM'),
(46, 'Bhutan', 'BT'),
(47, 'Bolivia, Plurinational State of', 'BO'),
(48, 'Bonaire, Sint Eustatius and Saba', 'BQ'),
(49, 'Bosnia and Herzegovina', 'BA'),
(50, 'Botswana', 'BW'),
(51, 'Bouvet Island', 'BV'),
(52, 'Brazil', 'BR'),
(53, 'British Indian Ocean Territory', 'IO'),
(54, 'Brunei Darussalam', 'BN'),
(55, 'Bulgaria', 'BG'),
(56, 'Burkina Faso', 'BF'),
(57, 'Burundi', 'BI'),
(58, 'Cambodia', 'KH'),
(59, 'Cameroon', 'CM'),
(60, 'Cape Verde', 'CV'),
(61, 'Cayman Islands', 'KY'),
(62, 'Central African Republic', 'CF'),
(63, 'Chad', 'TD'),
(64, 'Chile', 'CL'),
(65, 'China', 'CN'),
(66, 'Christmas Island', 'CX'),
(67, 'Cocos (Keeling) Islands', 'CC'),
(68, 'Colombia', 'CO'),
(69, 'Comoros', 'KM'),
(70, 'Congo', 'CG'),
(71, 'Congo, the Democratic Republic of the', 'CD'),
(72, 'Cook Islands', 'CK'),
(73, 'Costa Rica', 'CR'),
(74, 'Cote d\'Ivoire', 'CI'),
(75, 'Croatia', 'HR'),
(76, 'Cuba', 'CU'),
(77, 'Curaçao', 'CW'),
(78, 'Cyprus', 'CY'),
(79, 'Djibouti', 'DJ'),
(80, 'Dominica', 'DM'),
(81, 'Dominican Republic', 'DO'),
(82, 'Ecuador', 'EC'),
(83, 'Egypt', 'EG'),
(84, 'El Salvador', 'SV'),
(85, 'Equatorial Guinea', 'GQ'),
(86, 'Eritrea', 'ER'),
(87, 'Estonia', 'EE'),
(88, 'Ethiopia', 'ET'),
(89, 'Falkland Islands (Malvinas)', 'FK'),
(90, 'Faroe Islands', 'FO'),
(91, 'Fiji', 'FJ'),
(92, 'French Guiana', 'GF'),
(93, 'French Polynesia', 'PF'),
(94, 'French Southern Territories', 'TF'),
(95, 'Gabon', 'GA'),
(96, 'Gambia', 'GM'),
(97, 'Georgia', 'GE'),
(98, 'Ghana', 'GH'),
(99, 'Gibraltar', 'GI'),
(100, 'Greece', 'GR'),
(101, 'Greenland', 'GL'),
(102, 'Grenada', 'GD'),
(103, 'Guadeloupe', 'GP'),
(104, 'Guam', 'GU'),
(105, 'Guatemala', 'GT'),
(106, 'Guernsey', 'GG'),
(107, 'Guinea', 'GN'),
(108, 'Guinea-Bissau', 'GW'),
(109, 'Guyana', 'GY'),
(110, 'Haiti', 'HT'),
(111, 'Heard Island and McDonald Islands', 'HM'),
(112, 'Holy See (Vatican City State)', 'VA'),
(113, 'Honduras', 'HN'),
(114, 'Hong Kong', 'HK'),
(115, 'Hungary', 'HU'),
(116, 'India', 'IN'),
(117, 'Indonesia', 'ID'),
(118, 'Iran, Islamic Republic of', 'IR'),
(119, 'Iraq', 'IQ'),
(120, 'Israel', 'IL'),
(121, 'Jamaica', 'JM'),
(122, 'Japan', 'JP'),
(123, 'Jersey', 'JE'),
(124, 'Jordan', 'JO'),
(125, 'Kazakhstan', 'KZ'),
(126, 'Kenya', 'KE'),
(127, 'Kiribati', 'KI'),
(128, 'Korea, Democratic People\'s Republic of', 'KP'),
(129, 'Korea, Republic of', 'KR'),
(130, 'Kuwait', 'KW'),
(131, 'Kyrgyzstan', 'KG'),
(132, 'Lao People\'s Democratic Republic', 'LA'),
(133, 'Latvia', 'LV'),
(134, 'Lebanon', 'LB'),
(135, 'Lesotho', 'LS'),
(136, 'Liberia', 'LR'),
(137, 'Libyan Arab Jamahiriya', 'LY'),
(138, 'Liechtenstein', 'LI'),
(139, 'Lithuania', 'LT'),
(140, 'Luxembourg', 'LU'),
(141, 'Macao', 'MO'),
(142, 'Macedonia', 'MK'),
(143, 'Madagascar', 'MG'),
(144, 'Malawi', 'MW'),
(145, 'Malaysia', 'MY'),
(146, 'Maldives', 'MV'),
(147, 'Mali', 'ML'),
(148, 'Malta', 'MT'),
(149, 'Marshall Islands', 'MH'),
(150, 'Martinique', 'MQ'),
(151, 'Mauritania', 'MR'),
(152, 'Mauritius', 'MU'),
(153, 'Mayotte', 'YT'),
(154, 'Mexico', 'MX'),
(155, 'Micronesia, Federated States of', 'FM'),
(156, 'Moldova, Republic of', 'MD'),
(157, 'Monaco', 'MC'),
(158, 'Mongolia', 'MN'),
(159, 'Montenegro', 'ME'),
(160, 'Montserrat', 'MS'),
(161, 'Morocco', 'MA'),
(162, 'Mozambique', 'MZ'),
(163, 'Myanmar', 'MM'),
(164, 'Namibia', 'NA'),
(165, 'Nauru', 'NR'),
(166, 'Nepal', 'NP'),
(167, 'New Caledonia', 'NC'),
(168, 'Nicaragua', 'NI'),
(169, 'Niger', 'NE'),
(170, 'Nigeria', 'NG'),
(171, 'Niue', 'NU'),
(172, 'Norfolk Island', 'NF'),
(173, 'Northern Mariana Islands', 'MP'),
(174, 'Oman', 'OM'),
(175, 'Pakistan', 'PK'),
(176, 'Palau', 'PW'),
(177, 'Palestinian Territory, Occupied', 'PS'),
(178, 'Panama', 'PA'),
(179, 'Papua New Guinea', 'PG'),
(180, 'Paraguay', 'PY'),
(181, 'Peru', 'PE'),
(182, 'Philippines', 'PH'),
(183, 'Pitcairn', 'PN'),
(184, 'Poland', 'PL'),
(185, 'Puerto Rico', 'PR'),
(186, 'Qatar', 'QA'),
(187, 'Reunion', 'RE'),
(188, 'Romania', 'RO'),
(189, 'Rwanda', 'RW'),
(190, 'Saint Barthélemy', 'BL'),
(191, 'Saint Helena', 'SH'),
(192, 'Saint Kitts and Nevis', 'KN'),
(193, 'Saint Lucia', 'LC'),
(194, 'Saint Martin (French part)', 'MF'),
(195, 'Saint Pierre and Miquelon', 'PM'),
(196, 'Saint Vincent and the Grenadines', 'VC'),
(197, 'Samoa', 'WS'),
(198, 'San Marino', 'SM'),
(199, 'Sao Tome and Principe', 'ST'),
(200, 'Saudi Arabia', 'SA'),
(201, 'Senegal', 'SN'),
(202, 'Serbia', 'RS'),
(203, 'Seychelles', 'SC'),
(204, 'Sierra Leone', 'SL'),
(205, 'Singapore', 'SG'),
(206, 'Sint Maarten (Dutch part)', 'SX'),
(207, 'Slovakia', 'SK'),
(208, 'Slovenia', 'SI'),
(209, 'Solomon Islands', 'SB'),
(210, 'Somalia', 'SO'),
(211, 'South Africa', 'ZA'),
(212, 'South Georgia and the South Sandwich Islands', 'GS'),
(213, 'Sri Lanka', 'LK'),
(214, 'Sudan', 'SD'),
(215, 'Suriname', 'SR'),
(216, 'Svalbard and Jan Mayen', 'SJ'),
(217, 'Swaziland', 'SZ'),
(218, 'Syrian Arab Republic', 'SY'),
(219, 'Taiwan, Province of China', 'TW'),
(220, 'Tajikistan', 'TJ'),
(221, 'Tanzania, United Republic of', 'TZ'),
(222, 'Thailand', 'TH'),
(223, 'Timor-Leste', 'TL'),
(224, 'Togo', 'TG'),
(225, 'Tokelau', 'TK'),
(226, 'Tonga', 'TO'),
(227, 'Trinidad and Tobago', 'TT'),
(228, 'Tunisia', 'TN'),
(229, 'Turkey', 'TR'),
(230, 'Turkmenistan', 'TM'),
(231, 'Turks and Caicos Islands', 'TC'),
(232, 'Tuvalu', 'TV'),
(233, 'Uganda', 'UG'),
(234, 'Ukraine', 'UA'),
(235, 'United Arab Emirates', 'AE'),
(236, 'United States Minor Outlying Islands', 'UM'),
(237, 'Uruguay', 'UY'),
(238, 'Uzbekistan', 'UZ'),
(239, 'Vanuatu', 'VU'),
(240, 'Venezuela, Bolivarian Republic of', 'VE'),
(241, 'Viet Nam', 'VN'),
(242, 'Virgin Islands, British', 'VG'),
(243, 'Virgin Islands, U.S.', 'VI'),
(244, 'Wallis and Futuna', 'WF'),
(245, 'Western Sahara', 'EH'),
(246, 'Yemen', 'YE'),
(247, 'Zambia', 'ZM'),
(248, 'Zimbabwe', 'ZW'),
(249, 'South Korea', 'KR');

CREATE TABLE `customers` (
  `id` int NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  `address1` varchar(64) DEFAULT NULL,
  `address2` varchar(64) DEFAULT NULL COMMENT 'Address line 2',
  `city` varchar(64) DEFAULT NULL,
  `state_id` int DEFAULT NULL,
  `zip` varchar(16) DEFAULT NULL,
  `phone` varchar(32) DEFAULT NULL COMMENT 'Phone number',
  `email` varchar(255) DEFAULT NULL COMMENT 'Email address',
  `logo` varchar(256) DEFAULT NULL,
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `stripe_customer_id` varchar(128) DEFAULT NULL,
  `direct` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `customers_meta` (
  `id` int NOT NULL,
  `customer_id` int DEFAULT NULL,
  `key` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Customer key defining a variable',
  `value` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Customer value assigning the variable',
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='List of customer meta data';

CREATE TABLE `fulfillment` (
  `id` int NOT NULL,
  `name` varchar(64) NOT NULL,
  `wms` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `fulfillment_meta` (
  `id` int NOT NULL,
  `fulfillment_id` int NOT NULL,
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` varchar(2047) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `fulfillment_service_mapping` (
  `id` int NOT NULL,
  `service_id` int NOT NULL,
  `fulfillment_id` int NOT NULL,
  `fulfillment_service_identifier` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `integration` (
  `id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` int NOT NULL,
  `ecommerce` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fulfillment` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` int NOT NULL DEFAULT '0',
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status_message` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_success_run` datetime DEFAULT NULL,
  `webhooks_enabled` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `integration_hookdeck` (
  `id` int NOT NULL,
  `integration_id` int NOT NULL,
  `source_name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `destination_name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `destination_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `destination_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `integration_meta` (
  `id` int NOT NULL,
  `integration_id` int NOT NULL,
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `integration_webhook` (
  `id` int NOT NULL,
  `integration_id` int NOT NULL,
  `integration_hookdeck_id` int NOT NULL,
  `source_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `topic` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `inventory` (
  `id` int NOT NULL,
  `customer_id` int NOT NULL,
  `name` varchar(64) DEFAULT NULL,
  `sku` varchar(64) NOT NULL,
  `available_quantity` decimal(8,2) NOT NULL DEFAULT '0.00',
  `location` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `invoice` (
  `id` int NOT NULL,
  `customer_id` int NOT NULL COMMENT 'Reference to customer',
  `subscription_id` int NOT NULL COMMENT 'Reference to Subscription ID',
  `customer_name` varchar(64) NOT NULL COMMENT 'Customer Name',
  `amount` int NOT NULL COMMENT 'Total in Cents',
  `balance` int NOT NULL COMMENT 'Balance Due in Cents',
  `due_date` date NOT NULL COMMENT 'Due Date',
  `stripe_charge_id` char(128) DEFAULT NULL COMMENT 'stripe charge id',
  `status` int NOT NULL COMMENT 'Status of transaction'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `invoice_items` (
  `id` int NOT NULL,
  `invoice_id` int NOT NULL COMMENT 'Reference to invoice table',
  `name` varchar(128) NOT NULL,
  `amount` int NOT NULL COMMENT 'cents'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `items` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `quantity` int NOT NULL,
  `sku` varchar(64) NOT NULL,
  `name` varchar(128) DEFAULT NULL,
  `uuid` varchar(64) DEFAULT NULL,
  `alias_quantity` int DEFAULT NULL,
  `alias_sku` varchar(64) DEFAULT NULL,
  `notes` varchar(512) DEFAULT NULL,
  `type` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `migration` (`version`, `apply_time`) VALUES
('Da\\User\\Migration\\m000000_000001_create_user_table', 1660849907),
('Da\\User\\Migration\\m000000_000002_create_profile_table', 1660849907),
('Da\\User\\Migration\\m000000_000003_create_social_account_table', 1660849907),
('Da\\User\\Migration\\m000000_000004_create_token_table', 1660849907),
('Da\\User\\Migration\\m000000_000005_add_last_login_at', 1660849907),
('Da\\User\\Migration\\m000000_000006_add_two_factor_fields', 1660849915),
('Da\\User\\Migration\\m000000_000007_enable_password_expiration', 1660849915),
('Da\\User\\Migration\\m000000_000008_add_last_login_ip', 1660849915),
('Da\\User\\Migration\\m000000_000009_add_gdpr_consent_fields', 1660849915),
('m000000_000000_base', 1540735037),
('m130524_201442_init', 1540735042),
('m140209_132017_init', 1572007459),
('m140403_174025_create_account_table', 1572007459),
('m140504_113157_update_tables', 1572007460),
('m140504_130429_create_token_table', 1572007460),
('m140506_102106_rbac_init', 1673059309),
('m140830_171933_fix_ip_field', 1572007460),
('m140830_172703_change_account_table_name', 1572007460),
('m141222_110026_update_ip_field', 1572007460),
('m141222_135246_alter_username_length', 1572007460),
('m150614_103145_update_social_account_table', 1572007460),
('m150623_212711_fix_username_notnull', 1572007460),
('m151218_234654_add_timezone_to_profile', 1572007460),
('m160929_103127_add_last_login_at_to_user_table', 1572007460),
('m170907_052038_rbac_add_index_on_auth_assignment_user_id', 1673059309),
('m180523_151638_rbac_updates_indexes_without_prefix', 1673059309),
('m180715_010226_alter_table_api_consumer', 1540735042),
('m180716_005731_alter_table_customers_add_created_date', 1540735042),
('m180906_190733_order_uuid_addition', 1540735044),
('m181002_122533_modify_sku_name_size', 1540735044),
('m181027_174724_add_requested_ship_date', 1540735045),
('m181027_182316_add_superuser', 1540735045),
('m181031_025039_items_length_issue', 1540954394),
('m181031_030057_item_issue_validate', 1540954944),
('m181211_141338_ship_to_email_addition', 1549251232),
('m190204_025127_carrier_service_addition', 1549251237),
('m190423_163854_origin_on_orders', 1556038299),
('m191014_004543_create_table_user_customer', 1572007404),
('m191023_014053_customer_meta_data', 1579545326),
('m191217_231513_alter_service_table', 1579545326),
('m200215_012018_create_queue_table', 1586400589),
('m200218_091918_create_bulk_action_tables', 1586400589),
('m200220_201116_alter_customer_table', 1586400589),
('m200220_205707_alter_bulk_item_table', 1586400589),
('m200227_153230_create_items_shipped_table', 1582839610),
('m200227_154042_create_packages_shipped_table', 1582839612),
('m200301_002348_alter_customers_table', 1586400589),
('m200301_053834_alter_bulk_item_table', 1586400589),
('m200303_000401_update_items_table', 1582839613),
('m200303_211741_add_ponumber_column_to_orders_table', 1583338392),
('m200305_172737_alter_table_bulk_item', 1586400589),
('m200313_011047_drop_columns_packaage_items_table', 1584074759),
('m200313_030704_add_package_items_lot_numbers', 1584074759),
('m200316_021810_alter_packages_table_created_date_field', 1584074759),
('m200407_214047_alter_bulk_action_table', 1586400589),
('m200409_110543_rbac_update_mssql_trigger', 1673059309),
('m200504_210813_create_paymentmethod_table', 1593633054),
('m200504_210905_create_subscription_table', 1593633202),
('m200504_210925_create_invoice_table', 1593633288),
('m200504_211010_create_one_time_charge_table', 1593633328),
('m200504_211222_create_subscription_items_table', 1593633356),
('m200504_211239_create_invoice_items_table', 1593633373),
('m200507_030216_indexes_for_tables', 1588821508),
('m200507_153434_add_index_created_date_to_orders_table', 1588865905),
('m200511_194031_add_stripe_customer_id_column_to_customers_table', 1593633379),
('m200518_184725_create_payment_intent_table', 1593633393),
('m200522_193803_add_expression_to_payment_intent_table_for_created_date', 1593633400),
('m200604_183144_card_meta_data', 1593633400),
('m200608_074300_add_company_name_to_orders_table', 1591626118),
('m200608_171009_add_direct_to_customers_table', 1593633400),
('m200626_123538_customer_feilds_nullable', 1593633401),
('m200626_134938_create_stripe_id_for_existing_users', 1593633407),
('m200626_201658_customer_id_on_user_table', 1593633496),
('m200706_083000_create_inventory_table', 1594089598),
('m200720_073100_add_country_to_address_table', 1595438161),
('m200720_094730_make_state_id_defualt_0', 1595438161),
('m200722_082500_add_country_to_states_table', 1595438161),
('m200722_084130_adding_provinces_to_States_table', 1595438161),
('m200722_115830_eu_provinces_to_states_table', 1595863363),
('m200722_181343_create_country_table', 1595863363),
('m200722_183521_add_countries_to_country_table', 1595863363),
('m200727_002352_alter_order_table', 1595864142),
('m200727_092000_add_index_to_orders_table', 1596819909),
('m200803_125456_sku_table', 1598322325),
('m200803_133127_add_customer_id_to_sku_table', 1598322325),
('m200806_161158_create_batch_table', 1596819909),
('m200806_213621_create_table_batch_item', 1596819909),
('m200812_014115_add_from_address_to_orders', 1597249517),
('m200812_140710_shopify_webhook_table', 1598322325),
('m200812_204202_create_shopify_app_table', 1598322325),
('m200813_013210_add_charge_asap_to_one_time', 1597288535),
('m200819_192106_add_index_to_package_items_table', 1597885875),
('m200828_140724_alter_notes_on_order', 1598625699),
('m200831_084700_alter_notes_on_address_table', 1598883105),
('m200904_165806_add_item_alias_sku', 1599833043),
('m200918_133216_substitute_items', 1601478693),
('m200928_192142_location_for_inventory', 1601401441),
('m201001_235644_order_notes_1000', 1601920388),
('m201007_214500_order_notes_6000', 1602125479),
('m201220_195434_add_uuid_on_package_items', 1608495988),
('m210331_175922_alter_items_table', 1617216196),
('m210421_004630_utf8mb4', 1619454291),
('m210614_131847_alter_api_consumer_table_add_label', 1623991272),
('m210617_154845_increase_encrypted_size_on_api_table', 1623991273),
('m210706_194745_order_date_index', 1629143815),
('m210712_132224_modify_item_column_length', 1626111140),
('m210720_122600_create_integration_table', 1641181521),
('m210720_193447_create_integration_meta', 1641181521),
('m210723_140057_alter_sku_table_add_excluded', 1641181521),
('m210726_200931_add_fulfillment_coulum_to_integration_table', 1641181521),
('m210727_140813_create_fulfillment_meta_table', 1641181705),
('m210817_140856_expand_fulfillment_meta_value_length', 1641181705),
('m210918_202649_add_status_to_integration', 1641181705),
('m211029_124728_orders_fulltext_search', 1635862655),
('m211031_190735_integration_status_message', 1641181705),
('m211031_194324_add_success_date_to_integration', 1641181705),
('m211102_083339_fulltext_search_syntax_update', 1635862655),
('m211115_005035_create_behavior_table', 1641181705),
('m211123_205502_create_integration_webhook', 1641181705),
('m211123_205636_create_integration_hookdeck', 1641181705),
('m211124_010513_add_webhook_option', 1641181705),
('m220629_001442_transit_fields_on_order', 1656536952),
('m220909_214520_add_clone_order_preference_profile', 1662765779),
('m220909_223858_create_scheduled_orders_table', 1662765779),
('m220921_232826_behavior_meta', 1663809070),
('m220921_233512_add_behavior_column_to_behavior_table', 1663809070),
('m220922_200737_add_arrive_by_date', 1663880561),
('m221004_012013_sku_alter_column', 1664847781),
('m221010_192424_alias_table', 1665430525),
('m221010_192425_alias_child_table', 1665430525),
('m221208_225034_create_webhook_table', 1670539937),
('m221208_225254_create_webhook_log_table', 1670539938),
('m221208_230340_create_webhook_trigger_table', 1670541160),
('m221230_194520_create_warehouse_table', 1672435045),
('m230109_011242_add_type_to_user_table', 1673226957),
('zhuravljov\\yii\\queue\\monitor\\migrations\\M180807000000Schema', 1641181521),
('zhuravljov\\yii\\queue\\monitor\\migrations\\M190420000000ExecResult', 1641181521);

CREATE TABLE `one_time_charge` (
  `id` int NOT NULL,
  `customer_id` int NOT NULL COMMENT 'Reference to customer',
  `name` varchar(128) NOT NULL,
  `amount` int NOT NULL COMMENT 'In cents',
  `added_to_invoice` tinyint(1) NOT NULL DEFAULT '0',
  `charge_asap` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `customer_id` int NOT NULL,
  `order_reference` varchar(45) DEFAULT NULL,
  `customer_reference` varchar(64) NOT NULL,
  `status_id` int DEFAULT '0',
  `tracking` varchar(45) DEFAULT NULL,
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `address_id` int NOT NULL,
  `notes` varchar(6000) DEFAULT NULL,
  `uuid` varchar(64) DEFAULT NULL,
  `requested_ship_date` datetime DEFAULT NULL,
  `carrier_id` smallint DEFAULT NULL,
  `service_id` smallint DEFAULT NULL,
  `origin` varchar(64) DEFAULT NULL,
  `po_number` varchar(64) DEFAULT NULL,
  `label_data` longtext,
  `label_type` varchar(6) DEFAULT NULL,
  `ship_from_name` varchar(64) DEFAULT NULL,
  `ship_from_address1` varchar(64) DEFAULT NULL,
  `ship_from_address2` varchar(64) DEFAULT NULL,
  `ship_from_city` varchar(64) DEFAULT NULL,
  `ship_from_state_id` int DEFAULT NULL,
  `ship_from_zip` varchar(64) DEFAULT NULL,
  `ship_from_country_code` varchar(3) DEFAULT NULL,
  `ship_from_phone` varchar(64) DEFAULT NULL,
  `ship_from_email` varchar(64) DEFAULT NULL,
  `transit` int DEFAULT NULL,
  `packagingNotes` varchar(64) DEFAULT NULL,
  `must_arrive_by_date` datetime DEFAULT NULL,
  `warehouse_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `order_history` (
  `id` int NOT NULL,
  `status_id` int NOT NULL,
  `order_id` int NOT NULL,
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `comment` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `packages` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `tracking` varchar(128) DEFAULT NULL,
  `length` varchar(16) DEFAULT NULL,
  `width` varchar(16) DEFAULT NULL,
  `height` varchar(16) DEFAULT NULL,
  `weight` varchar(16) DEFAULT NULL,
  `created_date` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `package_items` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `package_id` int NOT NULL,
  `quantity` int NOT NULL,
  `sku` varchar(64) NOT NULL,
  `name` varchar(128) DEFAULT NULL,
  `created_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `uuid` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `package_items_lot_info` (
  `id` int NOT NULL,
  `package_items_id` int NOT NULL,
  `quantity` int NOT NULL,
  `lot_number` varchar(128) DEFAULT NULL,
  `serial_number` varchar(128) DEFAULT NULL,
  `created_date` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `paymentmethod` (
  `id` int NOT NULL,
  `customer_id` int DEFAULT NULL COMMENT 'Reference to customer',
  `stripe_payment_method_id` char(128) DEFAULT NULL,
  `default` tinyint(1) NOT NULL COMMENT 'Is this the customer''s default payment method?',
  `brand` varchar(64) DEFAULT NULL,
  `lastfour` varchar(64) DEFAULT NULL,
  `expiration` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `payment_intent` (
  `id` int NOT NULL,
  `customer_id` int NOT NULL COMMENT 'Reference to customer',
  `payment_method_id` int NOT NULL COMMENT 'Reference to payment method table',
  `invoice_id` int NOT NULL,
  `stripe_payment_intent_id` char(128) DEFAULT NULL COMMENT 'stripe payment intent id',
  `amount` int NOT NULL COMMENT 'Total in Cents',
  `status` varchar(64) NOT NULL COMMENT 'Stripe Status of Payment Intent',
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `profile` (
  `user_id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `public_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `gravatar_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `gravatar_id` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `website` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `bio` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `timezone` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `clone_order_preference` int NOT NULL DEFAULT '9'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `queue` (
  `id` int NOT NULL,
  `channel` varchar(255) NOT NULL,
  `job` blob NOT NULL,
  `pushed_at` int NOT NULL,
  `ttr` int NOT NULL,
  `delay` int NOT NULL,
  `priority` int UNSIGNED NOT NULL DEFAULT '1024',
  `reserved_at` int DEFAULT NULL,
  `attempt` int DEFAULT NULL,
  `done_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `queue_exec` (
  `id` bigint NOT NULL,
  `push_id` bigint NOT NULL,
  `worker_id` bigint DEFAULT NULL,
  `attempt` int UNSIGNED NOT NULL,
  `started_at` int UNSIGNED NOT NULL,
  `finished_at` int UNSIGNED DEFAULT NULL,
  `memory_usage` bigint UNSIGNED DEFAULT NULL,
  `error` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `result_data` longblob,
  `retry` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `queue_push` (
  `id` bigint NOT NULL,
  `parent_id` bigint DEFAULT NULL,
  `sender_name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `job_uid` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `job_class` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `job_data` longblob NOT NULL,
  `ttr` int UNSIGNED NOT NULL,
  `delay` int UNSIGNED NOT NULL,
  `trace` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `context` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `pushed_at` int UNSIGNED NOT NULL,
  `stopped_at` int UNSIGNED DEFAULT NULL,
  `first_exec_id` bigint DEFAULT NULL,
  `last_exec_id` bigint DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `queue_worker` (
  `id` bigint NOT NULL,
  `sender_name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `host` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pid` int UNSIGNED DEFAULT NULL,
  `started_at` int UNSIGNED NOT NULL,
  `pinged_at` int UNSIGNED NOT NULL,
  `stopped_at` int UNSIGNED DEFAULT NULL,
  `finished_at` int UNSIGNED DEFAULT NULL,
  `last_exec_id` bigint DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `scheduled_orders` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `status_id` int NOT NULL,
  `customer_id` int NOT NULL,
  `scheduled_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `service` (
  `id` int NOT NULL,
  `name` varchar(64) NOT NULL,
  `carrier_id` int NOT NULL,
  `shipwise_code` varchar(50) NOT NULL COMMENT 'ShipWise service code',
  `carrier_code` varchar(50) NOT NULL COMMENT 'Service code name as used by carrier''s API'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `shopify_app` (
  `id` int NOT NULL,
  `customer_id` int NOT NULL,
  `shop` varchar(128) NOT NULL,
  `scopes` varchar(128) NOT NULL,
  `access_token` varchar(128) DEFAULT NULL,
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `shopify_webhook` (
  `id` int NOT NULL,
  `customer_id` int NOT NULL,
  `shopify_webhook_id` varchar(64) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `sku` (
  `id` int NOT NULL,
  `sku` varchar(64) DEFAULT NULL,
  `name` varchar(64) DEFAULT NULL,
  `customer_id` int NOT NULL DEFAULT '0',
  `substitute_1` varchar(64) DEFAULT NULL,
  `substitute_2` varchar(64) DEFAULT NULL,
  `substitute_3` varchar(64) DEFAULT NULL,
  `excluded` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `social_account` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `provider` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `client_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `data` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `code` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `created_at` int DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `states` (
  `id` int NOT NULL,
  `name` varchar(45) NOT NULL,
  `abbreviation` varchar(12) NOT NULL,
  `country` varchar(2) NOT NULL DEFAULT 'US'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `states` (`id`, `name`, `abbreviation`, `country`) VALUES
(1, 'Alabama', 'AL', 'US'),
(2, 'Alaska', 'AK', 'US'),
(3, 'Arizona', 'AZ', 'US'),
(4, 'Arkansas', 'AR', 'US'),
(5, 'California', 'CA', 'US'),
(6, 'Colorado', 'CO', 'US'),
(7, 'Connecticut', 'CT', 'US'),
(8, 'Delaware', 'DE', 'US'),
(9, 'District of Columbia', 'DC', 'US'),
(10, 'Florida', 'FL', 'US'),
(11, 'Georgia', 'GA', 'US'),
(12, 'Hawaii', 'HI', 'US'),
(13, 'Idaho', 'ID', 'US'),
(14, 'Illinois', 'IL', 'US'),
(15, 'Indiana', 'IN', 'US'),
(16, 'Iowa', 'IA', 'US'),
(17, 'Kansas', 'KS', 'US'),
(18, 'Kentucky', 'KY', 'US'),
(19, 'Louisiana', 'LA', 'US'),
(20, 'Maine', 'ME', 'US'),
(21, 'Maryland', 'MD', 'US'),
(22, 'Massachusetts', 'MA', 'US'),
(23, 'Michigan', 'MI', 'US'),
(24, 'Minnesota', 'MN', 'US'),
(25, 'Mississippi', 'MS', 'US'),
(26, 'Missouri', 'MO', 'US'),
(27, 'Montana', 'MT', 'US'),
(28, 'Nebraska', 'NE', 'US'),
(29, 'Nevada', 'NV', 'US'),
(30, 'New Hampshire', 'NH', 'US'),
(31, 'New Jersey', 'NJ', 'US'),
(32, 'New Mexico', 'NM', 'US'),
(33, 'New York', 'NY', 'US'),
(34, 'North Carolina', 'NC', 'US'),
(35, 'North Dakota', 'ND', 'US'),
(36, 'Ohio', 'OH', 'US'),
(37, 'Oklahoma', 'OK', 'US'),
(38, 'Oregon', 'OR', 'US'),
(39, 'Pennsylvania', 'PA', 'US'),
(40, 'Puerto Rico', 'PR', 'US'),
(41, 'Rhode Island', 'RI', 'US'),
(42, 'South Carolina', 'SC', 'US'),
(43, 'South Dakota', 'SD', 'US'),
(44, 'Tennessee', 'TN', 'US'),
(45, 'Texas', 'TX', 'US'),
(46, 'Utah', 'UT', 'US'),
(47, 'Vermont', 'VT', 'US'),
(48, 'Virginia', 'VA', 'US'),
(49, 'Washington', 'WA', 'US'),
(50, 'West Virginia', 'WV', 'US'),
(51, 'Wisconsin', 'WI', 'US'),
(52, 'Wyoming', 'WY', 'US'),
(53, 'Armed Forces of America', 'AA', 'US'),
(54, 'Newfoundland and Labrador', 'NL', 'CA'),
(55, 'Prince Edward Island', 'PE', 'CA'),
(56, 'British Columbia', 'BC', 'CA'),
(57, 'Alberta', 'AB', 'CA'),
(58, 'Manitoba', 'MB', 'CA'),
(59, 'New Brunswick', 'NB', 'CA'),
(60, 'Northwest Territories', 'NT', 'CA'),
(61, 'Nova Scotia', 'NS', 'CA'),
(62, 'Nunavut', 'NU', 'CA'),
(63, 'Ontario', 'ON', 'CA'),
(64, 'Quebec', 'QC', 'CA'),
(65, 'Saskatchewan', 'SK', 'CA'),
(66, 'Yukon', 'YT', 'CA'),
(67, 'Aguascalientes', 'AG', 'MX'),
(68, 'Baja California', 'BC', 'MX'),
(69, 'Baja California Sur', 'BS', 'MX'),
(70, 'Campeche', 'CM', 'MX'),
(71, 'Chiapas', 'CS', 'MX'),
(72, 'Chihuahua', 'CH', 'MX'),
(73, 'Coahuila', 'CO', 'MX'),
(74, 'Colima', 'CL', 'MX'),
(75, 'Mexico City', 'DF', 'MX'),
(76, 'Durango', 'DG', 'MX'),
(77, 'Guanajuato', 'GT', 'MX'),
(78, 'Guerrero', 'GR', 'MX'),
(79, 'Hidalgo', 'HG', 'MX'),
(80, 'Jalisco', 'JA', 'MX'),
(81, 'Mexico', 'EM', 'MX'),
(82, 'Michoacan', 'MI', 'MX'),
(83, 'Morelos', 'MO', 'MX'),
(84, 'Nayarit', 'NA', 'MX'),
(85, 'Nuevo Leon', 'NL', 'MX'),
(86, 'Oaxaca', 'OA', 'MX'),
(87, 'Puebla', 'PU', 'MX'),
(88, 'Queretaro', 'QT', 'MX'),
(89, 'Quintana Roo', 'QR', 'MX'),
(90, 'San Luis Potosi', 'SL', 'MX'),
(91, 'Sinaloa', 'SI', 'MX'),
(92, 'Sonora', 'SO', 'MX'),
(93, 'Tabasco', 'TB', 'MX'),
(94, 'Tamaulipas', 'TM', 'MX'),
(95, 'Tlaxcala', 'TL', 'MX'),
(96, 'Veracruz', 'VE', 'MX'),
(97, 'Yucatan', 'YU', 'MX'),
(98, 'Zacatecas', 'ZA', 'MX'),
(99, 'Nord-Est', 'Nord-Est', 'RO'),
(100, 'Sud-Est', 'Sud-Est', 'RO'),
(101, 'Sud-Muntenia', 'Sud-Muntenia', 'RO'),
(102, 'Sud-Vest Olentia', 'Sud-Vest Ole', 'RO'),
(103, 'Vest', 'Vest', 'RO'),
(104, 'Nord-Vest', 'Nord-Vest', 'RO'),
(105, 'Centru', 'Centru', 'RO'),
(106, 'Bucuresti-Illfov', 'Bucuresti-Il', 'RO'),
(107, 'La Coruna', 'C', 'ES'),
(108, 'Alava', 'VI', 'ES'),
(109, 'Albacete', 'AB', 'ES'),
(110, 'Alicante', 'A', 'ES'),
(111, 'Almeria', 'AL', 'ES'),
(112, 'Asturias', '0', 'ES'),
(113, 'Aliva', 'AV', 'ES'),
(114, 'Badajoz', 'BA', 'ES'),
(115, 'Balears', 'PM', 'ES'),
(116, 'Barcelona', 'B', 'ES'),
(117, 'Bizkaia', 'BI', 'ES'),
(118, 'Burgos', 'BU', 'ES'),
(119, 'Caceres', 'CC', 'ES'),
(120, 'Cadiz', 'CA', 'ES'),
(121, 'Cantabria', 'S', 'ES'),
(122, 'Castellon', 'CS', 'ES'),
(123, 'Ciudad Real', 'CR', 'ES'),
(124, 'Cordoba', 'CO', 'ES'),
(125, 'Cuenca', 'CU', 'ES'),
(126, 'Gipuzkoa', 'SS', 'ES'),
(127, 'Girona', 'GI', 'ES'),
(128, 'Granada', 'GR', 'ES'),
(129, 'Guadalajara', 'GU', 'ES'),
(130, 'Huelva', 'H', 'ES'),
(131, 'Huesca', 'HU', 'ES'),
(132, 'Jaen', 'J', 'ES'),
(133, 'La Rioja', 'LO', 'ES'),
(134, 'Las Palmas', 'GC', 'ES'),
(135, 'Leon', 'LE', 'ES'),
(136, 'Lleida', 'L', 'ES'),
(137, 'Lugo', 'LU', 'ES'),
(138, 'Madrid', 'M', 'ES'),
(139, 'Malaga', 'MA', 'ES'),
(140, 'Murica', 'MU', 'ES'),
(141, 'Navarra', 'NA', 'ES'),
(142, 'Ourense', 'OR', 'ES'),
(143, 'Palencia', 'P', 'ES'),
(144, 'Pontevedra', 'PO', 'ES'),
(145, 'Salamanca', 'SA', 'ES'),
(146, 'Santa Cruz de Tenerife', 'TF', 'ES'),
(147, 'Segovia', 'SG', 'ES'),
(148, 'Sevilla', 'SE', 'ES'),
(149, 'Soria', 'SO', 'ES'),
(150, 'Tarragona', 'T', 'ES'),
(151, 'Teruel', 'TE', 'ES'),
(152, 'Toledo', 'TO', 'ES'),
(153, 'Valencia', 'V', 'ES'),
(154, 'Valladolid', 'VA', 'ES'),
(155, 'Zamora', 'ZA', 'ES'),
(156, 'Zaragoza', 'Z', 'ES'),
(157, 'Armed Forces of Europe', 'AE', 'US'),
(158, 'Armed Forces Pacific', 'AP', 'US');

CREATE TABLE `status` (
  `id` int NOT NULL,
  `name` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `status` (`id`, `name`) VALUES
(1, 'Shipped'),
(2, 'Prime'),
(6, 'On Hold'),
(7, 'Cancelled'),
(8, 'Pending Fulfillment'),
(9, 'Open'),
(10, 'WMS Error'),
(11, 'Completed'),
(12, 'Prime No Rate'),
(13, 'Transferred'),
(14, 'Transfer West'),
(15, 'Transfer East'),
(16, 'Awaiting Fulfillment'),
(17, 'Transfer Central'),
(18, 'Imported in WMS');

CREATE TABLE `subscription` (
  `id` int NOT NULL,
  `customer_id` int NOT NULL COMMENT 'Reference to customer',
  `next_invoice` date NOT NULL COMMENT 'The Next Date to generate an invoice',
  `months_to_recur` int NOT NULL COMMENT 'How many months will be used to calculate the next invoice'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `subscription_items` (
  `id` int NOT NULL,
  `subscription_id` int NOT NULL COMMENT 'Reference to subscriptions',
  `name` varchar(128) NOT NULL,
  `amount` int NOT NULL COMMENT 'amount in cents'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `token` (
  `user_id` int NOT NULL,
  `code` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `created_at` int NOT NULL,
  `type` smallint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `tracking_info` (
  `id` int NOT NULL,
  `carrier_id` int NOT NULL,
  `service_id` int NOT NULL,
  `tracking` varchar(100) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `user` (
  `id` int NOT NULL,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `password_hash` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `auth_key` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `confirmed_at` int DEFAULT NULL,
  `unconfirmed_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `blocked_at` int DEFAULT NULL,
  `registration_ip` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `created_at` int NOT NULL,
  `updated_at` int NOT NULL,
  `flags` int NOT NULL DEFAULT '0',
  `last_login_at` int DEFAULT NULL,
  `last_login_ip` varchar(45) DEFAULT NULL,
  `customer_id` int NOT NULL DEFAULT '0' COMMENT 'User is associated with this customer as it''s parent',
  `facility_id` int NOT NULL DEFAULT '0' COMMENT 'User is associated with this facility if it is present',
  `auth_tf_key` varchar(16) DEFAULT NULL,
  `auth_tf_enabled` tinyint(1) DEFAULT '0',
  `password_changed_at` int DEFAULT NULL,
  `gdpr_consent` tinyint(1) DEFAULT '0',
  `gdpr_consent_date` int DEFAULT NULL,
  `gdpr_deleted` tinyint(1) DEFAULT '0',
  `type` int NOT NULL DEFAULT '0' COMMENT 'User type association - 0 = customer, 1 = warehouse'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `user_customer` (
  `id` int NOT NULL,
  `user_id` int NOT NULL COMMENT 'Reference to user',
  `customer_id` int NOT NULL COMMENT 'Reference to customer'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Associate user with customers';

CREATE TABLE `user_warehouse` (
  `id` int NOT NULL,
  `warehouse_id` int NOT NULL,
  `user_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `warehouse` (
  `id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` int NOT NULL,
  `updated_at` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `webhook` (
  `id` int NOT NULL,
  `endpoint` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `authentication_type` int DEFAULT NULL,
  `user` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pass` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_id` int NOT NULL,
  `active` tinyint(1) NOT NULL,
  `user_id` int NOT NULL,
  `created_at` int NOT NULL,
  `updated_at` int NOT NULL,
  `signing_secret` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `webhook_log` (
  `id` int NOT NULL,
  `webhook_id` int NOT NULL,
  `response` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` int NOT NULL,
  `updated_at` int NOT NULL,
  `status_code` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `webhook_trigger` (
  `id` int NOT NULL,
  `webhook_id` int NOT NULL,
  `status_id` int NOT NULL,
  `created_at` int NOT NULL,
  `updated_at` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index2` (`state_id`);

ALTER TABLE `alias_children`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `alias_parent`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `api_consumer`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `auth_key` (`auth_key`);

ALTER TABLE `auth_assignment`
  ADD PRIMARY KEY (`item_name`,`user_id`),
  ADD KEY `idx-auth_assignment-user_id` (`user_id`);

ALTER TABLE `auth_item`
  ADD PRIMARY KEY (`name`),
  ADD KEY `rule_name` (`rule_name`),
  ADD KEY `idx-auth_item-type` (`type`);

ALTER TABLE `auth_item_child`
  ADD PRIMARY KEY (`parent`,`child`),
  ADD KEY `child` (`child`);

ALTER TABLE `auth_rule`
  ADD PRIMARY KEY (`name`);

ALTER TABLE `batch`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `batch_item`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `behavior`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `behavior_meta`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `bulk_action`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `bulk_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx-bulk_item-bulk_action_id` (`bulk_action_id`),
  ADD KEY `idx-bulk_item-order_id` (`order_id`);

ALTER TABLE `carrier`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `coldco_holiday`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `coldco_zip_lookup`
  ADD PRIMARY KEY (`zip`),
  ADD UNIQUE KEY `zip_UNIQUE` (`zip`);

ALTER TABLE `country`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx-customers-state_id` (`state_id`);

ALTER TABLE `customers_meta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_meta_key_idx` (`key`);

ALTER TABLE `fulfillment`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `fulfillment_meta`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `fulfillment_service_mapping`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `integration`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `integration_hookdeck`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `integration_meta`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `integration_webhook`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `invoice`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `invoice_items`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index2` (`order_id`),
  ADD KEY `index3` (`sku`),
  ADD KEY `order_id_idx` (`order_id`);

ALTER TABLE `migration`
  ADD PRIMARY KEY (`version`);

ALTER TABLE `one_time_charge`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer` (`customer_id`),
  ADD KEY `status` (`status_id`),
  ADD KEY `customer_reference_idx` (`customer_reference`,`customer_id`),
  ADD KEY `date_idx` (`created_date`),
  ADD KEY `idx-orders-warehouse_id` (`warehouse_id`);
ALTER TABLE `orders` ADD FULLTEXT KEY `customer_reference` (`customer_reference`);
ALTER TABLE `orders` ADD FULLTEXT KEY `customer_reference_2` (`customer_reference`);

ALTER TABLE `order_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index2` (`status_id`,`order_id`),
  ADD KEY `order_id_idx` (`order_id`);

ALTER TABLE `packages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id_idx` (`order_id`);

ALTER TABLE `package_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `package_id_idx` (`package_id`),
  ADD KEY `order_id_idx` (`order_id`);

ALTER TABLE `package_items_lot_info`
  ADD PRIMARY KEY (`id`),
  ADD KEY `package_items_id_idx` (`package_items_id`);

ALTER TABLE `paymentmethod`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `payment_intent`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `profile`
  ADD PRIMARY KEY (`user_id`);

ALTER TABLE `queue`
  ADD PRIMARY KEY (`id`),
  ADD KEY `channel` (`channel`),
  ADD KEY `reserved_at` (`reserved_at`),
  ADD KEY `priority` (`priority`);

ALTER TABLE `queue_exec`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ind_qe_push_id` (`push_id`),
  ADD KEY `ind_qe_worker_id` (`worker_id`);

ALTER TABLE `queue_push`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ind_qp_parent_id` (`parent_id`),
  ADD KEY `ind_qp_job_uid` (`sender_name`,`job_uid`),
  ADD KEY `ind_qp_job_class` (`job_class`),
  ADD KEY `ind_qp_first_exec_id` (`first_exec_id`),
  ADD KEY `ind_qp_last_exec_id` (`last_exec_id`);

ALTER TABLE `queue_worker`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ind_qw_finished_at` (`finished_at`),
  ADD KEY `ind_qw_last_exec_id` (`last_exec_id`);

ALTER TABLE `scheduled_orders`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `service`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index2` (`carrier_id`);

ALTER TABLE `shopify_app`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `shop` (`shop`);

ALTER TABLE `shopify_webhook`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `sku`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `social_account`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `account_unique` (`provider`,`client_id`),
  ADD UNIQUE KEY `account_unique_code` (`code`),
  ADD KEY `fk_user_account` (`user_id`);

ALTER TABLE `states`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `status`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `subscription`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `subscription_items`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `token`
  ADD UNIQUE KEY `token_unique` (`user_id`,`code`,`type`);

ALTER TABLE `tracking_info`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_unique_username` (`username`),
  ADD UNIQUE KEY `user_unique_email` (`email`);

ALTER TABLE `user_customer`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx-user_customer-user` (`user_id`),
  ADD KEY `idx-user_customer-customer` (`customer_id`);

ALTER TABLE `user_warehouse`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `warehouse`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `webhook`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx-webhook-customer_id` (`customer_id`),
  ADD KEY `idx-webhook-user_id` (`user_id`);

ALTER TABLE `webhook_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx-webhook_log-webhook_id` (`webhook_id`);

ALTER TABLE `webhook_trigger`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx-webhook_trigger-webhook_id` (`webhook_id`),
  ADD KEY `idx-webhook_trigger-status_id` (`status_id`);


ALTER TABLE `addresses`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `alias_children`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `alias_parent`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `api_consumer`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `batch`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `batch_item`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `behavior`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `behavior_meta`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `bulk_action`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `bulk_item`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `carrier`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `coldco_holiday`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `country`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `customers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `customers_meta`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `fulfillment`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `fulfillment_meta`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `fulfillment_service_mapping`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `integration`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `integration_hookdeck`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `integration_meta`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `integration_webhook`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `inventory`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `invoice`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `invoice_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `one_time_charge`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `order_history`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `packages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `package_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `package_items_lot_info`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `paymentmethod`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `payment_intent`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `queue`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `queue_exec`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

ALTER TABLE `queue_push`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

ALTER TABLE `queue_worker`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

ALTER TABLE `scheduled_orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `service`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `shopify_app`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `shopify_webhook`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `sku`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `social_account`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `states`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `status`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `subscription`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `subscription_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `tracking_info`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `user_customer`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `user_warehouse`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `warehouse`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `webhook`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `webhook_log`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `webhook_trigger`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;


ALTER TABLE `auth_assignment`
  ADD CONSTRAINT `auth_assignment_ibfk_1` FOREIGN KEY (`item_name`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `auth_item`
  ADD CONSTRAINT `auth_item_ibfk_1` FOREIGN KEY (`rule_name`) REFERENCES `auth_rule` (`name`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `auth_item_child`
  ADD CONSTRAINT `auth_item_child_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `auth_item_child_ibfk_2` FOREIGN KEY (`child`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `orders`
  ADD CONSTRAINT `fk-orders-warehouse_id` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouse` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `profile`
  ADD CONSTRAINT `fk_user_profile` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

ALTER TABLE `social_account`
  ADD CONSTRAINT `fk_user_account` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

ALTER TABLE `token`
  ADD CONSTRAINT `fk_user_token` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

ALTER TABLE `webhook`
  ADD CONSTRAINT `fk-webhook-customer_id` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk-webhook-user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

ALTER TABLE `webhook_log`
  ADD CONSTRAINT `fk-webhook_log-webhook_id` FOREIGN KEY (`webhook_id`) REFERENCES `webhook` (`id`) ON DELETE CASCADE;

ALTER TABLE `webhook_trigger`
  ADD CONSTRAINT `fk-webhook_trigger-status_id` FOREIGN KEY (`status_id`) REFERENCES `status` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk-webhook_trigger-webhook_id` FOREIGN KEY (`webhook_id`) REFERENCES `webhook` (`id`) ON DELETE CASCADE;
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
