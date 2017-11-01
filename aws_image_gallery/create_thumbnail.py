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
## pip install exifread
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
## Transfer it to your local computer
## scp -i key.pem ec2-user@public-ip-address:~/CreateThumbnail.zip ./CreateThumbnail.zip

import boto3
import os
import sys
import uuid
from PIL import Image
from PIL import ImageOps
import PIL.Image
import PIL.ImageOps
import exifread
from exifread.tags import EXIF_TAGS

s3_client = boto3.client('s3')

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
        auto_rotate = False

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

        try:
            auto_rotate = event['auto_rotate']
        except KeyError:
            pass        

        result['msg'] = 'No target size specified'
        if not target_height and not target_width and not target_factor:
            raise KeyError('dummy')

        guid = uuid.uuid4()
        try:
            extension = os.path.splitext(source_key)[1]
        except Exception as e:
            print(e)
            extension = 'jpg'

        download_path = '/tmp/{}.{}'.format(guid, extension).replace('..','.')
        upload_path   = '/tmp/resized-{}.{}'.format(guid, extension).replace('..','.')
        
        result['msg'] = 'Could not download file'
        s3_client.download_file(source_bucket, source_key, download_path)
        result['msg'] = 'Could not resize file'
        with Image.open(download_path) as image:
            new_image = image
            
            # If needed, rotate
            if auto_rotate == True and extension.lower() in ['.jpg','.jpeg','.tiff','.tif']:
                file = open(download_path, 'rb')
                orientation = 1

                ## Build Map for Re-Mapping of String to EXIF Orientation Integer
                orientation_map = {}
                for tag, tag_value in EXIF_TAGS.items():
                    try:
                        if tag_value[0] == 'Orientation':
                            for key in tag_value[1]:
                                orientation_map[tag_value[1][key]] = key
                    except KeyError:
                        pass

                ## Get EXIF Orientation
                try:
                    exif_tags = exifread.process_file(file, details=False, stop_tag='Image Orientation')
                    #print(exif_tags)
                    orientation_string = exif_tags['Image Orientation'].printable
                    orientation = orientation_map[orientation_string]
                except Exception as e:
                    pass

                result['exif'] = 'Detected Orientation {}'.format(orientation)

                if orientation == 1:
                    pass
                elif orientation == 2:
                    new_image = ImageOps.mirror(image)
                elif orientation == 3:
                    new_image = image.rotate(180, expand=True)
                elif orientation == 4:
                    new_image = ImageOps.flip(image)
                elif orientation == 5:
                    new_image = image.rotate(270, expand=True)
                    new_image = ImageOps.mirror(new_image)
                elif orientation == 6:
                    new_image = image.rotate(270, expand=True)
                elif orientation == 7:
                    new_image = image.rotate(90, expand=True)
                    new_image = ImageOps.mirror(new_image)
                elif orientation == 8:
                    new_image = image.rotate(90, expand=True)            


            source_width  = new_image.size[0]
            source_height = new_image.size[1]
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
                new_image = new_image.resize((target_width, target_height))
            else:
                new_image.thumbnail((target_width, target_height))

            new_image.save(upload_path)
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