---
category: Fonction
fonction: '/createHost'
title: 'createHost'
type: 'POST'

layout: default
---

This method allows users get informations about a specific lilac object.

### Request

* The headers must include a **valid authentication token**.
* **The body can't be empty** and must include at least the name attribute, a `string` that will be used as the name of the thing.

```Authentication: bearer TOKEN```
```{
    templateHostName, hostName, hostIp, hostAlias, contactName, contactGroupName, exportConfiguration
}```

### Response

**If succeeds**, Create a nagios host (affected to the provided parent template [templateHostName]) if not exists and reload lilac configuration. Posibility to attach a contact and/or a contact group to the host in the same time..

```Status: 200 OK```
```{
    "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]
}```

For errors responses, see the [response status codes documentation](#response-status-codes).