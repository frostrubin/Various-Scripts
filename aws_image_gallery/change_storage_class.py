import boto3

s3  = boto3.resource('s3')

def lambda_handler(event, context):
    max_count = -1
    bucket = 'your_bucket'
    prefix = 'a/folder/hierarchy/'
    
    if not bucket:
        raise KeyError('Bucket needed')
    
    if prefix:
        iterator = s3.Bucket(bucket).objects.filter(Prefix=prefix)
    else:
        iterator = s3.Bucket(bucket).objects.all()
    
    count = 0
    for obj in iterator:
        if obj.storage_class == 'STANDARD':
            continue
        
        count = count + 1
        
        if max_count > 0 and count > max_count:
            break
        
        try:
            obj.copy_from(
                CopySource={'Bucket':obj.bucket_name, 'Key':obj.key},
                MetadataDirective='COPY',
                StorageClass='STANDARD',
                ServerSideEncryption='AES256'
            )
        except Exception as e:
            print('Error for {}'.format(obj.key))
            print(e)
            continue
        
        print('Copied {}'.format(obj.key))
      
    return {'text':'All Done','count':count}