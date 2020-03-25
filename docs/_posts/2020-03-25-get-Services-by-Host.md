---
category: Fonction
path: '/getServicesByHost'
title: 'Get Services By Host'
type: 'POST'

layout: default
---

This method allows users get informations about services linked to a specified host.

### Request

* The headers must include a **valid authentication token**.
* **The body can't be empty** and must include at least the name attribute, a `string` that will be used as the name of the thing.

```Authentication: bearer TOKEN```
```{
    "hostName": 'name'
}```

### Response

**If succeeds**, returns informations about services linked with the given host.

```Status: 200 OK```
```{
    "http_code": "200 OK", 
    "result": [with the executed actions]
}```

For errors responses, see the [response status codes documentation](#response-status-codes).
