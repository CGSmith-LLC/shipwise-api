<?php

namespace api\modules\v1\models;

use yii\base\Model;

/**
 * @SWG\Definition(
 *     definition = "AuthenticationResponse",
 *
 *     @SWG\Property(
 *            property = "token",
 *            type = "string",
 *            description = "This is your API token"
 *        ),
 * )
 */

/**
 * Class AuthenticationResponse
 *
 * @package api\modules\v1\models
 */
class AuthenticationResponse extends Model
{
	public $token;
}