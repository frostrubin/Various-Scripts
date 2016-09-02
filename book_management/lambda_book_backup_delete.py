from __future__ import print_function

import json
import boto3
import ntpath

s3cli = boto3.client('s3')
s3res = boto3.resource('s3')

def lambda_handler(event, context):
    run = False
    try:
        run = event['wet']
    except KeyError:
        pass

    source = {}
    result = []
    try:
        for obj in s3res.Bucket('source_bucket').objects.filter(Prefix='source_folder/'):
            source[ntpath.basename(obj.key)] = ''

        for obj in s3res.Bucket('target_bucket').objects.filter(Prefix='target_folder/'):
            basename = ntpath.basename(obj.key)
            if basename not in source:
                print('Deleting ' + basename)
                result.append(basename)
                if run == True:
                    s3cli.delete_object(Bucket='target_bucket',Key='target_folder/'+basename)
        
        print('Deletion finished')
        return(result)
    except Exception as e:
        print(e)
        raise e