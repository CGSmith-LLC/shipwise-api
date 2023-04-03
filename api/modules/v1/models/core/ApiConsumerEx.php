<?php

namespace api\modules\v1\models\core;

use common\models\ApiConsumer;
use yii\web\IdentityInterface;
use Yii;

/**
 * Class ApiConsumerEx
 *
 * @package api\modules\v1\models\core
 *
 */
class ApiConsumerEx extends ApiConsumer implements IdentityInterface
{

    final const DATETIME_FORMAT    = 'Y-m-d H:i:s';
    final const EXPIRE_TOKEN_AFTER = 15; // Time in minutes after which the auth token will expire

    /** @inheritdoc */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    /** @inheritdoc */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findByToken($token);
    }

    /** @inheritdoc */
    public function getId()
    {
        return $this->id;
    }

    /** @inheritdoc */
    public function getAuthKey()
    {
        return $this->auth_token;
    }

    /** @inheritdoc */
    public function validateAuthKey($authKey)
    {
        return null;
    }

    /**
     * Generates authentication token
     *
     * @return $this
     * @throws \yii\base\Exception
     */
    public function generateToken()
    {
        // Generate random string for auth token
        $this->auth_token = Yii::$app->security->generateRandomString();
        $this->updateLastActivity();

        return $this;
    }

    /** @inheritdoc */
    public static function findByKeySecret($key, $secret)
    {
        return parent::findByKeySecret($key, $secret);
    }

    /** @inheritdoc */
    public function resetToken()
    {
        $this->auth_token = null;
        $this->updateLastActivity();

        return $this;
    }

    /** @inheritdoc */
    public function isActive()
    {
        return parent::isActive();
    }

    /** @inheritdoc */
    public function isSuperuser()
    {
        return parent::isSuperuser();
    }

    /**
     * Get Token Expiration date-time
     *
     * @param bool $formatted Whether to format the returned DateTime object
     *
     *
     * @return string|\DateTime
     * @throws \Exception
     */
    public function getTokenExpiration($formatted = true): string|\DateTime
    {
        $lastActivity = new \DateTime($this->last_activity, new \DateTimeZone("UTC"));
        $expiresOn    = $lastActivity->add(
            new \DateInterval('PT' . self::EXPIRE_TOKEN_AFTER . 'M')
        );

        return ($formatted ? $expiresOn->format(self::DATETIME_FORMAT) : $expiresOn);
    }

    /**
     * Is token expired
     *
     * Checks whether current date-time is greater than the
     * last activity date-time plus expiration interval
     *
     * @return bool
     * @throws \Exception
     * @see getTokenExpiration()
     *
     */
    public function isTokenExpired()
    {
        $now = new \DateTime("now", new \DateTimeZone("UTC"));

        return ($now > $this->getTokenExpiration(false));
    }

    /**
     * Update last activity date-time
     *
     * @return $this
     * @throws \Exception
     */
    public function updateLastActivity()
    {
        $this->last_activity =
            (new \DateTime("now", new \DateTimeZone("UTC")))->format(self::DATETIME_FORMAT);

        return $this;
    }
}
