---
category: Host
fonction: '/getHostGroup'
title: 'Get Host Group'
type: 'POST'

layout: default
---

This method allows users get informations about a Host Group.

### Request

* The headers must include a **valid authentication token**.
* **The body can't be empty** and must include at least the name attribute, a `string` that will be used as the name of the host group.

```Authentication: bearer TOKEN```
```{
    "hostGroupName": 'name_of_host_group'
}```

### Response

**If succeeds**, returns informations.

```Status: 200 OK```
```{
    "http_code": "200 OK", 
    "result": [with the executed actions]
}```

For errors responses, see the [response status codes documentation](#response-status-codes).