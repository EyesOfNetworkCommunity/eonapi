---
category: Fonction
path: '/getResources'
title: 'Get resources'
type: 'GET'

layout: nil
---

This method allows users get informations about a command.

### Request

* The headers must include a **valid authentication token**.

### Response

**If succeeds**, returns the list of resources.

```Status: 200 OK```
```{
    "http_code": "200 OK", 
    "result": [with the executed actions]
}```

For errors responses, see the [response status codes documentation](#response-status-codes).
