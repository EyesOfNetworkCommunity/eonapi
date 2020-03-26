---
category: Fonction
fonction: '/listNagiosObjects'
title: 'listNagiosObjects'
type: 'POST'

layout: default
---

This method allows users get informations about a specific lilac object.

### Request

* The headers must include a **valid authentication token**.
* **The body can't be empty** and must include at least the name attribute, a `string` that will be used as the name of the thing.

```Authentication: bearer TOKEN```
```{
    object, backendid = NULL, columns = FALSE, filters = FALSE
}```

### Response

**If succeeds**, Return nagios object like services, hosts, and their respective informations on which you can filter.

```Status: 200 OK```
```{
    "http_code": "200 OK", "result": [with the executed actions]
}```

For errors responses, see the [response status codes documentation](#response-status-codes).