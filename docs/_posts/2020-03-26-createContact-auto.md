---
category: 'Contact'
fonction: '/createContact'
title: 'createContact'
type: 'POST'

layout: default
---

This method allows users get informations about a specific lilac object.

### Request

* The headers must include a **valid authentication token**.
* **The body can't be empty** and must include at least the name attribute, a `string` that will be used as the name of the thing.

```Authentication: bearer TOKEN```
```{
    contactName,
   contactMail,
   contactAlias="description",
   contactMail,
   contactPager,
   contactGroup,
   options,
   exportConfiguration
}```

### Response

**If succeeds**, Create a nagios contact. In the options variables, you can set the same information than those given in the web interface..

```Status: 200 OK```
```{
    "http_code": "200 OK",
   "result": ["code":returnCode,
  "description":"logs"]
}```

For errors responses, see the [response status codes documentation](#response-status-codes).