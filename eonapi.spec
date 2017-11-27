Summary:        API for the EON suite.
Name:           eonapi
Version:        1.0
Release:        1.eon
Source:         %{name}-%{version}.tar.gz
Group:          Applications/System
License:        GPL
Vendor:         EyesOfNetwork Community
URL:            https://github.com/EyesOfNetworkCommunity/eonapi

BuildRoot:      %{_tmppath}/%{name}-%{version}-root

%define eondir          /srv/eyesofnetwork
%define	datadir		%{eondir}/%{name}-%{version}
%define linkdir         %{eondir}/%{name}
%define eonconfdir      /etc/httpd/conf.d

%description
Eyes Of Network includes a web-based "RESTful" API (Application Programming Interface) called EONAPI that enables external programs to access information from the monitoring database and to manipulate objects inside the databases of EON suite.


%prep
%setup -q

%build

%install
install -d -m0755 %{buildroot}%{datadir}
chmod -v 640 ./eonapi.conf
mv -v ./eonapi.conf %{eonconfdir}
cp -afv ./* %{buildroot}%{datadir}
systemctl restart httpd

%post
ln -nsf %{datadir} %{linkdir}
/bin/chown -R root:eyesofnetwork %{eondir}/%{name}-%{version}
/bin/chown -h root:eyesofnetwork %{linkdir}

%clean
rm -rf %{buildroot}

%files
%{eondir}

%changelog
* Tue Oct 26 2017 Michael Aubertin <michael.aubertin@gmail.com> - 1.0-1
- Fix permission issue.

* Wed Oct 25 2017 Lucas Salinas - 1.0-0
-Package for EyesOfNetwork API.
