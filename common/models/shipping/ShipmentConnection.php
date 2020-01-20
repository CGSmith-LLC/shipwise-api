<?php

namespace common\models\shipping;

/**
 * Class ShipmentConnection
 *
 * @package common\models\shipping
 */
class ShipmentConnection
{

    /**
     * Curl Connection Timeout (Milliseconds)
     *
     * @var int
     */
    protected $curlConnectTimeoutInMilliseconds = 60000; // 60 seconds
    /**
     * Curl Download Timeout (In Seconds)
     * @var int
     */
    protected $curlDownloadTimeoutInSeconds = 900; // 15 minutes


    /**
     * Run Post Call
     *
     * Run a call to the shipper using the POST method
     *
     * @param string     $url     Shipper API Url
     * @param null|mixed $data    Data to pass to API
     * @param null|array $headers HTTP headers
     *
     * @return string Return data
     * @throws ShipmentException Curl Error
     */
    public function runPostCall($url, $data = null, $headers = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_HEADER, false);

        if ($headers) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        //curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'TLSv1');
        curl_setopt(
            $ch,
            CURLOPT_CONNECTTIMEOUT_MS,
            $this->curlConnectTimeoutInMilliseconds
        );
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->curlDownloadTimeoutInSeconds);
        $response = curl_exec($ch);
        if ($response === false) {
            throw new ShipmentException(curl_error($ch));
        }
        curl_close($ch);

        return $response;
    }

    /**
     * Run Put Call
     *
     * Run a call to the shipper using the PUT method
     *
     * @param string     $url     Shipper API Url
     * @param null|mixed $data    Data to pass to API
     * @param null|array $headers HTTP headers
     *
     * @return string Return data
     * @throws ShipmentException Curl Error
     */
    public function runPutCall($url, $data = null, $headers = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_HEADER, false);
        if ($headers) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        //curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'TLSv1');
        curl_setopt(
            $ch,
            CURLOPT_CONNECTTIMEOUT_MS,
            $this->curlConnectTimeoutInMilliseconds
        );
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->curlDownloadTimeoutInSeconds);
        $response = curl_exec($ch);
        if ($response === false) {
            throw new ShipmentException(curl_error($ch));
        }
        curl_close($ch);

        return $response;
    }

    /**
     * Run GET Call
     *
     * Run a call to the shipper using the GET method
     *
     * @param string     $url     Shipper API Url
     * @param null|mixed $data    Data to pass to API
     * @param null|array $headers HTTP headers
     *
     * @return string Return data
     * @throws ShipmentException Curl Error
     */
    public function runGetCall($url, $data = null, $headers = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_HEADER, false);
        if ($headers) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        //curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'TLSv1');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, $this->curlConnectTimeoutInMilliseconds);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->curlDownloadTimeoutInSeconds);
        $response = curl_exec($ch);
        if ($response === false) {
            throw new ShipmentException(curl_error($ch));
        }
        curl_close($ch);

        return $response;
    }

    /**
     * Run DELETE Call
     *
     * Run a call to the shipper using the DELETE method
     *
     * @param string     $url     Shipper API Url
     * @param null|mixed $data    Data to pass to API
     * @param null|array $headers HTTP headers
     *
     * @return string Return data
     * @throws ShipmentException Curl Error
     */
    public function runDeleteCall($url, $data = null, $headers = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_HEADER, false);
        if ($headers) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        //curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'TLSv1');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, $this->curlConnectTimeoutInMilliseconds);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->curlDownloadTimeoutInSeconds);
        $response = curl_exec($ch);
        if ($response === false) {
            throw new ShipmentException(curl_error($ch));
        }
        curl_close($ch);

        return $response;
    }

    /**
     * Escape XML data characters
     *
     * @param string $string Raw data string
     *
     * @return string Escaped string
     */
    public function xmlOut($string)
    {
        return htmlspecialchars($string, ENT_XML1);
    }
}