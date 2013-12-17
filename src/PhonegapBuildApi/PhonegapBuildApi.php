<?php

namespace PhonegapBuildApi;

/**
 * Requires CURL PHP extension to be installed and enabled
 * 
 * Original API documentation: 
 * @link(http://docs.build.phonegap.com/en_US/3.1.0/developer_api_api.md.html)
 *
 * Shortcuts for links in annotations:
 * 
 * {docs_read_api}
 * @link(http://docs.build.phonegap.com/en_US/3.1.0/developer_api_read.md.html)
 *
 * {docs_write_api}
 * @link(http://docs.build.phonegap.com/en_US/3.1.0/developer_api_write.md.html)
 */
class PhonegapBuildApi
{

    /**
     * Api Endpoint
     *
     * @var string
     */
    protected $endpoint = 'https://build.phonegap.com/api/v1/';

    /**
     * Available methods
     *
     * @var array
     */
    protected $methods = array('get', 'post', 'put', 'delete');

    /**
     * Successful HTTP codes
     *
     * @var array
     */
    protected $codes = array(200, 302);

    /**
     * Username
     * 
     * May stay empty if using token
     *
     * @var string
     */
    protected $username = '';

    /**
     * Password
     *
     * May stay empty if using token
     *
     * @var string
     */
    protected $password = '';

    /**
     * Authentication token
     *
     * May stay empty if using username and password
     *
     * @var string
     */
    protected $token = '';

    /**
     * Check whether a request was successful
     *
     * @var bool
     */
    protected $success = false;

    /**
     * Error string
     *
     * Stores error for request if request failed
     *
     * @var string
     */
    protected $error = '';

    /**
     * Create instance
     *
     * If using username and password, pass both values to constructor;
     * otherwise, if using token, - pass single token value, leave second one empty
     *
     * @param string $usernameOrToken
     * @param string $password
     */
    public function __construct($usernameOrToken = '', $password = '')
    {
        if (! function_exists('curl_init')) {
            trigger_error('PhonegapBuildApi library requires CURL module to be enabled');
        }

        if (! empty($usernameOrToken)) {
            if (! empty($password)) {
                $this->setCredentials($usernameOrToken, $password);
            } else {
                $this->setToken($usernameOrToken);
            }
        }
    }

    /**
     * Create new instance statically
     *
     * @param string $usernameOrToken
     * @param string $password
     *
     * @return PhonegapBuildApi
     */
    public static function factory($usernameOrToken = null, $password = null)
    {
        return new self($usernameOrToken, $password);
    }

    /**
     * Set username and password for authentication
     *
     * @param string $username
     * @param string $password
     *
     * @return PhonegapBuildApi
     */
    public function setCredentials($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
        return $this;
    }

    /**
     * Set token for authentication
     *
     * @param string $token
     *
     * @return PhonegapBuildApi
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    // ------------------------------------
    // Read API methods
    // ------------------------------------

    /**
     * Get user's profile information
     *
     * @link({docs_read_api}, #_get_https_build_phonegap_com_api_v1_me)
     *
     * @return array
     */
    public function getProfile()
    {
        return $this->request('me');
    }

    /**
     * Get user's applications
     *
     * @link({docs_api_read}, #_get_https_build_phonegap_com_api_v1_apps)
     *
     * @return array
     */
    public function getApplications()
    {
        return $this->request('apps');
    }

    /**
     * Get user's application by application id
     *
     * @link({docs_api_read}, #_get_https_build_phonegap_com_api_v1_apps_id)
     * 
     * @param mixed $applicationId
     *
     * @return array
     */
    public function getApplication($applicationId)
    {
        return $this->request(array('apps', $applicationId));
    }

    /**
     * Get user's application icon url by application id
     *
     * @link({docs_api_read}, #_get_https_build_phonegap_com_api_v1_apps_id_icon)
     *
     * @param mixed $applicationId
     *
     * @return array
     */
    public function getApplicationIcon($applicationId)
    {
        return $this->request(array('apps', $applicationId, 'icon'));
    }

    /**
     * Get user's application download url for platform by application id
     *
     * @link({docs_api_read}, #_get_https_build_phonegap_com_api_v1_apps_id_platform)
     * 
     * @param mixed $applicationId
     * @param string $platform
     *
     * @return array
     */
    public function downloadApplicationPlatform($applicationId, $platform)
    {
        return $this->request(array('apps', $applicationId, $platform));
    }

    /**
     * Get user's keys
     *
     * @link({docs_api_read}, #_get_https_build_phonegap_com_api_v1_keys)
     *
     * @return array
     */
    public function getKeys()
    {
        return $this->request('keys');
    }

    /**
     * Get user's keys for platform
     *
     * @link({docs_api_read}, #_get_https_build_phonegap_com_api_v1_keys_platform)
     *
     * @param string $platform
     *
     * @return array
     */
    public function getKeysPlatform($platform)
    {
        return $this->request(array('keys', $platform));
    }

    /**
     * Get user's key for platform by key id
     *
     * @link({docs_api_read}, #_get_https_build_phonegap_com_api_v1_keys_platform_id)
     *
     * @param string $platform
     * @param mixed $keyId
     *
     * @return array
     */
    public function getKeyPlatform($platform, $keyId)
    {
        return $this->request(array('keys', $platform, $keyId));
    }

    // ------------------------------------
    // Write API methods
    // ------------------------------------

    // public function createApplication($options = array())
    // {
    //     $options['title'] = 'Some test';
    //     $options['create_method'] = 'remote_repo';
    //     $options['repo'] = 'https://github.com/micrddrgn/phonegap-start.git';

    //     $result = $this->request('apps', $options, 'POST');

    //     return $result;
    // }

    // ------------------------------------
    // Public utility methods
    // ------------------------------------

    /**
     * Check whether last request was successful
     *
     * @param mixed $result - pass result while check to shorten
     * 
     * @return bool
     */
    public function success()
    {
        return $this->success;
    }

    /**
     * Return error string for last request
     *
     * @return string
     */
    public function error()
    {
        return $this->error;
    }

    // ------------------------------------
    // Protected utility methods
    // ------------------------------------

    /**
     * Set error
     *
     * @param string $error
     *
     * @return  bool
     */
    protected function setError($error)
    {
        $this->error = $error;
        return false;
    }

    /**
     * Clear last request options
     */
    protected function clear()
    {
        $this->error = '';
        $this->success = false;
    }

    /**
     * Perform request to api using CURL
     *
     * @param mixed $uri - may be uri string or array with uri parts
     * @param string $method - request method
     * @param array $options - api request parameters
     *
     * @return array (if succeeded) | boolean (false, if failed)
     */
    protected function request($uri, $method = 'GET', $options = array())
    {
        $this->clear();

        if (! in_array(strtolower($method), $this->methods)) {
            return $this->setError('Unknow request method: ' . $method);
        }

        // if uri is passed as array, create uri string from uri parts
        if (is_array($uri)) {
            $uri = implode('/', $uri);
        }

        // api request url
        $url = $this->endpoint . $uri;

        // if using token - add it to final request url
        if ($this->token) {
            $url .= '?auth_token=' . $this->token;
        }

        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        if (! empty($options)) {
            curl_setopt($handle, CURLOPT_POSTFIELDS, array(
                'data' => json_encode($options),
            ));
        }

        $response = curl_exec($handle);

        // curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        
        if (curl_errno($handle)) {
            $this->setError(curl_error($handle));
            curl_close($handle);
            return false;
        }

        $httpcode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        $this->success = in_array($httpcode, $this->codes);

        curl_close($handle);

        $decoded = json_decode($response, true);

        if (isset($decoded['error']) && ! empty($decoded['error'])) {
            $error = is_array($decoded['error'])
                ? http_build_query($decoded['error'], '', ';')
                : $decoded['error'];
            $this->setError($error);
        }

        if (! $this->success) {
            return false;
        }

        return $decoded;
    }
}
