#!/bin/bash
sudo  /usr/local/mysql/support-files/mysql.server stop
sudo apachectl stop
/Users/rhutwijtulankar/SOLR4.8/solr-4.10.4/bin/solr  stop -p 8983
