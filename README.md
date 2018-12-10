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
| `getCommand` | POST | [**commandName**] | "http_code": "200 OK", "result": [with the executed actions] | Return the informations of a command |
| `getHost` | POST | [**hostName**] | "http_code": "200 OK", "result": [with the executed actions] | return the given host |
| `getHostsByTemplate` | POST | [**templateHostName**] | "http_code": "200 OK", "result": [with the executed actions] | return hosts link with the given template host |
| `getHostsByHostGroup` | POST | [**hostGroupName**] | "http_code": "200 OK", "result": [with the executed actions] | return hosts link with the given hostgroup |
| `getServicesByHost` | POST | [**hostName**] | "http_code": "200 OK", "result": [with the executed actions] | return services link with the given host|
| `getService` | POST | [**serviceName, hostName**] | "http_code": "200 OK", "result": [with the executed actions] | return the service link with the given host|
| `getServicesByHostTemplate` | POST | [**templateHostName**] | "http_code": "200 OK", "result": [with the executed actions] | return services link with the given host template|
| `getContact` | POST | [**contactName=FALSE**] | "http_code": "200 OK", "result": [with the executed actions] | return the given contact otherwise it return all the contact|
| `getContactGroup` | POST | [**contactGroupName=FALSE**] | "http_code": "200 OK", "result": [with the executed actions] | return the given contact group otherwise it return all the contac group|
| `createHost` | POST | [**templateHostName, hostName, hostIp, hostAlias, contactName, contactGroupName, exportConfiguration**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]  | Create a nagios host (affected to the provided parent template [templateHostName]) if not exists and reload lilac configuration. Posibility to attach a contact and/or a contact group to the host in the same time. |
| `createService` | POST | [**hostName, services, host = NULL, checkCommand=NULL, exportConfiguration**] The parameter **services** is an array with the service(s) name as a key, the service template as first parameter, and the following optional service arguments linked to the service template. | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]  | Add a new service(s) to an existant host and reload lilac configuration. To add a service, please see the parameters column. It will add a service to a specified nagios host with as many service arguments as needed. |
| `createUser` | POST | [**userName, userMail, admin, filterName, filterValue, exportConfiguration**] | "http_code": "200 OK", "result": [with the executed actions] | Create a nagios contact and a eon user. The user could be limited or admin (depends on the parameter "admin"). Limited user: admin=false / admin user: admin=true. For a limited user, the GED xml file is created in /srv/eyesofnetwork/eonweb/cache/ with the filters specified in parameters. |
| `createHostTemplate` | POST | [**templateHostName, templateHostDescription="",exportConfiguration**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]  | Create a new nagios host template. |
| `createHostGroup` | POST | [**hostGroupName, &error = "", &success = "", exportConfiguration = FALSE**] | "http_code": "200 OK", "result": [with the executed actions] | Create a Host Group |
| `createServiceTemplate` | POST | [**templateName, templateDescription="", servicesGroup=array(), contacts=array(), contactsGroup=array(), checkCommand, checkCommandParameters=array(), templatesToInherit=array(), exportConfiguration = FALSE**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]  | Create a new Service template, if you didn't give templatesToInherit it will provide "GENERIC_SERVICE" as Inheritance template. The argument witch is by default array take names of objects they are bind. |
| `addContactToHost` | POST | [**contactName, hostName, exportConfiguration**] | "http_code": "200 OK", "result": [with the executed actions] | Attach a nagios contact to a host if not already attached. |
| `addContactGroupToHost` | POST | [**contactGroupName, hostName, exportConfiguration**] | "http_code": "200 OK", "result": [with the executed actions] | Attach a nagios contact group to a host if not already attached. |
| `addHostTemplateToHost` | POST | [**templateHostName, hostName, exportConfiguration**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"] | Add a host template to a nagios host. |
| `addContactToHostTemplate` | POST | [**contactName, templateHostName, exportConfiguration**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]  | Add a contact to a nagios host template. |
| `addServiceTemplateFromService` | POST | [**serviceTemplateName, serviceName, hostName**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]  | Add a service template in the given service of the specified host. |
| `addContactGroupToHostTemplate` | POST | [**contactGroupName, templateHostName, exportConfiguration**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]  | Add a contact group to a nagios host template. |
| `addCommand` | POST | [**commandName,commandLine,commandDescription=""**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"] | Add a command to Nagios.returncode=0 or 1 if failed |
| `addHostGroupToHostTemplate` | POST | [**hostGroupName,templateHostName,exportConfiguration = FALSE**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"] | Add a Host group to a host template. returncode=0 or 1 if failed |
| `addInheritanceTemplateToHostTemplate` | POST | [**inheritanceTemplateName,templateHostName,exportConfiguration = FALSE**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"] | Add a Inherit host template to a host template. returncode=0 or 1 if failed |
| `addServiceGroupeToServiceTemplate` | POST | [**serviceGroupName,templateServiceName,exportConfiguration = FALSE**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"] | Add a service group to a service template. returncode=0 or 1 if failed |
| `addContactToServiceTemplate` | POST | [**contactName,templateServiceName,exportConfiguration = FALSE**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"] | Add a contact to a service template. returncode=0 or 1 if failed |
| `addContactGroupToServiceTemplate` | POST | [**contactGroupName,templateServiceName,exportConfiguration = FALSE**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"] | Add a contact group to a service template. returncode=0 or 1 if failed |
| `addInheritServiceTemplateToServiceTemplate` | POST | [**inheritServiceTemplateName,templateServiceName,exportConfiguration = FALSE**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"] | Add a inherited service template to a service template. returncode=0 or 1 if failed |
| `exportConfiguration` | POST | [**JobName**] | "http_code": "200 OK", "result": [with the executed actions] | Export Nagios Configuration. |
| `listHosts` | POST | [**hostName=FALSE, $hostTemplate=false**] | "http_code": "200 OK", "result": [with the executed actions] | List nagios hosts |
| `checkHost` | POST | [**type, adress, port, path**] | "http_code": "200 OK", "result": [with the executed actions] | Check an particulary host if it's available|
| `listNagiosBackends` | POST | [] | "http_code": "200 OK", "result": [with the executed actions] | Return available backend informations(log) |
| `listNagiosObjects` | POST | [**object, backendid = NULL, columns = FALSE, filters = FALSE**] | "http_code": "200 OK", "result": [with the executed actions] | Return nagios object like services, hosts, and their respective informations on which you can filter |
| `listNagiosStates` | POST | [**backendid = NULL, filters = FALSE**] | "http_code": "200 OK", "result": [with the executed actions] | Return states of hosts and services  |
| `modifyService` | POST | [**serviceName, hostName, column=array(), exportConfiguration = FALSE**] | "http_code": "200 OK",  "result": ["code":returnCode,"description":"logs"]  | modify the given service with the given columnName => value (ie bellow)|
| `modifyCommand` | POST | [**commandName,newCommandName="",commandLine,commandDescription=""**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs","changes":numerOfchanges] | modify a command to Nagios. returncode=0 or 1 if failed or nothing change |
| `modifyNagiosRessources` | POST | [**ressources**] | "http_code": "200 OK",  "result": ["code":returnCode,"description":"logs"]  | modify ressources represented in nagios by $USERi$, you passed an collection of "Useri":"value" or "" if you want to remove a ressource an example is given bellow |
| `deleteContact` | POST | [**contactName**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]  | delete the given contact |
| `deleteContactGroup` | POST | [**contactGroupName**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]  | delete the given contact Group  |
| `deleteService` | POST | [**serviceName, hostName**] | "http_code": "200 OK",  "result": ["code":returnCode,"description":"logs"]  | delete the given service  |
| `deleteServiceTemplate` | POST | [**templateName**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]  | Delete the given Service template |
| `deleteCommand` | POST | [**commandName**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]  | Delete a command to Nagios. |
| `deleteHost` | POST | [**hostName, exportConfiguration**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]  | Delete a nagios host. |
| `deleteServiceTemplateFromService` | POST | [**serviceTemplateName, serviceName, hostName**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]  | Delete a service template in the given service of the specified host. |
| `deleteHostGroupToHostTemplate` | POST | [**hostGroupName, templateHostName, exportConfiguration=FALSE**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]  | Delete a Host group in the given Host Template. returncode=0 or 1 if failed |
| `deleteContactToHostTemplate` | POST | [**contactName, templateHostName, exportConfiguration=FALSE**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]  | Delete a Contact in the given Host Template. returncode=0 or 1 if failed |
| `deleteContactGroupToHostTemplate` | POST | [**contactGroupName, templateHostName, exportConfiguration=FALSE**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]  | Delete a Contact group in the given Host Template. returncode=0 or 1 if failed |
| `deleteInheritanceTemplateToHostTemplate` | POST | [**inheritanceTemplateName, templateHostName, exportConfiguration=FALSE**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]  | Delete a inherited template in the given Host Template. returncode=0 or 1 if failed |
| `deleteInheritanceTemplateToServiceTemplate` | POST | [**inheritanceTemplateName, templateServiceName, exportConfiguration=FALSE**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]  | Delete a inherited Service template in the given service Template. returncode=0 or 1 if failed|
| `deleteContactGroupToServiceTemplate` | POST | [**contactGroupName, templateServiceName, exportConfiguration=FALSE**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]  | Delete a contact group in the given service Template. returncode=0 or 1 if failed|
| `deleteContactToServiceTemplate` | POST | [**contactName, templateServiceName, exportConfiguration=FALSE**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]  | Delete a contact in the given service Template. returncode=0 or 1 if failed|
| `deleteServiceGroupToServiceTemplate` | POST | [**serviceGroupName, templateServiceName, exportConfiguration=FALSE**] | "http_code": "200 OK", "result": ["code":returnCode,"description":"logs"]  | Delete a service group in the given service Template. returncode=0 or 1 if failed|



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

* /createService
```json 
{
	"hostName": "HostName",
	"services": {
                "Service1": [
                    "TEMPLATE_SERVICE_1",
                    "127.0.0.1",
                    "eth0",
                    "1000000",
                    "100",
                    "110"
                ],
                "Service2": [
                    "TEMPLATE_SERVICE_2",
                    "3000",
                    "80",
                    "5000",
                    "90"
                ]
        },
	"exportConfiguration": true
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

* /modifyService 
```json 
{
	"serviceName": "foe",
	"hostName":"doe",
	"columns":{
		"DisplayName":"erased",
		"CheckCommand":"check_api_eon",
		"IsVolatile":"enable"
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
