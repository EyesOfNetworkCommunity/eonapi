---
category: Fonction
fonction: '/getHostsByTemplate'
title: 'Get Hosts by Template'
type: 'POST'

layout: default
---

This method allows users get informations about hosts through is link with a specific template.

### Request

* The headers must include a **valid authentication token**.
* **The body can't be empty** and must include at least the name attribute, a `string` that will be used as the name of the thing.

```Authentication: bearer TOKEN```
```{
    "templateHostName": 'name_of_template'
}```

### Response

**If succeeds**, return hosts link with the given template host.

```Status: 200 OK```
```{
    "http_code": "200 OK", 
    "result": [with the executed actions]
}```

For errors responses, see the [response status codes documentation](#response-status-codes).
