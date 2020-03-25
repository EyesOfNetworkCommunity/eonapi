---
category: Fonction
fonction: '/modifyEonGroup'
title: 'modifyEonGroup'
type: 'POST'

layout: default
---

This method allows users get informations about a specific lilac object.

### Request

* The headers must include a **valid authentication token**.
* **The body can't be empty** and must include at least the name attribute, a `string` that will be used as the name of the thing.

```Authentication: bearer TOKEN```
```{
    [group_name,new_group_name=NULL, group_descr=NULL,is_ldap_group=NULL, group_right=NULL]
}```

### Response

**If succeeds**, Modify a nagios contact group and a eon group. The user could be limited..

```Status: 200 OK```
```{
    "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]
}```

For errors responses, see the [response status codes documentation](#response-status-codes).