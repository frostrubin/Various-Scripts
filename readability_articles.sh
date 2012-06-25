#!/bin/bash

consumer_key="yourusername"
consumer_secret=""
username="yourusername"
password="passw0rd"

xauth_endpoint="https://www.readability.com/api/rest/v1/oauth/access_token/"

if [ -n "$2" ] || [ -z "$1" ]; then
  echo 'usage: readability_articles.sh {init|dostuff}'; exit 1
fi

if [ "$1" == "init" ]; then
  timestamp=$(date "+%s")
  nonce=$(echo $RANDOM$RANDOM|base64|md5|base64)
  sig=$(echo "$consumer_secret""%26")

  response=$(curl -s --request POST -H "content-type: application/x-www-form-urlencoded" \
      "$xauth_endpoint" \
      -d oauth_consumer_key="$consumer_key" \
      -d oauth_consumer_secret="$consumer_secret" \
      -d oauth_timestamp="$timestamp" \
      -d oauth_nonce="$nonce" \
      -d oauth_signature_method="PLAINTEXT" \
      -d oauth_signature="$sig" \
      -d x_auth_username="$username" \
      -d x_auth_password="$password" \
      -d x_auth_mode="client_mode")

  oauth_token_secret=$(echo "$response"|sed -e 's,&oauth_token=,\\\n,')
  oauth_token_secret=$(echo -e "$oauth_token_secret"|sed -e 's,oauth_token_secret=,,')
  oauth_token=$(echo "$oauth_token_secret"|tail -n 1|sed -e 's,&oauth_callback_confirmed=true,,')
  oauth_token_secret=$(echo "$oauth_token_secret"|head -n 1)

  security add-generic-password -a "OAuthToken" -s "ReadabilityToEPUB" -w "$oauth_token" -T "" -U
  security add-generic-password -a "OAuthTokenSecret" -s "ReadabilityToEPUB" -w "$oauth_token_secret" -T "" -U
fi
  
if [ "$1" == "dostuff" ];then
  oauth_token=$(security 2>&1 >/dev/null find-generic-password -gs "ReadabilityToEPUB" -a "OAuthToken"| cut -d '"' -f 2 )
  oauth_token_secret=$(security 2>&1 >/dev/null find-generic-password -gs "ReadabilityToEPUB" -a "OAuthTokenSecret"| cut -d '"' -f 2 )
  
  nonce=$(echo $RANDOM$RANDOM|base64|md5|base64)
  timestamp=$(date "+%s")
  sig=$(echo "$consumer_secret""%26""$oauth_token_secret")

  curl -s --request GET -H "Authorization: OAuth oauth_version=\"1.0\", oauth_consumer_key=$consumer_key, oauth_timestamp=$timestamp, oauth_nonce=$nonce, oauth_token=$oauth_token, oauth_signature_method=\"PLAINTEXT\", oauth_signature=$sig" "https://www.readability.com/api/rest/v1/bookmarks?format=xml&archive=0"

  number_of_entries=$(echo "$xml"|xpath "count(/response/bookmarks/resource/article)" 2>/dev/null)
  titles=$(echo "$xml"|xpath /response/bookmarks/resource/article/title 2>/dev/null)
  titles=$(echo "$titles"|sed -e 's,</title><title>,\\\n,g')
  titles=$(echo -e "$titles"|sed -e 's,<title>,,;s,</title>,,')

 # echo "$titles"

 # echo $number_of_entries
  COUNTER=1
  while [  $COUNTER -le 1 ];do
  	  #"$number_of_entries" ]; do
    title=$(echo "$xml"|xpath "(//response/bookmarks/resource/article)[$COUNTER]/title" 2>/dev/null|sed -e 's,<title>,,'|sed 's/\(.*\)<\/title>/\1/')
    author=$(echo "$xml"|xpath "(//response/bookmarks/resource/article)[$COUNTER]/author" 2>/dev/null|sed -e 's,<author>,,'|sed 's/\(.*\)<\/author>/\1/')
    word_count=$(echo "$xml"|xpath "(//response/bookmarks/resource/article)[$COUNTER]/word_count" 2>/dev/null|sed -e 's,<word_count>,,'|sed 's/\(.*\)<\/word_count>/\1/')
    date_published=$(echo "$xml"|xpath "(//response/bookmarks/resource/article)[$COUNTER]/date_published" 2>/dev/null|sed -e 's,<date_published>,,'|sed 's/\(.*\)<\/date_published>/\1/')
    article_href=$(echo "$xml"|xpath "(//response/bookmarks/resource)[$COUNTER]/article_href" 2>/dev/null|sed -e 's,<article_href>,,'|sed 's/\(.*\)<\/article_href>/\1/')
    
   

    article_xml=$(curl -s --request GET -H "Authorization: OAuth oauth_version=\"1.0\", oauth_consumer_key=$consumer_key, oauth_timestamp=$timestamp, oauth_nonce=$nonce, oauth_token=$oauth_token, oauth_signature_method=\"PLAINTEXT\", oauth_signature=$sig" "https://www.readability.com$article_href?format=xml")
    content=$(echo "$article_xml"|xpath //response/content 2>/dev/null|sed -e 's,<content>,,'|sed 's/\(.*\)<\/content>/\1/'|sed -e 's,&lt;,<,g'|sed -e "s,',\\\',g;")
    #sed -e "s,',\\\',g;"|sed -e 's,",\\\",g')
    #content="$content""\n"
    #content=$(php -r "echo html_entity_decode( \'$content\' , ENT_QUOTES, 'UTF-8');")
    echo "$content"
    #echo "$article_xml"

    let COUNTER=COUNTER+1 
  done
fi
  
