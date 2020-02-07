Summary:        API for the EON suite.
Name:           eonapi
Version:        2.0
Release:        2.eon
Source:         https://github.com/EyesOfNetworkCommunity/%{name}/archive/master.tar.gz#/%{name}-%{version}.tar.gz
Group:          Applications/System
License:        GPL
Vendor:         EyesOfNetwork Community
URL:            https://github.com/EyesOfNetworkCommunity/eonapi
Requires:       eonweb

BuildRoot:      %{_tmppath}/%{name}-%{version}-root

%define eondir          /srv/eyesofnetwork
%define datadir         %{eondir}/%{name}

%description
Eyes Of Network includes a web-based "RESTful" API (Application Programming Interface) called EONAPI that enables external programs to access information from the monitoring database and to manipulate objects inside the databases of EON suite.

%prep
%setup -q -n %{name}-master

%build

%install
install -d -m0755 %{buildroot}%{datadir}
install -d -m0755 %{buildroot}%{_sysconfdir}/httpd/conf.d
cp -afv ./* %{buildroot}%{datadir}
install -m 640 eonapi.conf %{buildroot}%{_sysconfdir}/httpd/conf.d/
rm -rf %{buildroot}%{datadir}/%{name}.spec

%post
echo "[EyesOfNetwork/eonapi] Due to a security vulnerability, the authentication token for the admin user has been regenerated. Please consider replacing the token in your third party tools."
systemctl restart httpd

%clean
rm -rf %{buildroot}

%files
%defattr(-,root,eyesofnetwork)
%{eondir}
%defattr(-,root,root)
%{_sysconfdir}/httpd/conf.d/eonapi.conf

%changelog
* Fri Feb 07 2020 Sebastien DAVOULT <d@vou.lt> - 2.0-2
- FIX modifyNagiosMainConfiguration set value to none (null)
- FIX mysql_real_escape_string() for [username,password,apiKey] variables
- FIX APIKEY is now based on machine-id

* Thu Jul 24 2019 Sebastien DAVOULT <d@vou.lt> - 2.0-1
- FIX manage displayName
- Add "modifyHostTemplate"
- Add "deleteEonUser"
- Add "modifyEonUser"
- Add "createEonUser"
- Add "{create,modify,delete}EonGroup", add new features to manage eonweb group (create/modify/delete) 
- Add "exporterNotifierConfig", add function witch manage the exportation of notifier config 
- Add verification on the function add and modify rules 
- Add modifyNotifier{Rule,Method,Timeperiod}
- Add {add,delete}Notifier{Rule,Method,Timeperiod}
- FIX access class method
- Backend: creation of foundations of the notifier database management (MVC modele)
- Add the management of Global Nagios configuration
- Add "deleteServiceGroup"
- Add "createServiceGroup"
- Add "createContactGroup"

* Mon May 06 2019 Jean-Philippe Levy <jeanphilippe.levy@gmail.com> - 2.0-0
- Update to 2.0

* Thu Jun 14 2018 Jean-Philippe Levy <jeanphilippe.levy@gmail.com> - 1.0-3
- Add addEventBroker and delEventBroker functions

* Sun May 13 2018 Jean-Philippe Levy <jeanphilippe.levy@gmail.com> - 1.0-2
- Fix installation for EyesOfNetwork 5.2.

* Thu Oct 26 2017 Michael Aubertin <michael.aubertin@gmail.com> - 1.0-1
- Fix permission issue.

* Wed Oct 25 2017 Lucas Salinas - 1.0-0
-Package for EyesOfNetwork API.
