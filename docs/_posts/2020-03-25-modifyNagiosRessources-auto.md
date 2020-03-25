---
category: Fonction
fonction: '/modifyNagiosRessources'
title: 'modifyNagiosRessources'
type: 'POST'

layout: default
---

This method allows users get informations about a specific lilac object.

### Request

* The headers must include a **valid authentication token**.
* **The body can't be empty** and must include at least the name attribute, a `string` that will be used as the name of the thing.

```Authentication: bearer TOKEN```
```{
    [ressources]
}```

### Response

**If succeeds**, modify ressources represented in nagios by $USERi$, you passed an collection of "Useri":"value" or "" if you want to remove a ressource an example is given bellow.

```Status: 200 OK```
```{
    "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]
}```

For errors responses, see the [response status codes documentation](#response-status-codes).