var aws = require('aws-sdk');
var s3 = new aws.S3({ apiVersion: '2006-03-01' });
exports.handler = function(event, context) {
    var params = {};
    params.Bucket = event.Records[0].s3.bucket.name;
    params.Key = event.Records[0].s3.object.key;
    s3.headObject(params, function(err, data) {
         if (err) {
            context.fail('Error getting ' + params.Bucket + params.Key + ':' + JSON.stringify(err));
          } else {
              if (data.ServerSideEncryption !== 'AES256') {
                params.CopySource = params.Bucket + '/' + params.Key; 
                params.ServerSideEncryption = 'AES256';
                s3.copyObject(params, function(err,data) {
                  if (err) {
                      context.fail('Encryption of ' + params.CopySource + ' not successfull');
                  } else {
                      console.log('Encryption of ' + params.CopySource + ' successfull');
                      context.succeed('Encryption of ' + params.CopySource + ' successfull');
                  }
                });
              } else {
                  console.log('No Encryption necessary for ' + params.Bucket + params.Key);
                  context.succeed('No Encryption necessary for ' + params.Bucket + params.Key);
              }
          }
    });
};
