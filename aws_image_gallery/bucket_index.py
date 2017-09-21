import boto3
import json
import gzip
import os

s3  = boto3.resource('s3')

def lambda_handler(event, context):
    result = {'status': 1, 'msg': 'Invalid Parameters','info':''}
    prefix = ''
    keys   = []
    thumbnail_bucket = ''
    thumbnail_mode   = ''
    ends = []
    
    try:
        bucket = event['bucket']
        
        try:
            prefix = event['prefix']
        except KeyError:
            pass
        
        try:
            thumbnail_bucket = event['thumbnail_bucket']
        except KeyError:
            pass    
        
        try:
            thumbnail_mode = event['thumbnail_mode']
        except KeyError:
            pass          
        
        try: 
            ends = tuple(set(ext.lower() for ext in event['extensions']))
        except KeyError:
            pass  
        
        result['msg'] = 'Error getting Iterator'
        if not prefix:
            iterator = s3.Bucket(bucket).objects.all()
        else:
            iterator = s3.Bucket(bucket).objects.filter(Prefix=prefix)
            
        result['msg'] = 'Error extending Keys'
        #keys = [obj.key for obj in iterator] 
        
        if ends:
            keys = list(map(lambda obj: obj.key, 
                            filter(lambda o: o.key.lower().endswith(ends), iterator)))
        else:
            keys = list(map(lambda obj: obj.key, iterator))
            
        if not keys:
            result['msg'] = 'No Keys Found'
            result['status'] = 0
            raise KeyError('dummy')
            
        folders = list(set(map(lambda key: os.path.dirname(key), keys)))
            
        index_key = '{}/{}/Index.json'.format(bucket, prefix)
        index_key = index_key.replace('//','/')
        
        
        result['msg'] = 'Error putting Uncompressed Index to S3'
        content = json.dumps({'bucket':bucket,'keys':keys,
                              'folders':folders,'prefix':prefix,
                              'thumbnail_bucket':thumbnail_bucket,
                              'thumbnail_mode':thumbnail_mode}).encode()
        s3.Object(thumbnail_bucket,index_key).put(
            Body=content, #io.StringIO(content).read(), 
            CacheControl='max-age = "900, must re-validate"',
            ServerSideEncryption='AES256') 
           
        result['msg'] = 'Error putting Compressed Index to S3'
        index_key = index_key.replace('Index.json','IndexGzip.json')
        zipped = gzip.compress(content)
        s3.Object(thumbnail_bucket,index_key).put(
            Body=zipped, 
            CacheControl='max-age = "900, must re-validate"',
            ContentEncoding='gzip',
            ServerSideEncryption='AES256') 
        
        result['status'] = 0
        result['msg'] = 'Index successfully updated'
    except Exception as e:
        print(e)

    return(result)