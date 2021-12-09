---
category: '$CATEGORY$'
fonction: '/$FONCTION$'
title: '$NAME$'
type: '$TYPE$'

layout: default
---

This method allows users get informations about a specific lilac object.

### Request

* The headers must include a **valid authentication token**.
* **The body can't be empty** and must include at least the name attribute, a `string` that will be used as the name of the thing.

```Authentication: bearer TOKEN```
```{
    $BODY$
}```

### Response

**If succeeds**, $COMMENT$.

```Status: 200 OK```
```{
    $RESPONSE$
}```

For errors responses, see the [response status codes documentation](#response-status-codes).