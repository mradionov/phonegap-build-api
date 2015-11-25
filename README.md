Phonegap Build API - PHP
===

> PHP library to interact with Phonegap Build API

## Support

The latest version of *Cordova (Phonegap)* for the time when library was developed - **3.1.0**. Contains all methods that are presented in this version of *API*. Anyways, it has to work with the latest versions of *Cordova (Phonegap)* 5+.

OS support: *iOS*, *Android*.

**Requires CURL *PHP* extension** to be installed and enabled, it will trigger a *PHP* error otherwise.

## Install

Install via [Composer](https://getcomposer.org) from command line:

```bash
$ composer require mradionov/phonegap-build-api
```

***

**or** add it to `composer.json`:

```json
{
  "require": {
    "mradionov/phonegap-build-api": "^0.1.0"
  }
}
```

and run

```bash
$ composer install
```

## Use

`PhonegapBuildApi` constructor accepts two arguments, second is optional. If only one argument is provided, then it has to be authentication token. If two - username and password. Otherwise, you can always provide auth params later using public methods `setToken()` and `setCredentials()`. There is also a static `factory` method to use API in a different way:

```php
use PhonegapBuildApi;

// Use token

$api = new PhonegapBuildApi('authentication_token');

// Use username and password

$api = new PhonegapBuildApi('email@example.com', 'password');

// Set auth options after init

$api = new PhonegapBuildApi();

$api->setToken('authentication_token');
$api->setCredentials('email@example.com', 'password');

// Use factory

$res = PhonegapBuildApi::factory('email@example.com', 'password')->getProfile();
```

#### Handle response

Each API method returns response as associative array created from JSON (if successful). You can use method `success()` to check whether or not last request was successful. You can use method `error()` to get error message, if request failed.

```php
$res = $api->getProfile();

if ($api->success()) {

  var_dump($res['email']); // 'email@example.com'

} else {

  var_dump($api->error()); // 'Invalid email or password.'

}
```

## API

##### GET /api/v1/me ([docs](http://docs.build.phonegap.com/en_US/developer_api_read.md.html#_get_https_build_phonegap_com_api_v1_me))

```php
$res = $api->getProfile();
```

##### GET /api/v1/apps ([docs](http://docs.build.phonegap.com/en_US/developer_api_read.md.html#_get_https_build_phonegap_com_api_v1_apps))

```php
$res = $api->getApplications();
```

##### GET /api/v1/apps/:id ([docs](http://docs.build.phonegap.com/en_US/developer_api_read.md.html#_get_https_build_phonegap_com_api_v1_apps_id))

```php
$res = $api->getApplication(1488);
```

##### GET /api/v1/apps/:id/icon ([docs](http://docs.build.phonegap.com/en_US/developer_api_read.md.html#_get_https_build_phonegap_com_api_v1_apps_id_icon))

```php
$res = $api->getApplicationIcon(1488);
```

##### GET /api/v1/apps/:id/:platform ([docs](http://docs.build.phonegap.com/en_US/developer_api_read.md.html#_get_https_build_phonegap_com_api_v1_apps_id_platform))

```php
$res = $api->downloadApplicationPlatform(1488, PhonegapBuildApi::IOS);
$res = $api->downloadApplicationPlatform(1488, PhonegapBuildApi::ANDROID);
```

##### GET /api/v1/keys ([docs](http://docs.build.phonegap.com/en_US/developer_api_read.md.html#_get_https_build_phonegap_com_api_v1_keys))

```php
$res = $api->getKeys();
```

##### GET /api/v1/keys/:platform ([docs](http://docs.build.phonegap.com/en_US/developer_api_read.md.html#_get_https_build_phonegap_com_api_v1_keys_platform))

```php
$res = $api->getKeysPlatform(PhonegapBuildApi::IOS);
$res = $api->getKeysPlatform(PhonegapBuildApi::ANDROID);
```

##### GET /api/v1/keys/:platform/:id ([docs](http://docs.build.phonegap.com/en_US/developer_api_read.md.html#_get_https_build_phonegap_com_api_v1_keys_platform_id))

```php
$res = $api->getKeyPlatform(PhonegapBuildApi::IOS, 228);
$res = $api->getKeyPlatform(PhonegapBuildApi::ANDROID, 228);
```

##### POST /api/v1/apps ([docs](http://docs.build.phonegap.com/en_US/developer_api_write.md.html#_post_https_build_phonegap_com_api_v1_apps))

```php
$res = $api->createApplication(array(
  'title' => 'Phonegap Application',
  'package' => 'com.phonegap.www',
  'version' => '0.0.1',
  'description' => 'Phonegap Application Description',
  'debug' => false,
  'keys' => array(
    'ios' => array(
      'id' => 228,
      'password' => 'key_password'
    ),
  ),
  'private' => true,
  'phonegap_version' => '3.1.0',
  'hydrates' => false,
  // better use methods below or see docs for all options
));
```

From remote repo (preferable):

```php
$res = $api->createApplicationFromRepo('https://github.com/phonegap/phonegap-start', array(
  'title' => 'Phonegap Application',
  // see docs for all options
));
```

From file (preferable):

```php
$res = $api->createApplicationFromFile('/path/to/archive.zip', array(
  'title' => 'Phonegap Application',
  // see docs for all options
));
```

##### PUT /api/v1/apps/:id ([docs](http://docs.build.phonegap.com/en_US/developer_api_write.md.html#_put_https_build_phonegap_com_api_v1_apps_id))

```php
$res = $api->updateApplication(1488, array(
  'title' => 'Phonegap Application',
  // better use methods below or see docs for all options
));
```

From remote repo (preferable):

```php
$res = $api->updateApplicationFromRepo(1488, array(
  'title' => 'Phonegap Application',
  // see docs for all options
));
```

From file (preferable):

```php
$res = $api->updateApplicationFromFile(1488, '/path/to/archive.zip', array(
  'title' => 'Phonegap Application',
  // see docs for all options
));
```

##### POST /api/v1/apps/:id/icon ([docs](http://docs.build.phonegap.com/en_US/developer_api_write.md.html#_post_https_build_phonegap_com_api_v1_apps_id_icon))

```php
$res = $api->updateApplicationIcon(1488, '/path/to/icon.png');
```

##### POST /api/v1/apps/:id/build ([docs](http://docs.build.phonegap.com/en_US/developer_api_write.md.html#_post_https_build_phonegap_com_api_v1_apps_id_build))

```php
$res = $api->buildApplication(1488, array(
  PhonegapBuildApi::IOS, PhonegapBuildApi::ANDROID
));
```

##### POST /api/v1/apps/:id/build/:platform ([docs](http://docs.build.phonegap.com/en_US/developer_api_write.md.html#_post_https_build_phonegap_com_api_v1_apps_id_build_platform))

```php
$res = $api->buildApplicationPlatform(1488, PhonegapBuildApi::IOS);
$res = $api->buildApplicationPlatform(1488, PhonegapBuildApi::ANDROID);
```

##### POST /api/v1/apps/:id/collaborators ([docs](http://docs.build.phonegap.com/en_US/developer_api_write.md.html#_post_https_build_phonegap_com_api_v1_apps_id_collaborators))

```php
$res = $api->addCollaborator(1488, array(
  'email' => 'collab@example.com',
  'role' => PhonegapBuildApi::ROLE_TESTER, // PhonegapBuildApi::ROLE_DEV
  // see docs for all options
));
```

##### PUT /api/v1/apps/:id/collaborators/:id ([docs](http://docs.build.phonegap.com/en_US/developer_api_write.md.html#_put_https_build_phonegap_com_api_v1_apps_id_collaborators_id))

```php
$res = $api->updateCollaborator(1488, 69, array(
  'role' => PhonegapBuildApi::ROLE_TESTER, // PhonegapBuildApi::ROLE_DEV
  // see docs for all options
));
```

##### POST /api/v1/keys/:platform ([docs](http://docs.build.phonegap.com/en_US/developer_api_write.md.html#_post_https_build_phonegap_com_api_v1_keys_platform))

```php
$res = $api->addKeyPlatform(PhonegapBuildApi::IOS, array(
  // better use methods below or see docs for all options
));
$res = $api->addKeyPlatform(PhonegapBuildApi::ANDROID, array(
  // better use methods below or see docs for all options
));
```

Android specific (preferable):

```php
$res = $api->addKeyAndroid('Key Title', '/path/to/key.keystore', array(
  'alias' => 'release',
  // see docs for all options
));
```

iOS specific (preferable):

```php
$res = $api->addKeyIos('Key Title', '/path/to/key.p12', '/path/to/key.mobileprovision', array(
  // see docs for all options
));
```

##### PUT /api/v1/keys/:platform/:id ([docs](http://docs.build.phonegap.com/en_US/developer_api_write.md.html#_put_https_build_phonegap_com_api_v1_keys_platform))

```php
$res = $api->updateKeyPlatform(PhonegapBuildApi::IOS, 228, array(
  // better use methods below or see docs for all options
));
$res = $api->updateKeyPlatform(PhonegapBuildApi::ANDROID, 228, array(
  // better use methods below or see docs for all options
));
```

Android specific (preferable):

```php
$res = $api->updateKeyIos(228, 'key_password');
```

iOS specific (preferable):

```php
$res = $api->updateKeyAndroid(228, 'key_password', 'keystore_password');
```

##### DELETE /api/v1/apps/:id ([docs](http://docs.build.phonegap.com/en_US/developer_api_write.md.html#_delete_https_build_phonegap_com_api_v1_apps_id))

```php
$res = $api->deleteApplication(1488);
```

##### DELETE /api/v1/apps/:id/collaborators/:id ([docs](http://docs.build.phonegap.com/en_US/developer_api_write.md.html#_delete_https_build_phonegap_com_api_v1_apps_id_collaborators_id))

```php
$res = $api->deleteCollaborator(1488, 69);
```

##### DELETE /api/v1/keys/:platform/:id ([docs](http://docs.build.phonegap.com/en_US/developer_api_write.md.html#_delete_https_build_phonegap_com_api_v1_keys_platform_id))

```php
$res = $api->deleteKeyPlatform(PhonegapBuildApi::IOS, 228);
$res = $api->deleteKeyPlatform(PhonegapBuildApi::ANDROID, 228);
```