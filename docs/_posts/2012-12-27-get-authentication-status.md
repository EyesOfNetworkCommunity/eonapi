---
category: Fonction
fonction: '/getAuthenticationStatus'
title: 'Get Authentication Status'
type: 'GET'

layout: nil
---

Confirm that the provided user account has admin privileges and the permission to make advanced API calls. This means the association username/apiKey is correct.

### Request

* The headers must include a **valid authentication token**.
```http
https://[EON_IP]/eonapi/getAuthentificationStatus?&username=[username]&apiKey=[apiKey]
```

### Response

Sends back if the user is authorized.

```Status: 200 OK```
``` json
{
    "api_version": "2.4.2",
    "http_code": "200 OK", 
    "Status": "Authorized"
}
```

For errors responses, see the [response status codes documentation](#response-status-codes).