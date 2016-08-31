#!/usr/bin/env python

import json
import sys
import codecs
import subprocess
import time

# Proper UTF8 Output for print
sys.stdout = codecs.getwriter('utf8')(sys.stdout)
sys.stderr = codecs.getwriter('utf8')(sys.stderr)

# Get List of Files
command = ['aws','s3api','list-objects','--bucket','bookbucket','--prefix','bookfolder','--encoding-type','url','>','/tmp/filelist.json']
subprocess.call(command)
f = open('/tmp/filelist.json', 'r')
text = f.read()
#book_array = eval(json.loads(text))
return_array = json.loads(text)
book_array = return_array['Contents']

command = ['aws', 'lambda', 'invoke', '--function-name', 'LambdaBookDBFromS3Insert', '--invocation-type', 'Event', '--payload', 'fileb:///tmp/trigger_event_in.json', '/tmp/dummy.txt']

for book in book_array:
	# if not book.endswith('.pdf'):
	# 	continue

	event_text = {'Records':[{'s3':{'object':{'key':book['Key']},'bucket':{'arn':'arn:aws:s3:::bookbucket','name':'bookbucket'}}}]}
	event_dump = json.dumps(event_text)
	with open('/tmp/trigger_event_in.json', "w") as text_file:
	    text_file.write(event_dump)
	print(book['Key'])
	subprocess.call(command)
	time.sleep(10)
