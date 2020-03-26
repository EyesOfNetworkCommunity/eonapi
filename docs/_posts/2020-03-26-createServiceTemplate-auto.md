---
category: Fonction
fonction: '/createServiceTemplate'
title: 'createServiceTemplate'
type: 'POST'

layout: default
---

This method allows users get informations about a specific lilac object.

### Request

* The headers must include a **valid authentication token**.
* **The body can't be empty** and must include at least the name attribute, a `string` that will be used as the name of the thing.

```Authentication: bearer TOKEN```
```{
    templateName, templateDescription="", servicesGroup=array(), contacts=array(), contactsGroup=array(), checkCommand, checkCommandParameters=array(), templatesToInherit=array(), exportConfiguration = FALSE
}```

### Response

**If succeeds**, Create a new Service template, if you didn't give templatesToInherit it will provide "GENERIC_SERVICE" as Inheritance template. The argument witch is by default array take names of objects they are bind..

```Status: 200 OK```
```{
    "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]
}```

For errors responses, see the [response status codes documentation](#response-status-codes).