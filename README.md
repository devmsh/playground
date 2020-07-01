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

### Planned
- [ ] Login by mobile
- [ ] Verify mobile via SMS 
- [ ] Anonymous login support
- [ ] Better device management
- [ ] Support FCM Push notifications

### Methodology

I just install a fresh Laravel installation with authentication enabled, and install Sanctum to enable API token based authentication, then I tried to figure our how to support the same web auth routes as api routes.

Once I finish playing with the code and stabilize the features, I will publish it as a separated packages.

### Documentation

Currently, you can ready the [tests](https://github.com/devmsh/playground/tree/master/tests/Feature), once the package is published, I will document all the features and customization options both from Laravel side and package side.

```
POST: /api/register {name,email,password,device_name}
POST: /api/login {email,password,device_name}
GET: /api/user
POST: /api/logout {from_other:true|false | from_all:true|false}
```

## Have any ideas?

You can open new issue here on github, or you can contact me at [devmsh](https://twitter.com/devmsh).
