---
category: Fonction
fonction: '/addContactGroupToServiceInHost'
title: 'addContactGroupToServiceInHost'
type: 'POST'

layout: default
---

This method allows users get informations about a specific lilac object.

### Request

* The headers must include a **valid authentication token**.
* **The body can't be empty** and must include at least the name attribute, a `string` that will be used as the name of the thing.

```Authentication: bearer TOKEN```
```{
    [contactGroupName, serviceName, hostName]
}```

### Response

**If succeeds**, Add a contact group in the given service of the specified host..

```Status: 200 OK```
```{
    "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]
}```

For errors responses, see the [response status codes documentation](#response-status-codes).