---
category: Fonction
fonction: '/modifyContact'
title: 'modifyContact'
type: 'POST'

layout: default
---

This method allows users get informations about a specific lilac object.

### Request

* The headers must include a **valid authentication token**.
* **The body can't be empty** and must include at least the name attribute, a `string` that will be used as the name of the thing.

```Authentication: bearer TOKEN```
```{
    contactName, newContactName="", contactAlias="",contactMail="",contactPager="",contactGroup="",serviceNotificationCommand="",hostNotificationCommand="", $options=array(), exportConfiguration = FALSE
}```

### Response

**If succeeds**, modify the given contact. if contact group is already set the membershib will be deleted, The same happen for contact notification command..

```Status: 200 OK```
```{
    "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]
}```

For errors responses, see the [response status codes documentation](#response-status-codes).