---
category: Other
fonction: '/healthCheck'
title: 'Check health'
type: 'POST'

layout: default
---

This method allows users to get general informations about the system.

### Request

* The headers must include a **valid authentication token**.

### Response

**If succeeds**, returns few informations about RAM, disk and ports.

```Status: 200 OK```
```{
    "api_version": "2.4.2",
    "http_code": "200 OK", 
    "result": [with the executed actions]
}```

For errors responses, see the [response status codes documentation](#response-status-codes).