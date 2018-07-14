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
class AuthenticationResponse extends Model
{
	public $token;
}