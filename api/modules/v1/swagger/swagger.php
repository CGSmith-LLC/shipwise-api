<?php

namespace api\modules\v1\swagger;

/**
 * @SWG\Swagger(
 *     host = SWAGGER_API_HOST,
 *     schemes = {SWAGGER_API_SCHEMES},
 *     basePath = "/v1",
 *
 *     @SWG\Info(
 *         version="1.0.0",
 *         title="Ship Wise API",
 *         description="**Short description of the API goes here.**

Content-Type must be application/json.
To request your API credentials please contact us.",
 *
 *         @SWG\Contact(
 *              name = "ShipWise Development Team",
 *              email = "info@info.com"
 *         )
 *     ),
 *
 *     consumes={"application/json"},
 *     produces={"application/json"},
 *     security={"apiTokenAuth"}
 * )
 */