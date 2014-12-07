<?php
namespace ngyuki\ComposerCurlPlugin;

class CurlClient
{
    private $curl;

    public function __construct($userAgent)
    {
        $this->curl = curl_init();

        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_USERAGENT => $userAgent,
            CURLOPT_ENCODING => "",
        );

        curl_setopt_array($this->curl, $options);
    }

    /**
     * @param string $url
     * @return array [$code, $headers, $body]
     */
    public function get($url)
    {
        curl_setopt($this->curl, CURLOPT_URL, $url);

        $resp = curl_exec($this->curl);
        if ($resp === false) {
            throw new \RuntimeException("Unable download $url");
        }

        list ($header, $body) = explode("\r\n\r\n", $resp, 2);
        $headers = explode("\r\n", $header);

        $info = curl_getinfo($this->curl);
        $code = $info['http_code'];

        return array($code, $headers, $body);
    }
}
