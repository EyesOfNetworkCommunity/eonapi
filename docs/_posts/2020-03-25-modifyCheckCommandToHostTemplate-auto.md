---
category: Fonction
fonction: '/modifyCheckCommandToHostTemplate'
title: 'modifyCheckCommandToHostTemplate'
type: 'POST'

layout: default
---

This method allows users get informations about a specific lilac object.

### Request

* The headers must include a **valid authentication token**.
* **The body can't be empty** and must include at least the name attribute, a `string` that will be used as the name of the thing.

```Authentication: bearer TOKEN```
```{
    [commandName, templateHostName, exportConfiguration=FALSE]
}```

### Response

**If succeeds**, Modify the check command associate with the given host template. returnCode=0 for data updated and 1 if it has failed.

```Status: 200 OK```
```{
    "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]
}```

For errors responses, see the [response status codes documentation](#response-status-codes).