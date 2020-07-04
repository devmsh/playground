## About Laravel Playground

This repo is just a playground where I will prepare and test some ideas of Laravel packages.

- Api Authentication the laravel way 

## Api Authentication

The awesomeness of [Laravel Sanctum](https://laravel.com/docs/master/sanctum) meet with the simplicity of [Laravel UI](https://laravel.com/docs/master/authentication#introduction) so API developers can focus on building their applications.

### Done
- [X] Login by email and password
- [X] Support login attempts
- [X] API enabled guest middleware
- [X] Token generation via Sanctum
- [X] Simple device management via Token names
- [X] Logout from device, other devices, and all devices
- [X] Custom login (e.g. active users only)
- [X] Last user activity via Token
- [X] Login by mobile
- [X] Anonymous login support
- [X] Support FCM Push notifications

### Planned
- [ ] Verify email
- [ ] Forget password 
- [ ] Verify mobile  
- [ ] Better device management
- [ ] Support password-less login

### Methodology

I just install a fresh Laravel installation with authentication enabled, and install Sanctum to enable API token based authentication, then I tried to figure our how to support the same web auth routes as api routes.

Once I finish playing with the code and stabilize the features, I will publish it as a separated packages.

### Documentation

Currently, you can ready the [tests](https://github.com/devmsh/playground/tree/master/tests/Feature), once the package is published, I will document all the features and customization options both from Laravel side and package side.

```
POST: /api/register {name,[email|mobile],password,device_name}
POST: /api/login {[email|mobile],password,device_name}
GET: /api/user
POST: /api/logout {from_other:true|false | from_all:true|false}
```

### Config

You can customize the allowed list of username fields using `username_fields` in `lock.php` config file.

```php
'username_fields' => [
    'email',
    'mobile'
],
```

You can customize the validation rules to be used in the registration process by change the `username_registration_validation` in `lock.php` config file.

```php
'username_registration_validation' => [
    'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
    'mobile' => ['required', 'string', 'min:10', 'unique:users'],
]
```

To enable Anonymous login feature, you must change `anonymous_login` in `lock.php` config file.

```php
'anonymous_login' => true,
```

Then you can directly send a login request without any credentials, but you must send a special payload;
```php
POST: /api/login {device_name,type=anonymous}
```

FCM Notification is also supported using `laravel-notification-channels/fcm`, all what you need to do is to specify the `FIREBASE_CREDENTIALS` in your .env as show in .env.example
```
FIREBASE_CREDENTIALS=/full/path/to/firebase_credentials.json
```

`AccountActivated` notification is available as a sample for you, and you can specify fccm_token both in Registration and Login requests

```
POST: /api/register {name,[email|mobile],password,device_name,fcm_token}
POST: /api/login {[email|mobile],password,device_name,fcm_token}
```

## Have any ideas?

You can open new issue here on github, or you can contact me at [devmsh](https://twitter.com/devmsh).
