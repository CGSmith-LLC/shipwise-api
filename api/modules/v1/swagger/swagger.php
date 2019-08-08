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
 *         description="**Ship Wise** connects ecommerce sites with fulfillment centers in a standard way. See more at https://getshipwise.com

Content-Type must be application/json.
To request your API credentials please contact us.",
 *
 *         @SWG\Contact(
 *              name = "Ship Wise Development Team",
 *              email = "sales@cgsmith.net"
 *         )
 *     ),
 *
 *     consumes={"application/json"},
 *     produces={"application/json"},
 *     security={{"apiTokenAuth":{}}}
 * )
 */