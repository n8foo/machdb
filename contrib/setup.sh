#! /bin/sh

mkdir /var/machdb/ -p && echo "Directory /var/machdb created"

echo "Installing LWP"
#perl -MCPAN -e 'install "LWP"'
yum install -y perl-libwww-perl
echo "Installing XML::Simple"
yum install -y perl-XML-SAX perl-XML-NamespaceSupport.noarch
#perl -MCPAN -e 'install "XML::Simple"'
#rpm -Uvh ftp://rpmfind.net/linux/dag/redhat/el4/en/x86_64/dag/RPMS/perl-XML-Simple-2.13-2.2.el4.rf.noarch.rpm
#rpm -Uvh http://dag.wieers.com/rpm/packages/perl-XML-Simple/perl-XML-Simple-2.16-1.el4.rf.noarch.rpm

echo "Setup complete"
