# Eyes Of Network API : EONAPI

## Installation steps: Get started!
1. Clone the project EONAPI:
```bash
$ git clone https://github.com/eyesofnetworkcommunity/eonapi.git eonapi-git
```

2. Make the symbolic links in your project:
```bash
$ ln -sf /srv/eyesofnetwork/eonapi-git/ /srv/eyesofnetwork/eonapi
```

3. Edit the eonapi httpd conf file:
```bash
$ vim /etc/httpd/conf.d/eonapi.conf
```
```php
Alias /eonapi "/srv/eyesofnetwork/eonapi/html/api"


<Directory /srv/eyesofnetwork/eonapi/html/api>
        Options -Indexes
        Require all granted

        FallbackResource index.php
</Directory>
```
4. Restart the httpd daemon:
```bash
$ service httpd restart
```

## Presentation: What is EONAPI?
Eyes Of Network includes a web-based "RESTful" API (Application Programming Interface) called EONAPI that enables external programs to access information from the monitoring database and to manipulate objects inside the databases of EON suite.

In the context of the EON HTTP API, the attribute "RESTful" essentially means:
* that it is HTTP/HTTPS based
* that it uses a set of "HTTP GET/POST" URLs to access and manipulate the data and that you'll get back an JSON document in return (for most calls).

The EON HTTP API offers the following functionality:
* Functions for manipulating objects (e.g. edit, add, delete)

## Utilisation: How do I use EONAPI?
All calls to the EON HTTP API are performed by HTTP GET/POST requests. The URLs consist of a path to the API function and sometimes some parameters.

Some calls to the API are protected by API key. You need to present a valid key in your request. Each EON user has a private APIKEY that enables to authenticate/validate the privileges.

1. Generate your APIKEY with the EONAPI following this URI in your browser or application API call (this operation should be done one time):
```http
https://[EON_IP]/eonapi/getApiKey?&username=[username]&password=[password]
```
**Pre-requisites:** You have to be a local admin user (and not an LDAP user) in order to get an APIKEY from the EONAPI. If not, EONAPI will return an "Unauthorized" 401 response.

If authorized, you should have in return a JSON document with your **EONAPI_KEY** value:
```json
{
    "api_version": "2.4.2",
    "http_code": "200 OK", 
    "EONAPI_KEY": "022dfa0d83996bddada25cd01d051c6d85b64d5e383ef1f9f6cfb30e0f5b1170"
}
```
**NB:** Note the **api_version** version for implementation in your apps.

2. Test the privileges of your API key

This API call you will allow you to now if the association username/apiKey is valid & has the needed privileges.
```http
https://[EON_IP]/eonapi/getAuthenticationStatus?&username=[username]&apiKey=[apiKey]
```

You should have an authorized response:
```json
{
    "api_version": "2.4.2",
    "http_code": "200 OK", 
    "Status": "Authorized"
}
```

3. You can use the generated API key in your applications / API calls

There are different methods to test your API.
I recommend the Open Source client software [Postman](https://www.getpostman.com/) to test your requests and check the working of the API. Otherwise, tools like [Curl](https://curl.haxx.se/) will do the job.

A basic API call will look like that:
```http
https://[EON_IP]/eonapi/[API_function]?&username=[username]&apiKey=[apiKey]
```

## EONAPI features
EONAPI is open source and is built to make object manipulation easier. A few actions could be done remotely by calling the right API URLs.

As a reminder, a basic API call will look like that:
```http
https://[EON_IP]/eonapi/[API_function]?&username=[username]&apiKey=[apiKey]
```

You will find below the updated list of actions (**"API_function"**) possible in EONAPI:

| Action URL **[API_function]** | Request type | Parameters (body/payload) | Expected response | Comments |
| --- | --- | --- | --- | --- |
| `getAuthenticationStatus` | GET | None | "status": "authorized" | Confirm that the provided user account has admin privileges and the permission to make advanced API calls. This means the association username/apiKey is correct.  |
| `getResources` | GET | None | "status": "authorized" | Return the list of resources.  |
| `getCommand` | POST | [**commandName**] | "http_code": "200 OK", "result": [with the executed actions] | Return the informations of a command |
| `getHost` | POST | [**hostName**] | "http_code": "200 OK", "result": [with the executed actions] | return the given host |
| `getHostGroup` | POST | [**hostGroupName**] | "http_code": "200 OK", "result": [with the executed actions] | return the given host group|
| `getHostsByTemplate` | POST | [**templateHostName**] | "http_code": "200 OK", "result": [with the executed actions] | return hosts link with the given template host |
| `getHostsByHostGroup` | POST | [**hostGroupName**] | "http_code": "200 OK", "result": [with the executed actions] | return hosts link with the given hostgroup |
| `getServicesByHost` | POST | [**hostName**] | "http_code": "200 OK", "result": [with the executed actions] | return services link with the given host|
| `getServicesByHostTemplate` | POST | [**templateHostName**] | "http_code": "200 OK", "result": [with the executed actions] | return services link with the given host template|
| `getContact` | POST | [**contactName=FALSE**] | "http_code": "200 OK", "result": [with the executed actions] | return the given contact otherwise it return all the contact|
| `getContactGroup` | POST | [**contactGroupName=FALSE**] | "http_code": "200 OK", "result": [with the executed actions] | return the given contact group otherwise it return all the contac group|
| `createHost` | POST | [**templateHostName, hostName, hostIp, hostAlias, contactName, contactGroupName, exportConfiguration**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]  | Create a nagios host (affected to the provided parent template [templateHostName]) if not exists and reload lilac configuration. Posibility to attach a contact and/or a contact group to the host in the same time. |
| `createEonGroup` | POST | [**group_name, group_descr="",is_ldap_group=false, group_right=array()**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"] | Create a nagios contact group and a eon group. The user could be limited or admin, If you decide to changed rights, you must provide the complete array like in the ie bellow |
| `modifyEonGroup` | POST | [**group_name,new_group_name=NULL, group_descr=NULL,is_ldap_group=NULL, group_right=NULL**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"] | Modify a nagios contact group and a eon group. The user could be limited. If you decide to changed rights, you must provide the complete array like in the ie of 'createEonGroup'|
| `deleteEonGroup` | POST | [**group_name**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"] | delete a eon group.|
| `createUser` | POST | [**userName, userMail, admin, filterName, filterValue, exportConfiguration**] | "http_code": "200 OK", "result": [with the executed actions] | Create a nagios contact and a eon user. The user could be limited or admin (depends on the parameter "admin"). Limited user: admin=false / admin user: admin=true. For a limited user, the GED xml file is created in /srv/eyesofnetwork/eonweb/cache/ with the filters specified in parameters. |
| `createContact` | POST | [**contactName, contactMail, contactAlias="description", contactMail, contactPager, contactGroup, options, exportConfiguration**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"] | Create a nagios contact. In the options variables, you can set the same information than those given in the web interface. |
| `createHostTemplate` | POST | [**templateHostName, templateHostDescription="",exportConfiguration**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]  | Create a new nagios host template. |
| `createHostDowntime`| POST | [**hostName, comment, startTime, endTime, user, fixed=1, duration=1000**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"] | Create a Host's downtime |
| `createServicesDowntime`| POST | [**hostName, serviceName, comment, startTime, endTime, user, fixed=1, duration=1000 , childHostAction = FALSE**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"] | Create a Service's downtime |
| `createHostGroup` | POST | [**hostGroupName, description="host group", exportConfiguration = FALSE**] | "http_code": "200 OK", "result": [with the executed actions] | Create a Host Group |
| `createServiceGroup` | POST | [**serviceGroupName, description="service group", exportConfiguration = FALSE**] | "http_code": "200 OK", "result": [with the executed actions] | Create a Service Group |
| `createContactGroup` | POST | [**contactGroupName, description="contact group", exportConfiguration = FALSE**] | "http_code": "200 OK", "result": [with the executed actions] | Create a contact Group |
| `createServiceTemplate` | POST | [**templateName, templateDescription="", servicesGroup=array(), contacts=array(), contactsGroup=array(), checkCommand, checkCommandParameters=array(), templatesToInherit=array(), exportConfiguration = FALSE**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]  | Create a new Service template, if you didn't give templatesToInherit it will provide "GENERIC_SERVICE" as Inheritance template. The argument witch is by default array take names of objects they are bind. |
| `createServiceToHost` | POST | [**hostName, service, exportConfiguration**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]  | Create a service in the given host. (allow to create a service with optional inherited template, optional command and parameters in a specified host) See example bellow for utilisation |
| `createServiceToHostTemplate` | POST | [**hostTemplateName, service, exportConfiguration**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]  | Create a service in the given host template. (allow to create a service with optional inherited template, optional command and parameters in a specified host template) |
| `addContactToHost` | POST | [**contactName, hostName, exportConfiguration**] | "http_code": "200 OK", "result": [with the executed actions] | Attach a nagios contact to a host if not already attached. |
| `addContactGroupToHost` | POST | [**contactGroupName, hostName, exportConfiguration**] | "http_code": "200 OK", "result": [with the executed actions] | Attach a nagios contact group to a host if not already attached. |
| `addHostTemplateToHost` | POST | [**templateHostName, hostName, exportConfiguration**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"] | Add a host template to a nagios host. |
| `addContactGroupToContact` | POST | [**contactName, contactGroupName, exportConfiguration**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]  | add a contact group to a nagios contact. |
| `addContactNotificationCommandToContact` | POST | [**contactName, commandName, type_command, exportConfiguration**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]  | Add a contact notification command to a nagios contact. |
| `addContactToHostTemplate` | POST | [**contactName, templateHostName, exportConfiguration**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]  | Add a contact to a nagios host template. |
| `addServiceTemplateFromService` | POST | [**serviceTemplateName, serviceName, hostName**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]  | Add a service template in the given service of the specified host. |
| `addContactGroupToHostTemplate` | POST | [**contactGroupName, templateHostName, exportConfiguration**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]  | Add a contact group to a nagios host template. |
| `addCommand` | POST | [**commandName,commandLine,commandDescription=""**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"] | Add a command to Nagios.returncode=0 or 1 if failed |
| `addCheckCommandParameterToServiceTemplate` | POST | [**templateServiceName,parameters**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"] | Add command parameter to a service template.returncode=0 or 1 if failed /!\parameters is a list |
| `addHostGroupToHostTemplate` | POST | [**hostGroupName,templateHostName,exportConfiguration = FALSE**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"] | Add a Host group to a host template. returncode=0 or 1 if failed |
| `addInheritanceTemplateToHostTemplate` | POST | [**inheritanceTemplateName,templateHostName,exportConfiguration = FALSE**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"] | Add a Inherit host template to a host template. returncode=0 or 1 if failed |
| `addServiceGroupeToServiceTemplate` | POST | [**serviceGroupName,templateServiceName,exportConfiguration = FALSE**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"] | Add a service group to a service template. returncode=0 or 1 if failed |
| `addContactGroupToServiceInHost` | POST | [**contactGroupName, serviceName, hostName**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]  | Add a contact group in the given service of the specified host. |
| `addContactToServiceInHost` | POST | [**contactName, serviceName, hostName**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]  | Add a contact in the given service of the specified host. |
| `addServiceGroupToServiceInHost` | POST | [**serviceGroupName, serviceName, hostName, exportConfiguration = FALSE**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]  | Add a service group in the given service of the specified host. |
| `addServiceTemplateToServiceInHost` | POST | [**templateServiceName, serviceName, hostName**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]  | Add a service Template in the given service of the specified host. |
| `addContactToServiceTemplate` | POST | [**contactName,templateServiceName,exportConfiguration = FALSE**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"] | Add a contact to a service template. returncode=0 or 1 if failed |
| `addContactGroupToServiceTemplate` | POST | [**contactGroupName,templateServiceName,exportConfiguration = FALSE**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"] | Add a contact group to a service template. returncode=0 or 1 if failed |
| `addInheritServiceTemplateToServiceTemplate` | POST | [**inheritServiceTemplateName,templateServiceName,exportConfiguration = FALSE**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"] | Add a inherited service template to a service template. returncode=0 or 1 if failed |
| `addCustomArgumentsToService` | POST | [**serviceName,hostName,customArguments, exportConfiguration = FALSE**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"] | Add customs arguments to a service. returncode=0 or 1 if failed or didn't changed |
| `addCustomArgumentsToServiceTemplate` | POST | [**templateServiceName,customArguments, exportConfiguration = FALSE**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"] | Add customs arguments to a service template. returncode=0 or 1 if failed or didn't changed |
| `addCheckCommandParameterToServiceInHost` | POST | [**serviceName, hostName, parameters**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"] | Add command parameters in a service of a specified host. returncode=0 or 1 if failed or didn't changed /!\ parameters is a list|
| `addCustomArgumentsToHostTemplate` | POST | [**templateHostName,customArguments, exportConfiguration = FALSE**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"] | Add customs arguments to a host template. returncode=0 or 1 if failed or didn't changed |
| `addCustomArgumentsToHost` | POST | [**hostName,customArguments, exportConfiguration = FALSE**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"] | Add customs arguments to a host. returncode=0 or 1 if failed or didn't changed |
| `addNotifierMethod` | POST | [**method_name,method_type, method_line**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"] | Add a Notifier method into the databases |
| `exportConfiguration` | POST | [**JobName**] | "http_code": "200 OK", "result": [with the executed actions] | Export Nagios Configuration. |
| `listHosts` | POST | [**hostName=FALSE, $hostTemplate=false**] | "http_code": "200 OK", "result": [with the executed actions] | List nagios hosts |
| `checkHost` | POST | [**type, adress, port, path**] | "http_code": "200 OK", "result": [with the executed actions] | Check an particulary host if it's available|
| `listNagiosBackends` | POST | [] | "http_code": "200 OK", "result": [with the executed actions] | Return available backend informations(log) |
| `listNagiosObjects` | POST | [**object, backendid = NULL, columns = FALSE, filters = FALSE**] | "http_code": "200 OK", "result": [with the executed actions] | Return nagios object like services, hosts, and their respective informations on which you can filter |
| `listNagiosStates` | POST | [**backendid = NULL, filters = FALSE**] | "http_code": "200 OK", "result": [with the executed actions] | Return states of hosts and services  |
| `modifyContact` | POST | [**contactName, newContactName="", contactAlias="",contactMail="",contactPager="",contactGroup="",serviceNotificationCommand="",hostNotificationCommand="", $options=array(), exportConfiguration = FALSE**] | "http_code": "200 OK",  "result": ["code":returnCode,"description":"logs"]  | modify the given contact. if contact group is already set the membershib will be deleted, The same happen for contact notification command.  |
| `modifyContactGroup` | POST | [**contactGroupName, newContactGroupName=NULL, description, exportConfiguration = FALSE**] | "http_code": "200 OK",  "result": ["code":returnCode,"description":"logs"]  | modify the given contact group |
| `modifyServiceFromHostTemplate` | POST | [**hostTemplateName, service=array(), exportConfiguration = FALSE**] | "http_code": "200 OK",  "result": ["code":returnCode,"description":"logs"]  | modify the given service with the given columnName => value (ie bellow)|
| `modifyServiceFromHost` | POST | [**hostName, service=array(), exportConfiguration = FALSE**] | "http_code": "200 OK",  "result": ["code":returnCode,"description":"logs"]  | modify the given service with the given columnName => value (ie bellow)|
| `modifyCommand` | POST | [**commandName,newCommandName="",commandLine,commandDescription=""**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs","changes":numerOfchanges] | modify a command to Nagios. returncode=0 or 1 if failed or nothing change |
| `modifyNagiosRessources` | POST | [**ressources**] | "http_code": "200 OK",  "result": ["code":returnCode,"description":"logs"]  | modify ressources represented in nagios by $USERi$, you passed an collection of "Useri":"value" or "" if you want to remove a ressource an example is given bellow |
| `modifyCheckCommandToServiceTemplate` | POST | [**commandName, templateServiceName, exportConfiguration=FALSE**] | "http_code": "200 OK",  "result": ["code":returnCode,"description":"logs"]  | modify Modify the check command associate with the given service template. returnCode=0 for data updated and 1 if it has failed  |
| `modifyCheckCommandToHostTemplate` | POST | [**commandName, templateHostName, exportConfiguration=FALSE**] | "http_code": "200 OK",  "result": ["code":returnCode,"description":"logs"]  | Modify the check command associate with the given host template. returnCode=0 for data updated and 1 if it has failed  |
| `modifyNagiosMainConfiguration` | POST | [**requestConf, exportConfiguration=FALSE**] | "http_code": "200 OK",  "result": ["code":returnCode,"description":"logs"]  | Modify The Nagios global configuration. See bellow the different parameter that you can changed.|
| `modifyNotifierTimeperiod` | POST | [**timeperiod_name,new_timeperiod_name=NULL, timeperiod_days=NULL, timeperiod_hours_notifications=NULL**] | "http_code": "200 OK",  "result": ["code":returnCode,"description":"logs"]  | Modify The timeperiod of advanced notification (Notifier module) .|
| `modifyNotifierMethod` | POST | [**method_name,method_type,new_method_name=NULL, change_type=NULL, method_line=NULL**] | "http_code": "200 OK",  "result": ["code":returnCode,"description":"logs"]  | Modify a method of advanced notification (Notifier module) .|
| `modifyNotifierRule` | POST | [**rule_name, rule_type, new_rule_name=NULL, change_type=NULL, rule_timeperiod=NULL,  add_rule_method=NULL, delete_rule_method=NULL, rule_contact=NULL, rule_debug=NULL, rule_host=NULL, rule_service=NULL, rule_state=NULL, rule_notificationNumber=NULL,rule_tracking=NULL**] | "http_code": "200 OK",  "result": ["code":returnCode,"description":"logs"]  | Modify a rule of advanced notification menu (Notifier module) .|
| `deleteContact` | POST | [**contactName**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]  | delete the given contact |
| `deleteHostDowntime` | POST | [**idDowntime**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]  | Delete nagios host downtime. |
| `deleteServiceDowntime` | POST | [**idDowntime**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]  | Delete nagios service downtime. |
| `deleteContactGroup` | POST | [**contactGroupName**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]  | delete the given contact Group  |
| `deleteService` | POST | [**serviceName, hostName**] | "http_code": "200 OK",  "result": ["code":returnCode,"description":"logs"]  | delete the given service  |
| `deleteServiceByHostTemplate` | POST | [**serviceName, hostTemplateName**] | "http_code": "200 OK",  "result": ["code":returnCode,"description":"logs"]  | delete the given service  |
| `deleteServiceTemplate` | POST | [**templateName**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]  | Delete the given Service template |
| `deleteCommand` | POST | [**commandName**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]  | Delete a command to Nagios. |
| `deleteHost` | POST | [**hostName, exportConfiguration**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]  | Delete a nagios host. |
| `deleteContactGroupToContact` | POST | [**contactName, contactGroupName, exportConfiguration**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]  | delete a contact group to a nagios contact. |
| `deleteContactNotificationCommandToContact` | POST | [**contactName, commandName, exportConfiguration**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]  | delete a contact notification command to a nagios contact. |
| `deleteServiceGroupToServiceInHost` | POST | [**serviceGroupName, serviceName, hostName, exportConfiguration = FALSE**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]  | delete a service group in the given service of the specified host. |
| `deleteContactToServiceInHost` | POST | [**contactName, serviceName, hostName**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]  | Delete a contact in the given service of the specified host. |
| `deleteContactGroupToServiceInHost` | POST | [**contactGroupName, serviceName, hostName**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]  | Delete a contact group in the given service of the specified host. |
| `deleteServiceTemplateToServiceInHost` | POST | [**templateServiceName, serviceName, hostName**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]  | Delete a service template in the given service of the specified host. |
| `deleteHostGroupToHostTemplate` | POST | [**hostGroupName, templateHostName, exportConfiguration=FALSE**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]  | Delete a Host group in the given Host Template. returncode=0 or 1 if failed |
| `deleteContactToHostTemplate` | POST | [**contactName, templateHostName, exportConfiguration=FALSE**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]  | Delete a Contact in the given Host Template. returncode=0 or 1 if failed |
| `deleteContactGroupToHostTemplate` | POST | [**contactGroupName, templateHostName, exportConfiguration=FALSE**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]  | Delete a Contact group in the given Host Template. returncode=0 or 1 if failed |
| `deleteInheritanceTemplateToHostTemplate` | POST | [**inheritanceTemplateName, templateHostName, exportConfiguration=FALSE**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]  | Delete a inherited template in the given Host Template. returncode=0 or 1 if failed |
| `deleteInheritServiceTemplateToServiceTemplate` | POST | [**inheritanceTemplateName, templateServiceName, exportConfiguration=FALSE**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]  | Delete a inherited Service template in the given service Template. returncode=0 or 1 if failed|
| `deleteContactGroupToServiceTemplate` | POST | [**contactGroupName, templateServiceName, exportConfiguration=FALSE**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]  | Delete a contact group in the given service Template. returncode=0 or 1 if failed|
| `deleteContactToServiceTemplate` | POST | [**contactName, templateServiceName, exportConfiguration=FALSE**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]  | Delete a contact in the given service Template. returncode=0 or 1 if failed|
| `deleteServiceGroupToServiceTemplate` | POST | [**serviceGroupName, templateServiceName, exportConfiguration=FALSE**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]  | Delete a service group in the given service Template. returncode=0 or 1 if failed|
| `deleteCustomArgumentsToService` | POST | [**serviceName,hostName,customArguments, exportConfiguration = FALSE**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"] | delete customs arguments to a service. returncode=0 or 1 if failed or didn't changed |
| `deleteCustomArgumentsToServiceTemplate` | POST | [**templateServiceName,customArguments, exportConfiguration = FALSE**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"] | delete customs arguments to a service template. returncode=0 or 1 if failed or didn't changed |
| `deleteCustomArgumentsToHostTemplate` | POST | [**templateHostName,customArguments, exportConfiguration = FALSE**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"] | delete customs arguments to a host template. returncode=0 or 1 if failed or didn't changed |
| `deleteCustomArgumentsToHost` | POST | [**hostName,customArguments, exportConfiguration = FALSE**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"] | delete customs arguments to a host. returncode=0 or 1 if failed or didn't changed |
|`deleteCheckCommandParameterToServiceTemplate` | POST | [**templateServiceName, parameters**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"] | delete command parameter to a Service template. returncode=0 or 1 if failed or didn't changed /!\parameters is a list|
|`deleteCheckCommandParameterToServiceInHost` | POST | [**serviceName, hostName, parameters**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"] | delete command parameter to a Service of a specified host. returncode=0 or 1 if failed or didn't changed /!\ parameters is a list|
|`deleteCheckCommandParameterToHostTemplate` | POST | [**templateHostName, parameters**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"] | delete command parameter to host template. returncode=0 or 1 if failed or didn't changed /!\ parameters is a list|
|`exporterNotifierConfig` | POST | [****] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"] | Write configuration in the nagios file. It export the configuration of advance notification.|



## EONAPI calls examples
To illustrate the EON API features tab, you will find a few implementation examples (JSON body parameters):

* /createHost
```json 
{
	"templateHostName": "TEMPLATE_HOST",
	"hostName": "HostName",
	"hostIp": "8.8.8.8",
	"hostAlias": "My first host",
	"contactName": "usertest",
	"contactGroupName": null,
	"exportConfiguration": true
}
```

* /createEonGroup
```json 
{
  "group_name":"sous-chef",
  "group_descr":"lieutenant",
  "group_right": {
      "dashboard":1,
      "disponibility":1,
      "capacity":1,
      "production":1,
      "reports":1,
      "administration":0,
      "help":1
    }
}
```

* /createUser
```json 
{
	"userName": "bob",
	"userMail": "bob@marley.com",
	"admin": true,
	"filterName": "hostgroups",
	"filterValue": "HOSTGROUP_JAMAICA",
	"exportConfiguration": true
}
```

* /createContact
```json 
{
  "contactName": "bob",
  "contactMail": "bob@eon.fr",
  "contactPager": "bob_pager@eon.fr",
  "contactGroup": "admins",
  "options":{
    "host_notification_period": "24x7",
    "host_notification_options_down": 1,
    "can_submit_commands":1
  }
}
```

* /addNotifierMethod
```json
{
  "method_name":"test",
  "method_type":"host",
  "method_line":"ll -a"
}
```

* /addContactToHost
```json 
{
	"contactName": "bob",
	"hostName": "HostName",
	"exportConfiguration": true
}
```

* /addContactGroupToHost
```json 
{
	"contactGroupName": "admins",
	"hostName": "HostName",
	"exportConfiguration": true
}
```

* /createHostTemplate
```json 
{
	"templateHostName": "TEMPLATE_HOST",
	"exportConfiguration": true
}
```

* /addHostTemplateToHost
```json 
{
	"templateHostName": "TEMPLATE_HOST",
	"hostName": "HostName",
	"exportConfiguration": true
}
```

* /deleteCustomArgumentsToService
```json 
{
  "serviceName":"toto",
  "hostName":"DUMMY_HOST",
  "customArguments":{
    					"toto":"123",
                      	"titi":"321"
  					}
}
```

* /addContactToHostTemplate
```json 
{
	"contactName": "bob",
	"templateHostName": "TEMPLATE_HOST",
	"exportConfiguration": true
}
```

* /addContactGroupToHostTemplate
```json 
{
	"contactGroupName": "admins",
	"templateHostName": "TEMPLATE_HOST",
	"exportConfiguration": true
}
```
* /createServiceTemplate
```json 
{
  "templateName":"foe",
  "templateDescription":"test description ",
  "checkCommand":"check_ping",
  "checkCommandParameters":["arg1","arg2"]
}
```

* /modifyCommand `/!\ newCommandName and commandDescription are not required`
```json 
{
	"commandName": "foe",
	"newCommandName":"doe",
	"commandLine": "$USER1$/foe.py -H $ARG1$",
	"commandDescription":"Do something great"
}
```

* /modifyServiceFromHost
```json 
{
	"hostName":"doe",
	"service":{
		"name":"Foe",
	  "inheritance":"EMC", 
    "command":"check_ftp",
    "parameters":["toto","titi"] 
  	}
}
```

* /modifyNagiosMainConfiguration
```json
{
  "requestConf":{
    "hostEventHandler":"check_ping",	  //optinal
    "hostEventHandler":"",				      //optinal
    "serviceEventHandler":"",			      //optinal
    "hostPerfdata":"",					        //optinal
    "servicePerfdata":"",				        //optinal
    "hostPerfdataFileProcessing":"",	  //optinal
    "servicePerfdataFileProcessing":"" 	//optinal
    }
}
```

* /modifyNotifierTimeperiod
```json
{
  "timeperiod_name":"24/7",
  "new_timeperiod_name":"24/24x7/7",                                                    //optional
  "timeperiod_days":"mon,tue,fri,wed,sun", //* || ["mon", "wed", ...]                   //optional
  "timeperiod_hours_notifications":"*" //0000-0100,1030-1230,... || ["0000-0100",...]   //optional
}

* /modifyNotifierMethod
```json
{
  "method_name":"email_host",
  "new_method_name":"email_service",  //optional
  "method_type":"host",
  "change_type":"service",            //optional
  "method_line":"send('NOTIF')"       //optional
}

* /modifyNotifierRule
```json
{
  "rule_name":"test(24x7)", 
  "rule_type":"service", 
  "new_rule_name":"regle24/24", 
  "change_type":"host", 
  "rule_timeperiod":"48/8", 
  "add_rule_method":["email-host"], 
  "rule_contact":"admin", 
  "rule_debug":0, 
  "rule_host":"localhost", 
  "rule_service":"ssh", 
  "rule_state":["UP"],
  "rule_notificationNumber":1,
  "rule_tracking":0
}
```

* /createServiceToHost
```json 
{
  "hostName":"localhost",
  "service": {
    "name":"Foe",
	  "inheritance":"EMC", //optinal and the template have to exist
    "command":"check_ftp", //optional and the command have to exist
    "parameters":["toto","titi"] //optional
  }
}
```

* /modifyNagiosRessources
```json 
{
  "ressources":{
    "User17":"12",
    "User21":"admin",
    "User18":"",
    "User19":"",
    "User20":"/root"
  }
}
```
* /addCheckCommandParameterToServiceTemplate
```json
{
  "templateServiceName": "DUMMY_TEMPLATE",
  "parameters": ["titi","toto"]
}
```

**NB:** You should notice the optional parameter `exportConfiguration` (boolean true or false) that allows the nagios configuration export. An API call doesn't need systematically a nagios configuration reload. That's why you should set this parameter depending your needs.

## Add EONAPI features: How to do this?
The EON API is an open source project. You can obviously add features to fit your needs. Do not hesitate to share your version with the EON community.

The REST API is mainly based on function calls. The functions are defined in the [ObjectManager.php](include/ObjectManager.php) file. To make these functions available remotely (http calls via token), we declare the ObjectManager function needed in [index.php](html/api/index.php) by adding a route.

A "framework" has been developped in order to add routes very easily.
The function `addRoute($httpMethod, $routeName, $methodName)` allow you to generate the route and function automatically, based on the ObjectManager method.

Example:
```php
#index.php
addRoute('post', '/createHost', 'createHost' );
```
**NB:**
The `$methodName` parameter is the Action URL (route call) defined in the [features array](#eonapi-features). It must have the same name as the method defined in [ObjectManager.php](include/ObjectManager.php).

## Security and Encryption
If you are accessing the API inside your secure LAN you can simply use HTTP. In insecure environments (e.g. when accessing your EON server across the Internet) you should use HTTPS requests to make sure that your parameters and passwords are encrypted. This way all communication between the EON server and your client is encrypted by SSL encryption.

## Versioning
Most JSON replies from the API contain a **"api_version"** field that contains the api version on the EON server. Your applications developers should take note of this version for compatibility reasons.

## Error Handling
Each response to an API call contains a status code. These status codes have a meaning and are referenced in the table below:

| Status Code | Meaning | Comments |
| --- | --- | --- |
| `200` | OK | The API call was completed successfully, the JSON response contains the result data. |
| `400` | Bad Request | The API call could not be completed successfully. The XML response contains the error message. |
| `401` | Unauthorized | The username/password or username/apiKey credentials of your authentication can not be accepted. |

## About the EONAPI
The EON API is built with Slim Framework.

## About Slim Framework
Slim is a PHP micro framework that helps you quickly write simple yet powerful web applications and APIs.
Slim Framework source sode https://www.slimframework.com/.

**Slim version:** `2.4.2`

**Dependenties:**
`PHP >= 5.3.0`

**Compatibility matrix:**

| Version | Comments |
| --- | --- |
| `PHP 5.3` | Tested |
| `PHP 5.4` | Tested |
| `PHP 5.5` | Tested |
| `PHP > 5.5` | Not tested |

## License
* Eyes Of Network is licensed under the GNU General Public License.
* The Slim Framework is licensed under the MIT license.
