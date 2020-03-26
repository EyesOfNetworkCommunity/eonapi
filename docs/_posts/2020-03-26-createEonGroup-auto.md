---
category: Fonction
fonction: '/createEonGroup'
title: 'createEonGroup'
type: 'POST'

layout: default
---

This method allows users get informations about a specific lilac object.

### Request

* The headers must include a **valid authentication token**.
* **The body can't be empty** and must include at least the name attribute, a `string` that will be used as the name of the thing.

```Authentication: bearer TOKEN```
```{
    group_name, group_descr="",is_ldap_group=false, group_right=array()
}```

### Response

**If succeeds**, Create a nagios contact group and a eon group. The user could be limited or admin, If you decide to changed rights, you must provide the complete array like in the ie bellow.

```Status: 200 OK```
```{
    "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]
}```

For errors responses, see the [response status codes documentation](#response-status-codes).