---
path: '/getApiKey'
title: 'Authenticate'

layout: nil
---

## Utilisation: How do I use EONAPI?
All calls to the EON HTTP API are performed by HTTP GET/POST requests. The URLs consist of a path to the API function and sometimes some parameters.

Some calls to the API are protected by API key. You need to present a valid key in your request. Each EON user has a private APIKEY that enables to authenticate/validate the privileges.

1. Generate your APIKEY with the EONAPI following this URI in your browser or application API call (this operation should be done one time):
``` http
https://[EON_IP]/eonapi/getApiKey?&username=[username]&password=[password]
```
**Pre-requisites:** You have to be a local admin user (and not an LDAP user) in order to get an APIKEY from the EONAPI. If not, EONAPI will return an "Unauthorized" 401 response.

If authorized, you should have in return a JSON document with your **EONAPI_KEY** value:

``` json
{
    "api_version": "2.4.2",
    "http_code": "200 OK", 
    "EONAPI_KEY": "022dfa0d83996bddada25cd01d051c6d85b64d5e383ef1f9f6cfb30e0f5b1170"
}
``` 

**NB:** Note the **api_version** version for implementation in your apps.

2. Test the privileges of your API key

This API call you will allow you to now if the association username/apiKey is valid & has the needed privileges.
```http
https://[EON_IP]/eonapi/getAuthenticationStatus?&username=[username]&apiKey=[apiKey]
```

You should have an authorized response:
```json
{
    "api_version": "2.4.2",
    "http_code": "200 OK", 
    "Status": "Authorized"
}
```

3. You can use the generated API key in your applications / API calls

There are different methods to test your API.
I recommend the Open Source client software [Postman](https://www.getpostman.com/) to test your requests and check the working of the API. Otherwise, tools like [Curl](https://curl.haxx.se/) will do the job.

A basic API call will look like that:
```http
https://[EON_IP]/eonapi/[API_function]?&username=[username]&apiKey=[apiKey]
```
