---
category: Fonction
fonction: '/getContactGroup'
title: 'getContactGroup'
type: 'POST'

layout: default
---

This method allows users get informations about a specific lilac object.

### Request

* The headers must include a **valid authentication token**.
* **The body can't be empty** and must include at least the name attribute, a `string` that will be used as the name of the thing.

```Authentication: bearer TOKEN```
```{
    contactGroupName=FALSE
}```

### Response

**If succeeds**, return the given contact group otherwise it return all the contac group.

```Status: 200 OK```
```{
    "http_code": "200 OK", "result": [with the executed actions]
}```

For errors responses, see the [response status codes documentation](#response-status-codes).