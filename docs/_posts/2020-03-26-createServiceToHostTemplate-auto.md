---
category: Fonction
fonction: '/createServiceToHostTemplate'
title: 'createServiceToHostTemplate'
type: 'POST'

layout: default
---

This method allows users get informations about a specific lilac object.

### Request

* The headers must include a **valid authentication token**.
* **The body can't be empty** and must include at least the name attribute, a `string` that will be used as the name of the thing.

```Authentication: bearer TOKEN```
```{
    hostTemplateName, service, exportConfiguration
}```

### Response

**If succeeds**, Create a service in the given host template. (allow to create a service with optional inherited template, optional command and parameters in a specified host template).

```Status: 200 OK```
```{
    "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]
}```

For errors responses, see the [response status codes documentation](#response-status-codes).