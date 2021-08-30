<?php

class WebRequest
{
    protected array $options = [];
    protected string $method;
    protected array $get_parameters;
    protected array $post_parameters;
    protected array $request_parameters;
    protected array $path_info_array;

    public function __construct($options)
    {
        $this->options = array_merge(
            [
                'path_info_key' => 'PATH_INFO',
                'path_info_array' => 'SERVER',
                'http_port' => null,
                'https_port' => null,
                'trust_proxy' => true,
            ],
            $options
        );

        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        $this->get_parameters = $_GET;
        $this->post_parameters = $_POST;

        $this->request_parameters = match ($this->method) {
            'POST' => $_POST,
            default => $_GET,
        };
    }

    /**
     * Returns the array that contains all request information ($_SERVER or $_ENV).
     *
     * This information is stored in the path_info_array option.
     *
     * @return  array Path information
     */
    public function getPathInfoArray(): array
    {
        if (!isset($this->path_info_array)) {
            // parse PATH_INFO
            switch ($this->getOption('path_info_array')) {
                case 'SERVER':
                    $this->path_info_array = &$_SERVER;
                    break;

                case 'ENV':
                default:
                    $this->path_info_array = &$_ENV;
            }
        }

        return $this->path_info_array;
    }

    public function getOption($name)
    {
        return $this->options[$name] ?? null;
    }

    public function getUrl(): string
    {
        $pathArray = $this->getPathInfoArray();

        return ($this->isSecure() ? 'https://' : 'http://') .
            $this->getHost().
            ($pathArray['REQUEST_URI'] ?? '');
    }

    /**
     * Returns the remote IP address that made the request.
     *
     * @return string The remote IP address
     */
    public function getRemoteAddress(): string
    {
        $pathInfo = $this->getPathInfoArray();

        return $pathInfo['REMOTE_ADDR'];
    }

    /**
     * Returns user agent info that made the request.
     *
     * @return string The user agent info
     */
    public function getUserAgent(): string
    {
        $pathInfo = $this->getPathInfoArray();

        return $pathInfo['HTTP_USER_AGENT'];
    }

    /**
     * Returns the client IP address that made the request.
     *
     * @param bool $proxy Whether the current request has been made behind a proxy or not
     *
     * @return string Client IP
     */
    public function getClientIp(bool $proxy = true): string
    {
        if ($proxy) {
            $pathInfo = $this->getPathInfoArray();

            if (isset($pathInfo["HTTP_CLIENT_IP"]) && ($ip = $pathInfo["HTTP_CLIENT_IP"])) {
                return $ip;
            }

            if ($this->getOption('trust_proxy') && ($ip = $this->getForwardedFor())) {
                return isset($ip[0]) ? trim($ip[0]) : '';
            }
        }

        return $this->getRemoteAddress();
    }

    public function getForwardedFor() : ?array
    {
        $pathInfo = $this->getPathInfoArray();

        if (empty($pathInfo['HTTP_X_FORWARDED_FOR'])) {
            return null;
        }

        return explode(', ', $pathInfo['HTTP_X_FORWARDED_FOR']);
    }

    public function isSecure(): bool
    {
        $pathArray = $this->getPathInfoArray();

        return
            (isset($pathArray['HTTPS']) && (('on' == strtolower($pathArray['HTTPS']) || 1 == $pathArray['HTTPS']))) ||
            (
                $this->getOption('trust_proxy') &&
                isset($pathArray['HTTP_SSL_HTTPS']) &&
                (('on' == strtolower($pathArray['HTTP_SSL_HTTPS']) || 1 == $pathArray['HTTP_SSL_HTTPS']))
            ) ||
            ($this->getOption('trust_proxy') && $this->isForwardedSecure());
    }

    protected function isForwardedSecure(): bool
    {
        $pathArray = $this->getPathInfoArray();

        return isset($pathArray['HTTP_X_FORWARDED_PROTO']) &&
            'https' == strtolower($pathArray['HTTP_X_FORWARDED_PROTO']);
    }

    public function getHost(): string
    {
        $pathArray = $this->getPathInfoArray();

        if ($this->getOption('trust_proxy') && isset($pathArray['HTTP_X_FORWARDED_HOST'])) {
            $elements = explode(',', $pathArray['HTTP_X_FORWARDED_HOST']);

            return trim($elements[count($elements) - 1]);
        }

        return $pathArray['HTTP_HOST'] ?? '';
    }

    public function getReferer() : string
    {
        $pathArray = $this->getPathInfoArray();

        return $pathArray['HTTP_REFERER'] ?? '';
    }
}
