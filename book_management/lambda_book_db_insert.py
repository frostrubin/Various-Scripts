from __future__ import print_function

import json
import urllib
import boto3
import StringIO
import sys
import codecs
import ntpath
import datetime

sys.stdout = codecs.getwriter('utf8')(sys.stdout)
sys.stderr = codecs.getwriter('utf8')(sys.stderr)
sdb = boto3.client('sdb')
lmb = boto3.client('lambda')
s3  = boto3.client('s3')

def path_leaf(path):
    head, tail = ntpath.split(path)
    return tail or ntpath.basename(head)

def lambda_handler(event, context):
    #print("Received event: " + json.dumps(event, indent=2))
    bucket = event['Records'][0]['s3']['bucket']['name']
    key = event['Records'][0]['s3']['object']['key']
    print(key)
    file = StringIO.StringIO(json.dumps({ 'bucket':bucket, 'file_in': urllib.unquote_plus(key) }))
    try:
        info = {}
        exist = s3.list_objects_v2(Bucket=bucket, Prefix=urllib.unquote_plus(key))
        if not 'Contents' in exist:
            return('Object does not exist ' + key)
            
        if key.lower().endswith('.epub'):
            response = lmb.invoke(
                FunctionName='LambdaCloudCalibreBinaryFunction',
                InvocationType='RequestResponse',
                Payload=file,
            )
            json_dict = json.loads(response['Payload'].read())
            status = json_dict['status']
            if status != 0:
            	raise KeyError('Status was not 0')

            print(json_dict['data'])
            for element in json_dict['data']:
            	lines = filter(None, element.split('\n'))
            	if len(lines) < 2:
            		continue
            	for line in lines:
            		components = line.split(':')
            		#print(components)
            		try:
            		    component = " ".join(components[0].split())
            		    value = " ".join(components[1].split())
            		    for part in components[2:]:
            		        value += ': ' + ' '.join(part.split())
            		    info[component] = value
            		except IndexError:
            		    pass
        elif key.lower().endswith('.pdf'):
            filename = path_leaf(urllib.unquote_plus(key))
            try:
                if len(filename) > 20:
                    datetime.datetime.strptime(filename[0:10], '%Y-%d-%m')
                    filename = filename[17:]
            except ValueError as e:
                pass
            
            info['Title'] = filename[filename.find(" - ")+3:len(filename)-4].strip()
            info['Tags'] = 'PDF File'
            info['Author(s)'] = filename[0:filename.find(" - ")+1] + '[]'
        else:
            return('Not an ebook file')
            
        try:
            for semaphore in ['semaphores/1.txt', 'semaphores/2.txt']:
                s3.put_object(Key=semaphore,ServerSideEncryption='AES256',
                    Bucket='bookbucket',Body=StringIO.StringIO('ins '+key))
        except Exception as e:
            print(e)
            print('Could not create Semaphores')            

            
        authors = info['Author(s)']
        title = info['Title']
        genre = info['Tags']
        author = authors[0:authors.find("[")-1]
        author_sort = authors[authors.find("[")+1:authors.find("]")]
        attributes = []
        for attr in ['title', 'author', 'author_sort', 'genre']:
	        attributes.append({'Name':attr,'Value':urllib.quote_plus(locals()[attr].encode('utf8'))})
        
        for attr in attributes:
         	attr['Replace'] = True
        
        print(attributes)
        response = sdb.put_attributes(
            DomainName='SimpleBooksDB',
            ItemName=key,
            Attributes = attributes
        )

        return("Successfully updated DB");
    except KeyError as e:
        print(e)
        print(json_dict)
        print('Key Error ')
        raise e
    except Exception as e:
        print(e)
        print('Error ')
        raise e