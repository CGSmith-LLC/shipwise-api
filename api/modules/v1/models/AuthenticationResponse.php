<?php

namespace api\modules\v1\models;

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
class AuthenticationResponse
{
	private $token;

	/**
	 * AuthenticationResponse constructor
	 *
	 * @param string $token
	 */
	function __construct($token)
	{
		$this->token = $token;
	}
}