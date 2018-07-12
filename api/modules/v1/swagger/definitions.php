<?php

namespace api\modules\v1\swagger;

/**
 * @SWG\Definition(
 *     definition = "ErrorMessage",
 *
 *     @SWG\Property( property = "code",    type = "integer" ),
 *     @SWG\Property( property = "message", type = "string" ),
 * )
 */

/**
 * @SWG\Definition(
 *     definition = "ErrorData",
 *
 *     @SWG\Property( property = "code", type = "integer" ),
 *     @SWG\Property(
 *            property = "error",
 *            type = "object",
 *            description = "Validation errors or several error messages"
 *        ),
 * )
 */