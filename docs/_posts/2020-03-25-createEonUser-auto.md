---
category: Fonction
fonction: '/createEonUser'
title: 'createEonUser'
type: 'POST'

layout: default
---

This method allows users get informations about a specific lilac object.

### Request

* The headers must include a **valid authentication token**.
* **The body can't be empty** and must include at least the name attribute, a `string` that will be used as the name of the thing.

```Authentication: bearer TOKEN```
```{
    [user_mail, user_name,user_descr="",user_group, user_password, is_ldap_user=false, user_location="", user_limitation=0, user_language = 0, in_nagvis = false, in_cacti = false, nagvis_group = false]
}```

### Response

**If succeeds**, Create a nagios contact, a eon user and possibly cacti and nagvis user if necessary. ie bellow.

```Status: 200 OK```
```{
    "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]
}```

For errors responses, see the [response status codes documentation](#response-status-codes).