---
category: Fonction
fonction: '/modifyNotifierRule'
title: 'modifyNotifierRule'
type: 'POST'

layout: default
---

This method allows users get informations about a specific lilac object.

### Request

* The headers must include a **valid authentication token**.
* **The body can't be empty** and must include at least the name attribute, a `string` that will be used as the name of the thing.

```Authentication: bearer TOKEN```
```{
    rule_name, rule_type, new_rule_name=NULL, change_type=NULL, rule_timeperiod=NULL, add_rule_method=NULL, delete_rule_method=NULL, rule_contact=NULL, rule_debug=NULL, rule_host=NULL, rule_service=NULL, rule_state=NULL, rule_notificationNumber=NULL,rule_tracking=NULL
}```

### Response

**If succeeds**, Modify a rule of advanced notification menu (Notifier module) ..

```Status: 200 OK```
```{
    "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]
}```

For errors responses, see the [response status codes documentation](#response-status-codes).