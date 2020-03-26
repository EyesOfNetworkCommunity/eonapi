---
category: 'Check'
fonction: '/addCheckCommandParameterToServiceTemplate'
title: 'addCheckCommandParameterToServiceTemplate'
type: 'POST'

layout: default
---

This method allows users get informations about a specific lilac object.

### Request

* The headers must include a **valid authentication token**.
* **The body can't be empty** and must include at least the name attribute, a `string` that will be used as the name of the thing.

```Authentication: bearer TOKEN```
```{
    templateServiceName,
  parameters
}```

### Response

**If succeeds**, Add command parameter to a service template.returncode=0 or 1 if failed /!\parameters is a list.

```Status: 200 OK```
```{
    "http_code": "200 OK",
   "result": ["code":returnCode,
  "description":"logs"]
}```

For errors responses, see the [response status codes documentation](#response-status-codes).