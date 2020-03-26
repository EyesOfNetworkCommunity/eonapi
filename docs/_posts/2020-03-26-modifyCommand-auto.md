---
category: Fonction
fonction: '/modifyCommand'
title: 'modifyCommand'
type: 'POST'

layout: default
---

This method allows users get informations about a specific lilac object.

### Request

* The headers must include a **valid authentication token**.
* **The body can't be empty** and must include at least the name attribute, a `string` that will be used as the name of the thing.

```Authentication: bearer TOKEN```
```{
    commandName,newCommandName="",commandLine,commandDescription=""
}```

### Response

**If succeeds**, modify a command to Nagios. returncode=0 or 1 if failed or nothing change.

```Status: 200 OK```
```{
    "http_code": "200 OK", "result": ["code":returnCode,"description":"logs","changes":numerOfchanges]
}```

For errors responses, see the [response status codes documentation](#response-status-codes).