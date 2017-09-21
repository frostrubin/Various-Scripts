## Created via the Guide at http://docs.aws.amazon.com/lambda/latest/dg/with-s3-example-deployment-pkg.html#with-s3-example-deployment-pkg-python
## Save the file as CreateThumbnail.py.
## Copy it over.
## scp -i key.pem /path/to/my_code.py ec2-user@public-ip-address:~/CreateThumbnail.py
## Connect to a 64-bit Amazon Linux instance via SSH.
## ssh -i key.pem ec2-user@public-ip-address
## Install Python 3.6 and virtualenv using the following steps:
## sudo yum install -y gcc zlib zlib-devel openssl openssl-devel
## wget https://www.python.org/ftp/python/3.6.1/Python-3.6.1.tgz
## tar -xzvf Python-3.6.1.tgz
## cd Python-3.6.1 && ./configure && make
## sudo make install
## sudo /usr/local/bin/pip3 install virtualenv
## Choose the virtual environment that was installed via pip3
## /usr/local/bin/virtualenv ~/shrink_venv
## source ~/shrink_venv/bin/activate
## Install libraries in the virtual environment
## pip install Pillow
## pip install boto3
## Note !!!
## AWS Lambda includes the AWS SDK for Python (Boto 3), so you don't need to include it in 
## your deployment package, but you can optionally include it for local testing.
## End Note !!!
## Add the contents of lib and lib64 site-packages to your .zip file. 
## cd $VIRTUAL_ENV/lib/python3.6/site-packages
## zip -r9 ~/CreateThumbnail.zip *
## Add your python code to the .zip file
## cd ~
## zip -g CreateThumbnail.zip CreateThumbnail.py

import boto3
import os
import sys
import uuid
from PIL import Image
import PIL.Image

s3_client = boto3.client('s3')


def splitext_(path):
    if len(path.split('.')) > 2:
        return path.split('.')[0],'.'.join(path.split('.')[-2:])
    return os.path.splitext(path)


def handler(event, context):
    result = {'status': 1, 'msg': 'Source or Target not fully specified','info':''}
    try:
        source_bucket = event['source_bucket']
        target_bucket = event['target_bucket']
        source_key    = event['source_key']
        target_key    = event['target_key']

        if (not source_bucket or not target_bucket
            or not source_key or not target_key):
            raise KeyError('dummy')

        target_width  = 0
        target_height = 0
        target_factor = 0

        try:
            target_width  = event['target_width']
        except KeyError:
            pass

        try:
            target_height = event['target_height']
        except KeyError:
            pass

        try:
            target_factor = event['target_factor']
        except KeyError:
            pass

        result['msg'] = 'No target size specified'
        if not target_height and not target_width and not target_factor:
            raise KeyError('dummy')

        guid = uuid.uuid4()
        file_name, extension = splitext_(source_key)
        download_path = '/tmp/{}.{}'.format(guid, extension)
        upload_path   = '/tmp/resized-{}.{}'.format(guid, extension)
        
        result['msg'] = 'Could not download file'
        s3_client.download_file(source_bucket, source_key, download_path)
        result['msg'] = 'Could not resize file'
        with Image.open(download_path) as image:
            source_width  = image.size[0]
            source_height = image.size[1]
            if target_factor != 0:
                result['info'] = 'Target Factor {}'.format(target_factor)
                target_width  = int(source_width  / target_factor)
                target_height = int(source_height / target_factor)
            elif target_width != 0 and target_height != 0:
                result['info'] = 'Width {} , Height {}'.format(target_width, target_height)
            elif target_width != 0:
                result['info'] = 'Target Width {}'.format(target_width)
                target_height = int(source_height * target_width / source_width)
            else:
                result['info'] = 'Target Height {}'.format(target_height)
                target_width = int(source_width * target_height / source_height)

            if target_width > source_width or target_height > source_height:
                resized = image.resize((target_width, target_height))
                resized.save(upload_path)
            else:
                image.thumbnail((target_width, target_height))
                image.save(upload_path)
        result['msg'] = 'Could not upload file'
        s3_client.upload_file(upload_path, target_bucket, target_key, {'ServerSideEncryption': 'AES256'})
        result['msg'] = 'Could not clean up'
        os.remove(download_path)
        os.remove(upload_path)
        result['status'] = 0
        result['msg'] = 'Finished'
    except Exception as e:
        print(e)

    return(result)