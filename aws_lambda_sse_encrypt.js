var aws = require('aws-sdk');
var s3 = new aws.S3({ apiVersion: '2006-03-01' });
exports.handler = function(event, context) {
    var params = { Bucket: event.Records[0].s3.bucket.name };
    params.Key = decodeURIComponent(event.Records[0].s3.object.key.replace(/\+/g, ' '));
    s3.headObject(params, function(err, data) {
        if (err) {
            context.fail('Error getting ' + params.Bucket + '/' + params.Key + ': ' + JSON.stringify(err));
        } else {
            params.CopySource = params.Bucket + '/' + params.Key;
            params.ServerSideEncryption = 'AES256';
            if (data.ServerSideEncryption !== params.ServerSideEncryption) {
                s3.copyObject(params, function(err, data) {
                    if (err) {
                        context.fail('Encryption of ' + params.CopySource + ' not successfull');
                    } else {
                        console.log('Encryption of ' + params.CopySource + ' successfull');
                        context.succeed('Encryption of ' + params.CopySource + ' successfull');
                    }
                });
            } else {
                console.log('No Encryption necessary for ' + params.CopySource);
                context.succeed('No Encryption necessary for ' + params.CopySource);
            }
        }
    });
};
