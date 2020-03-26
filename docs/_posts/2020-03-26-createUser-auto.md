---
category: 'Other'
fonction: '/createUser'
title: 'createUser'
type: 'POST'

layout: default
---

This method allows users get informations about a specific lilac object.

### Request

* The headers must include a **valid authentication token**.
* **The body can't be empty** and must include at least the name attribute, a `string` that will be used as the name of the thing.

```Authentication: bearer TOKEN```
```{
    userName,
   userMail,
   admin,
   filterName,
   filterValue,
   exportConfiguration
}```

### Response

**If succeeds**, Create a nagios contact and a eon user. The user could be limited or admin (depends on the parameter "admin"). Limited user: admin=false / admin user: admin=true. For a limited user, the GED xml file is created in /srv/eyesofnetwork/eonweb/cache/ with the filters specified in parameters..

```Status: 200 OK```
```{
    "http_code": "200 OK",
   "result": [with the executed actions]
}```

For errors responses, see the [response status codes documentation](#response-status-codes).