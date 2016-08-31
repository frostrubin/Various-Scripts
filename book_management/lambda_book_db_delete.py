from __future__ import print_function

import json
import boto3
import StringIO

sdb = boto3.client('sdb')
s3  = boto3.client('s3')

def lambda_handler(event, context):
    #print("Received event: " + json.dumps(event, indent=2))
    key = event['Records'][0]['s3']['object']['key']
    if not (key.lower().endswith('.pdf') or key.lower().endswith('.epub')):
        return('Not a book file')
            
    try:
        for semaphore in ['semaphores/1.txt', 'semaphores/2.txt']:
            s3.put_object(Key=semaphore,ServerSideEncryption='AES256',
                Bucket='bookbucket',Body=StringIO.StringIO('del '+key))
    except Exception as e:
        print(e)
        print('Could not create Semaphores')

    try:
        response = sdb.delete_attributes(
            DomainName='SimpleBooksDB',
            ItemName=key
        )
        return 'Deleted ' + key
    except Exception as e:
        print(e)
        print('Could not delete DB Record for ' + key)
        raise e