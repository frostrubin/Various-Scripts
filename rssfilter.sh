#!/bin/bash

curl -s http://www.example.com/feed.xml \
 | xsltproc rssfilter.xslt - \
 | sed '/^ *$/d' > /tmp/filteredfeed.rss
