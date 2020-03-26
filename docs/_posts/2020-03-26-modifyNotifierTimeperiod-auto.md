---
category: Fonction
fonction: '/modifyNotifierTimeperiod'
title: 'modifyNotifierTimeperiod'
type: 'POST'

layout: default
---

This method allows users get informations about a specific lilac object.

### Request

* The headers must include a **valid authentication token**.
* **The body can't be empty** and must include at least the name attribute, a `string` that will be used as the name of the thing.

```Authentication: bearer TOKEN```
```{
    timeperiod_name,new_timeperiod_name=NULL, timeperiod_days=NULL, timeperiod_hours_notifications=NULL
}```

### Response

**If succeeds**, Modify The timeperiod of advanced notification (Notifier module) ..

```Status: 200 OK```
```{
    "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]
}```

For errors responses, see the [response status codes documentation](#response-status-codes).