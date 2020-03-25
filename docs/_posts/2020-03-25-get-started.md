---
title: 'Get Started'

layout: default
---

## Installation steps through git: 
1. Clone the project EONAPI:
```$ git clone https://github.com/eyesofnetworkcommunity/eonapi.git
```

2. Make the symbolic links in your project:
```$ ln -sf /srv/eyesofnetwork/eonapi-git/ /srv/eyesofnetwork/eonapi
```

3. Edit the eonapi httpd conf file:
```$ vim /etc/httpd/conf.d/eonapi.conf```

    ```Alias /eonapi "/srv/eyesofnetwork/eonapi/html/api"
    <Directory /srv/eyesofnetwork/eonapi/html/api>
        Options -Indexes
        Require all granted
        FallbackResource index.php
    </Directory>```

4. Restart the httpd daemon:
```$ service httpd restart```

## Installation through RPM:

