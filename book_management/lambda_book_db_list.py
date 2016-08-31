from __future__ import print_function

import json
import boto3 
import urllib 
import StringIO 

s3  = boto3.resource('s3')
sdb = boto3.client('sdb')

def lambda_handler(event, context):
    run = False
    try:
        if event['force']:
            run = True
    except KeyError:
        pass
    
    semaphores = {}
    if not run:
        try:
            semaphores = boto3.client('s3').list_objects_v2(Bucket='bookbucket', Prefix='semaphores/')
        except Exception as e:
            print(e)
            print('Could not read semaphores')
        
    try:
        for semaphore in semaphores['Contents']:
            if semaphore['Key'].endswith('.txt'):
                run = True
                break
    except KeyError:
        pass
    
    if not run:
        return('No reason to run. Shutting down.')
    
    books = {}
    library = []
    nextToken = '' 
    while True: 
        response = sdb.select( 
            SelectExpression='select * from SimpleBooksDB', 
            NextToken=nextToken, ConsistentRead=True) 
        for item in response['Items']:
            filename = urllib.unquote_plus(item['Name'])
            book = {'filename':filename} 
            for attr in item['Attributes']:
                book[attr['Name']] = urllib.unquote_plus(attr['Value'])

            books[filename] = book 
        try: 
            nextToken = response['NextToken'] 
        except KeyError: 
            response = [] 
            break 
 
    try: 
        for obj in s3.Bucket('bookbucket').objects.all():
            key_low = obj.key.lower()
            if key_low.endswith('.pdf') or key_low.endswith('.epub'):
                if obj.key in books:
                    library.append(books[obj.key])
                else:
                    newbook = {'filename':obj.key}
                    for attr in ['title', 'author', 'author_sort', 'genre']:
                        newbook[attr] = ''
                    library.append(newbook)
                    
        books = {} 
    except Exception as e: 
        print('Error listing files from bookbucket') 
        raise e 
        
    try: 
        s3.Object('bookbucket','books/Library_Overview.json').put(
            Body=StringIO.StringIO(json.dumps(library)), 
            ServerSideEncryption='AES256') 
    except Exception as e: 
        print('Error putting file to S3') 
        raise e             
            
    try:
        for semaphore in semaphores['Contents']:
            if semaphore['Key'].endswith('.txt'):
                s3.Object('bookbucket',semaphore['Key']).delete()
                print('Deleted ' + semaphore['Key'])
                break
    except KeyError as e:
        pass
    except Exception as e: 
        print('Error deleting a semaphore') 
        raise e  
        
    return(library) 