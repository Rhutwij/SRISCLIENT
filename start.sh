#!/bin/bash
sudo  /usr/local/mysql/support-files/mysql.server start
sudo apachectl start
#bin/solr start -e techproducts -Dsolr.clustering.enabled=true
/Users/rhutwijtulankar/SOLR4.8/solr-4.10.4/bin/solr  start -p 8983
