from __future__ import print_function

import boto3
import ntpath

s3cli = boto3.client('s3')
s3res = boto3.resource('s3')

def lambda_handler(event, context):
    target = {}
    try:
        for obj in s3res.Bucket('target_bucket').objects.filter(Prefix='target_folder/'):
            target[ntpath.basename(obj.key)] = obj.e_tag
            
        for obj in s3res.Bucket('source_bucket').objects.filter(Prefix='source_folder/'):
            basename = ntpath.basename(obj.key)
            try:
                if target[basename] != obj.e_tag:
                    raise KeyError('dummy')
            except KeyError:
                print('Copying {} to {}'.format(basename,basename))
                s3cli.copy_object(Bucket='target_bucket',Key='target_folder/'+basename,
                    CopySource={'Bucket':'source_bucket','Key':'source_folder/'+basename})
        
        print('Copy finished')
        return('Copy finished')
    except Exception as e:
        print(e)
        raise e
            
        