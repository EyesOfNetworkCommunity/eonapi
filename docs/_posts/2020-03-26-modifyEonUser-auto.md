---
category: Fonction
fonction: '/modifyEonUser'
title: 'modifyEonUser'
type: 'POST'

layout: default
---

This method allows users get informations about a specific lilac object.

### Request

* The headers must include a **valid authentication token**.
* **The body can't be empty** and must include at least the name attribute, a `string` that will be used as the name of the thing.

```Authentication: bearer TOKEN```
```{
    user_mail=NULL, user_name, new_user_name=NULL,user_descr=NULL,user_group=NULL, user_password=NULL, is_ldap_user=NULL, user_location=NULL, user_limitation=NULL, user_language = NULL, in_nagvis = NULL, in_cacti = NULL, nagvis_group = NULL
}```

### Response

**If succeeds**, Modify a nagios contact user, a eon user and possibly cacti and nagvis user if necessary. ie bellow.

```Status: 200 OK```
```{
    "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]
}```

For errors responses, see the [response status codes documentation](#response-status-codes).