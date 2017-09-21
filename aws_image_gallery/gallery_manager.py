import json
import boto3

s3client = boto3.client('s3')
s3res    = boto3.resource('s3')

aliases = [
    {
        "name":"my_first_alias", "bucket": "your_bucket", 
        "prefix": "path/to/a/folder/",
        "thumbMode": "all", # One Thumbnail per Folder vs. Every Image has it's thumbnail
        "thumbHeight": 180, # Height in px
        "authorized_users": [ '1234', '5678' ]
    },
    {
        "name":"another_alias", "bucket": "your_bucket", "thumbMode":"single", "thumbHeight": 200,
        "authorized_users": [ '4321' ]
    }
]

extensions = tuple(['.jpg','.jpeg','.png','.gif'])

thumbnail_bucket = 'thumbnail_bucket'

def does_key_exist(bucket,key):
    try:
        s3client.head_object(Bucket=bucket,Key=key)
    except Exception as e:
        return False
        
    return True

def get_index_link(alias, force_create = False):
    output = {'info':''}
    # Get Pre-Signed URL for an index
    index_key = '{}/{}/IndexGzip.json'.format(alias['bucket'], alias['prefix'])
    index_key = index_key.replace('//','/')
    output['url'] = s3client.generate_presigned_url(
        'get_object', Params={'Bucket': thumbnail_bucket, 'Key': index_key},
        ExpiresIn=15,
    )
   
    # If it does not exist: Create it
    if force_create or not does_key_exist(thumbnail_bucket, index_key):
        output['info'] = 'Created new Index {}'.format(index_key)
        boto3.client('lambda').invoke(
            FunctionName='bucketIndex',
            InvocationType='Event',
            Payload=json.dumps({'bucket':alias['bucket'],
                                'prefix':alias['prefix'],
                                'thumbnail_bucket':thumbnail_bucket,
                                'thumbnail_mode':alias['thumbMode'],
                                'extensions': extensions
            }),
        )
    return output
    
def create_thumbnails(alias):
    # Create Thumbnails
    index_key = '{}/{}/Index.json'.format(alias['bucket'], alias['prefix'])
    index_key = index_key.replace('//','/')    
    ## Get List of Keys
    try:
        resp = s3client.get_object(Bucket=thumbnail_bucket,Key=index_key)
    except Exception as e:
        print(e)
        return ['Could not retrieve Index for Thumbnail Creation']

    gallery_index = json.loads(resp['Body'].read())
    
    thumb_mode = 'single' # 'all'
    try:
        thumb_mode = alias['thumbMode']
    except KeyError:
        pass
    
    thumb_height = 200
    try:
        thumb_height = alias['thumbHeight']
    except KeyError:
        pass    
    
    ## Get List of Existing Thumbnails
    prefix = '{}/{}/'.format(alias['bucket'],alias['prefix'])
    prefix = prefix.replace('//','/')    
    iterator = s3res.Bucket(thumbnail_bucket).objects.filter(Prefix=prefix)
    thumbnails = list(map(lambda obj: obj.key, iterator))
    thumbnails_created = []

    if thumb_mode == 'single':
        for folder in gallery_index['folders']:
            thumb = '{}/{}/thumbnail.png'.format(alias['bucket'],folder)
            thumb = thumb.replace('//','/')  
            if thumb in thumbnails:
                continue
            
            # Still here? Then Thumbnail does not exist yet. 
            # Get First Folder Key
            try:
                key = next(s for s in gallery_index['keys'] if folder in s)
            except Exception as e:
                print(e)
                continue
            
            #if len(thumbnails_created) > 50:
            #    break
            
            
            # Log
            thumbnails_created.append(thumb)
            # "Event" execution leads to automatic retries for 6h
            # http://docs.aws.amazon.com/lambda/latest/dg/concurrent-executions.html
            boto3.client('lambda').invoke(
                FunctionName='createThumbnail',
                InvocationType='Event',
                Payload=json.dumps({'source_bucket':alias['bucket'],
                                    'source_key': key,
                                    'target_bucket': thumbnail_bucket,
                                    'target_key': thumb,
                                    'target_height': thumb_height
                }),
            )            
    elif thumb_mode == 'all':
        for key in gallery_index['keys']:
            thumb = '{}/{}'.format(alias['bucket'],key)
            thumb = thumb.replace('//','/')
            if thumb in thumbnails:
                continue
            
            # Still here? Then Thumbnail does not exist yet. 
            
            #if len(thumbnails_created) > 50:
            #    break            

            # Log
            thumbnails_created.append(thumb)
            # "Event" execution leads to automatic retries for 6h
            # http://docs.aws.amazon.com/lambda/latest/dg/concurrent-executions.html
            boto3.client('lambda').invoke(
                FunctionName='createThumbnail',
                InvocationType='Event',
                Payload=json.dumps({'source_bucket':alias['bucket'],
                                    'source_key': key,
                                    'target_bucket': thumbnail_bucket,
                                    'target_key': thumb,
                                    'target_height': thumb_height
                }),
            )            
    else:
        raise KeyError('Unknown ThumbMode')

    return thumbnails_created

def splitext_(path):
    if len(path.split('.')) > 2:
        return path.split('.')[0],'.'.join(path.split('.')[-2:])
    return os.path.splitext(path)

def lambda_handler(event, context):
    result = {"status":1,"text": "Please specify a valid alias","ad":"s3.thumbnails"}
    try:
        result['alias'] = event['alias']
        alias = next(a for a in aliases if a['name'] == event['alias'])
        
        if not alias:
            raise KeyError('no alias')
            
        # Get User and validate
        result['text'] = 'Please specify a user'
        user = event['user']
        
        result['text'] = 'Not authorized'
        if user not in alias['authorized_users']:
            raise KeyError('user not auth')
        
        # Add Defaults
        try:
            bla = alias['prefix']
        except KeyError:
            alias['prefix'] = ''
        
        result['text'] = 'Please specify an action'
        action = event['action']
        
        result['text'] = 'Error during action execution'
        if action == 'getIndexUrl':
            print('a')
            temp = get_index_link(alias)
            result['url']  = temp['url']
            result['info'] = temp['info']
        elif action == 'createThumbnails':
            print('b')
            result['createdThumbnails'] = create_thumbnails(alias)
        elif action == 'createIndex':
            print('c')
            temp = get_index_link(alias, True)
            result['url']  = temp['url']
            result['info'] = temp['info']
        else:
            raise KeyError('dummy')
        
        result['status'] = 0
        result['text'] = 'Success'
    except Exception as e:
        print(e)

    return result