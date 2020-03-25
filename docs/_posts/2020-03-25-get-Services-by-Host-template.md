---
category: Fonction
path: '/getServicesByHostTemplate'
title: 'Get Services By Host Template'
type: 'POST'

layout: default
---

This method allows users get services linked to a host template.

### Request

* The headers must include a **valid authentication token**.
* **The body can't be empty** and must include at least the name attribute, a `string` that will be used as the name of the thing.

```Authentication: bearer TOKEN```
```{
    "templateHostName": 'name'
}```

### Response

**If succeeds**, returns information of services linked with the given host template.

```Status: 200 OK```
```{
    "http_code": "200 OK", 
    "result": [with the executed actions]
}```

For errors responses, see the [response status codes documentation](#response-status-codes).
