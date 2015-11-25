<?php

/**
 * PHP library to interact with Phonegap Build API
 *
 * The latest version of Phonegap for the time when library was developed 3.1.0
 * Anyways, it has to work with the latest versions of Cordova (Phonegap) 5+.
 * Contains all methods the a presented in this version of API
 *
 * Requires CURL PHP extension to be installed and enabled
 *
 * Original API documentation:
 * @link http://docs.build.phonegap.com/en_US/developer_api_api.md.html
 * @link http://docs.build.phonegap.com/en_US/developer_api_read.md.html
 * @link http://docs.build.phonegap.com/en_US/developer_api_write.md.html
 *
 * @author Michael Radionov <https://github.com/mradionov>
 * @link https://github.com/mradionov/phonegap-build-api
 * @license MIT
 */
class PhonegapBuildApi
{
    const IOS = 'ios';
    const ANDROID = 'android';

    const ROLE_TESTER = 'tester'; // read-only
    const ROLE_DEV = 'dev'; // read and write

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
    protected $codes = array(200, 201, 202, 302);

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
     * @param string $usernameOrToken - username of token
     * @param string $password - password, if username passed as first arg
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
     * @param string $usernameOrToken - username of token
     * @param string $password - password, if username passed as first arg
     *
     * @return PhonegapBuildApi
     */
    public static function factory($usernameOrToken = null, $password = null)
    {
        return new self($usernameOrToken, $password);
    }

    /**
     * Set username and password for authentication, if set
     *
     * Token will be cleared
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
        $this->token = '';
        return $this;
    }

    /**
     * Set token for authentication
     *
     * Username and password will be cleared, if set
     *
     * @param string $token
     *
     * @return PhonegapBuildApi
     */
    public function setToken($token)
    {
        $this->token = $token;
        $this->username = '';
        $this->password = '';
        return $this;
    }

    // ------------------------------------
    // Read API methods
    // ------------------------------------

    /**
     * Get user's profile information
     *
     * @link http://docs.build.phonegap.com/en_US/developer_api_read.md.html#_get_https_build_phonegap_com_api_v1_me
     *
     * @return mixed: array on success | false on fail
     */
    public function getProfile()
    {
        return $this->request('me');
    }

    /**
     * Get user's applications
     *
     * @link http://docs.build.phonegap.com/en_US/developer_api_read.md.html#_get_https_build_phonegap_com_api_v1_apps
     *
     * @return mixed: array on success | false on fail
     */
    public function getApplications()
    {
        return $this->request('apps');
    }

    /**
     * Get user's application by application id
     *
     * @link http://docs.build.phonegap.com/en_US/developer_api_read.md.html#_get_https_build_phonegap_com_api_v1_apps_id
     *
     * @param mixed: int | string $applicationId
     *
     * @return mixed: array on success | false on fail
     */
    public function getApplication($applicationId)
    {
        return $this->request(array('apps', $applicationId));
    }

    /**
     * Get user's application icon url by application id
     *
     * @link http://docs.build.phonegap.com/en_US/developer_api_read.md.html#_get_https_build_phonegap_com_api_v1_apps_id_icon
     *
     * @param mixed: int | string $applicationId
     *
     * @return mixed: array on success | false on fail
     */
    public function getApplicationIcon($applicationId)
    {
        return $this->request(array('apps', $applicationId, 'icon'));
    }

    /**
     * Get user's application download url for platform by application id
     *
     * @link http://docs.build.phonegap.com/en_US/developer_api_read.md.html#_get_https_build_phonegap_com_api_v1_apps_id_platform
     *
     * @param mixed: int | string $applicationId
     * @param string $platform - platform name ('android', 'ios', ...')
     *
     * @return mixed: array on success | false on fail
     */
    public function downloadApplicationPlatform($applicationId, $platform)
    {
        return $this->request(array('apps', $applicationId, $platform));
    }

    /**
     * Get user's keys
     *
     * @link http://docs.build.phonegap.com/en_US/developer_api_read.md.html#_get_https_build_phonegap_com_api_v1_keys
     *
     * @return mixed: array on success | false on fail
     */
    public function getKeys()
    {
        return $this->request('keys');
    }

    /**
     * Get user's keys for platform
     *
     * @link http://docs.build.phonegap.com/en_US/developer_api_read.md.html#_get_https_build_phonegap_com_api_v1_keys_platform
     *
     * @param string $platform - platform name ('android', 'ios', ...')
     *
     * @return mixed: array on success | false on fail
     */
    public function getKeysPlatform($platform)
    {
        return $this->request(array('keys', $platform));
    }

    /**
     * Get user's key for platform by key id
     *
     * @link http://docs.build.phonegap.com/en_US/developer_api_read.md.html#_get_https_build_phonegap_com_api_v1_keys_platform_id
     *
     * @param string $platform - platform name ('android', 'ios', ...')
     * @param mixed: int | string $keyId
     *
     * @return mixed: array on success | false on fail
     */
    public function getKeyPlatform($platform, $keyId)
    {
        return $this->request(array('keys', $platform, $keyId));
    }

    // ------------------------------------
    // Write API methods
    // ------------------------------------

    /**
     * Create application
     *
     * @link http://docs.build.phonegap.com/en_US/developer_api_write.md.html#_post_https_build_phonegap_com_api_v1_apps
     * @param array $options - additional options, see details in API docs
     *
     * @return mixed: array on success | false on fail
     */
    public function createApplication($options = array())
    {
        // duplicating the default values from API documentation
        $defaults = array(
            'title' => 'Phonegap Application',
            'package' => 'com.phonegap.www',
            'version' => '0.0.1',
            'description' => '',
            'debug' => false,
            // don't set defaults for keys, as it will throw an error
            // if you want to set keys just pass them as options
            // 'keys' => array(),
            'private' => true,
            'phonegap_version' => '3.1.0',
            'hydrates' => false,
        );

        $options = array_merge($defaults, $options);

        return $this->request('apps', 'post', $options);
    }

    /**
     * Create application using GitHub repository
     *
     * @link http://docs.build.phonegap.com/en_US/developer_api_write.md.html#_post_https_build_phonegap_com_api_v1_apps
     *
     * @param string $source - GitHub repository
     * @param array $options - additional options, see details in API docs
     *
     * @return mixed: array on success | false on fail
     */
    public function createApplicationFromRepo($source, $options = array())
    {
        $options = array_merge($options, array(
            'create_method' => 'remote_repo',
            'repo' => $source,
        ));

        return $this->createApplication($options);
    }

    /**
     * Create application from file
     *
     * @link http://docs.build.phonegap.com/en_US/developer_api_write.md.html#_post_https_build_phonegap_com_api_v1_apps
     *
     * @param string $source - file path
     * @param array $options - additional options, see details in API docs
     *
     * @return mixed: array on success | false on fail
     */
    public function createApplicationFromFile($source, $options = array())
    {
        $options = array_merge($options, array(
            'create_method' => 'file',
            'file' => $this->file($source),
        ));

        return $this->createApplication($options);
    }

    /**
     * Update application
     *
     * @link http://docs.build.phonegap.com/en_US/developer_api_write.md.html#_put_https_build_phonegap_com_api_v1_apps_id
     *
     * @param mixed: int | string $applicationId
     * @param array $options - additional options, see details in API docs
     *
     * @return mixed: array on success | false on fail
     */
    public function updateApplication($applicationId, $options = array())
    {
        return $this->request(array('apps', $applicationId), 'put', $options);
    }

    /**
     * Update application from GitHub repository
     *
     * No need to pass repository as it was set when application had been created
     *
     * @link http://docs.build.phonegap.com/en_US/developer_api_write.md.html#_put_https_build_phonegap_com_api_v1_apps_id
     *
     * @param mixed: int | string $applicationId
     * @param array $options - additional options, see details in API docs
     *
     * @return mixed: array on success | false on fail
     */
    public function updateApplicationFromRepo($applicationId, $options = array())
    {
        $options = array_merge($options, array(
            'pull' => true,
        ));

        return $this->updateApplication($applicationId, $options);
    }

    /**
     * Update application from file
     *
     * @link http://docs.build.phonegap.com/en_US/developer_api_write.md.html#_put_https_build_phonegap_com_api_v1_apps_id
     *
     * @param mixed: int | string $applicationId
     * @param string $source - file path
     * @param array $options - additional options, see details in API docs
     *
     * @return mixed: array on success | false on fail
     */
    public function updateApplicationFromFile($applicationId, $source, $options = array())
    {
        $options = array_merge($options, array(
            'file' => $this->file($source),
        ));

        return $this->updateApplication($applicationId, $options);
    }

    /**
     * Update application icon
     *
     * @link http://docs.build.phonegap.com/en_US/developer_api_write.md.html#_post_https_build_phonegap_com_api_v1_apps_id_icon
     *
     * @param mixed: int | string $applicationId
     * @param string $source - png icon file path
     *
     * @return mixed: array on success | false on fail
     */
    public function updateApplicationIcon($applicationId, $source)
    {
        $options['icon'] = $this->file($source);

        return $this->request(array('apps', $applicationId, 'icon'), 'post', $options);
    }

    /**
     * Start building application
     *
     * @link http://docs.build.phonegap.com/en_US/developer_api_write.md.html#_post_https_build_phonegap_com_api_v1_apps_id_build
     *
     * @param mixed: int | string $applicationId
     * @param array | string $platforms - platform name ('android', 'ios', ...')
     *
     * @return mixed: array on success | false on fail
     */
    public function buildApplication($applicationId, $platforms = array())
    {
        $options = array();
        if (! empty($platforms)) {
            if (! is_array($platforms)) {
                $platforms = array($platforms);
            }
            $options['platforms'] = $platforms;
        }

        return $this->request(array('apps', $applicationId, 'build'), 'post', $options);
    }

    /**
     * Start building application for specified platform
     *
     * @link http://docs.build.phonegap.com/en_US/developer_api_write.md.html#_post_https_build_phonegap_com_api_v1_apps_id_build_platform
     *
     * @param mixed: int | string $applicationId
     * @param mixed: string | array $platform - platform name ('android', 'ios', ...')
     *
     * @return mixed: array on success | false on fail
     */
    public function buildApplicationPlatform($applicationId, $platform)
    {
        return $this->request(array('apps', $applicationId, 'build', $platform), 'post');
    }

    /**
     * Add collaborator
     *
     * @link http://docs.build.phonegap.com/en_US/developer_api_write.md.html#_post_https_build_phonegap_com_api_v1_apps_id_collaborators
     *
     * @param mixed: int | string $applicationId
     * @param array $options - additional options, see details in API docs
     *
     * @return mixed: array on success | false on fail
     */
    public function addCollaborator($applicationId, $options = array())
    {
        $defaults = array(
            'email' => '',
            'role' => self::ROLE_TESTER, // self::ROLE_DEV
        );

        $options = array_merge($defaults, $options);

        return $this->request(array('apps', $applicationId, 'collaborators'), 'post', $options);
    }

    /**
     * Update collaborator
     *
     * @link http://docs.build.phonegap.com/en_US/developer_api_write.md.html#_put_https_build_phonegap_com_api_v1_apps_id_collaborators_id
     *
     * @param mixed: int | string $applicationId
     * @param mixed: int | string $collaboratorId
     * @param array $options- additional options, see details in API docs
     *
     * @return mixed: array on success | false on fail
     */
    public function updateCollaborator($applicationId, $collaboratorId, $options = array())
    {
        $defaults = array(
            'role' => self::ROLE_TESTER, // self::ROLE_DEV
        );

        $options = array_merge($defaults, $options);

        return $this->request(array('apps', $applicationId, 'collaborators', $collaboratorId), 'put', $options);
    }

    /**
     * Add key for platform
     *
     * Better use function for specified platforms, cause it has some values restrictions
     *
     * @link http://docs.build.phonegap.com/en_US/developer_api_write.md.html#_post_https_build_phonegap_com_api_v1_keys_platform
     *
     * @param string $platform - platform name ('android', 'ios', ...')
     * @param array $options - additional options, see details in API docs
     *
     * @return mixed: array on success | false on fail
     */
    public function addKeyPlatform($platform, $options = array())
    {
        return $this->request(array('keys', $platform), 'post', $options);
    }

    /**
     * Add key for android
     *
     * @link http://docs.build.phonegap.com/en_US/developer_api_write.md.html#_post_https_build_phonegap_com_api_v1_keys_platform
     *
     * @param string $title - key title
     * @param string $keystore - keystore file
     * @param array $options - additional options, see details in API docs
     *
     * @return mixed: array on success | false on fail
     */
    public function addKeyAndroid($title, $keystore, $options = array())
    {
        $defaults = array(
            'title' => $title,
            'keystore' => $this->file($keystore),
            // 'alias' => 'release',
            // 'key_pw' => '',
            // 'keystore_pw' => '',
        );

        $options = array_merge($defaults, $options);

        return $this->addKeyPlatform(self::ANDROID, $options);
    }

    /**
     * Add key for ios
     *
     * @link http://docs.build.phonegap.com/en_US/developer_api_write.md.html#_post_https_build_phonegap_com_api_v1_keys_platform
     *
     * @param string $title - key title
     * @param string $cert - p12 certificate file
     * @param string $profile - mobileprovision file
     * @param array $options - additional options, see details in API docs
     *
     * @return mixed: array on success | false on fail
     */
    public function addKeyIos($title, $cert, $profile, $options = array())
    {
        $defaults = array(
            'title' => $title,
            'cert' => $this->file($cert),
            'profile' => $this->file($profile),
            // 'password' => '',
        );

        $options = array_merge($defaults, $options);

        return $this->addKeyPlatform(self::IOS, $options);
    }

    /**
     * Update / unlock key for platform
     *
     * Better use function for specified platforms, cause it has some values restrictions
     *
     * @link http://docs.build.phonegap.com/en_US/developer_api_write.md.html#_put_https_build_phonegap_com_api_v1_keys_platform
     *
     * @param string $platform
     * @param mixed: int | string $keyId
     * @param array $options - additional options, see details in API docs
     *
     * @return mixed: array on success | false on fail
     */
    public function updateKeyPlatform($platform, $keyId, $options = array())
    {
        return $this->request(array('keys', $platform, $keyId), 'put', $options);
    }

    /**
     * Update / unlock key for ios
     *
     * @link http://docs.build.phonegap.com/en_US/developer_api_write.md.html#_put_https_build_phonegap_com_api_v1_keys_platform
     *
     * @param mixed: int | string $keyId
     * @param string $password - key password
     *
     * @return mixed: array on success | false on fail
     */
    public function updateKeyIos($keyId, $password)
    {
        $options['password'] = $password;

        return $this->updateKeyPlatform(self::IOS, $keyId, $options);
    }

    /**
     * Update / unlock key for android
     *
     * @link http://docs.build.phonegap.com/en_US/developer_api_write.md.html#_put_https_build_phonegap_com_api_v1_keys_platform
     *
     * @param mixed: int | string $keyId
     * @param string $keyPw - key password
     * @param string $keystorePw - keystore password
     *
     * @return mixed: array on success | false on fail
     */
    public function updateKeyAndroid($keyId, $keyPw, $keystorePw)
    {
        $options = array(
            'key_pw' => $keyPw,
            'keystore_pw' => $keystorePw,
        );

        return $this->updateKeyPlatform(self::ANDROID, $keyId, $options);
    }

    /**
     * Delete application
     *
     * @link http://docs.build.phonegap.com/en_US/developer_api_write.md.html#_delete_https_build_phonegap_com_api_v1_apps_id
     *
     * @param mixed: int | string $applicationId
     *
     * @return mixed: array on success | false on fail
     */
    public function deleteApplication($applicationId)
    {
        return $this->request(array('apps', $applicationId), 'delete');
    }

    /**
     * Delete collaborator
     *
     * @link http://docs.build.phonegap.com/en_US/developer_api_write.md.html#_delete_https_build_phonegap_com_api_v1_apps_id_collaborators_id
     *
     * @param mixed: int | string $applicationId
     * @param mixed: int | string $collaboratorId
     *
     * @return mixed: array on success | false on fail
     */
    public function deleteCollaborator($applicationId, $collaboratorId)
    {
        return $this->request(array('apps', $applicationId, 'collaborators', $collaboratorId), 'delete');
    }

    /**
     * Delete key for platform
     *
     * @link http://docs.build.phonegap.com/en_US/developer_api_write.md.html#_delete_https_build_phonegap_com_api_v1_keys_platform_id
     *
     * @param string $platform - platform name ('android', 'ios', ...')
     * @param mixed: int | string $keyId
     *
     * @return mixed: array on success | false on fail
     */
    public function deleteKeyPlatform($platform, $keyId)
    {
        return $this->request(array('keys', $platform, $keyId), 'delete');
    }

    // ------------------------------------
    // Public utility methods
    // ------------------------------------

    /**
     * Check whether last request was successful
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
     * Return file path for curl
     *
     * @param string $source
     *
     * @return string
     */
    protected function file($source)
    {
        return '@' . realpath($source);
    }

    /**
     * PHP 5.5 introduced a CurlFile object that deprecates the old @filename syntax
     * See: https://wiki.php.net/rfc/curl-file-upload
     *
     * @param string $file constructed file string
     *
     * @return mixed instance of CurlFile or string
     */
    protected function toCurlFile($file) {
        if (function_exists('curl_file_create')) {
            // remove first @ character
            return curl_file_create(substr($file, 1));
        }
        return $file;
    }

    /**
     * Represent associative array as string
     *
     * @param array $array
     * @param string $keyValueSeparator
     * @param string $pairSeparator
     *
     * @return string
     */
    protected function assocToString($array, $keyValueSeparator = ' - ', $pairSeparator = '; ')
    {
        if (! is_array($array)) {
            return print_r($array, true);
        }

        foreach ($array as $key => &$value) {
            $value = $key . $keyValueSeparator . $value;
        }
        return implode($pairSeparator, $array);
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
    protected function request($uri, $method = 'get', $options = array())
    {
        $this->clear();

        if (! in_array(strtolower($method), $this->methods)) {
            return $this->setError('Unknown request method: ' . $method);
        }

        if (! ($this->token || ($this->username && $this->password))) {
            return $this->setError('Please provide token or username and password');
        }

        // if uri is passed as array, create uri string from uri parts
        if (is_array($uri)) {
            $uri = implode('/', $uri);
        }

        // api request url
        $url = $this->endpoint . $uri;

        $handle = curl_init();

        if ($this->token) {
            // if using token - add it to final request url
            $url .= '?auth_token=' . $this->token;
        } else {
            // if usting username and password - pass then as curl option
            curl_setopt($handle, CURLOPT_USERPWD, $this->username . ':' . $this->password);
        }

        curl_setopt($handle, CURLOPT_URL, $url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        curl_setopt($handle, CURLOPT_HTTPHEADER, array(
            'Content-type: multipart/form-data;',
        ));

        // if request has additional options - add them to request
        if (! empty($options)) {

            // extract files, pass them separately
            $files = array();
            foreach ($options as $key => $value) {
                if (! empty($value) && ! is_array($value) && $value[0] === '@') {
                    $files[$key] = $this->toCurlFile($value);
                    unset($options[$key]);
                }
            }

            $data = $files;

            if (! empty($options)) {
                // original API requires data to be in json format
                $data = array_merge($data, array(
                    'data' => json_encode($options),
                ));
            }

            curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
        }

        $response = curl_exec($handle);

        if (curl_errno($handle)) {
            $this->setError(curl_error($handle));
            curl_close($handle);
            return false;
        }

        // get httd code to identify if request was successful
        $httpcode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        $this->success = in_array($httpcode, $this->codes);

        curl_close($handle);

        $decoded = json_decode($response, true);

        // request may be successful, but also can have errors, for example, about keys
        if (isset($decoded['error']) && $error = $decoded['error']) {
            if (is_array($error)) {
                $error = $this->assocToString($error);
            }
            $this->setError($error);
        }

        if (! $this->success) {
            return false;
        }

        return $decoded;
    }
}
