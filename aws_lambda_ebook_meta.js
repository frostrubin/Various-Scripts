var exec  = require('child_process').exec;
var aws   = require('aws-sdk');
var fs    = require('fs');
var async = require('async');

exports.handler = function(event, context) {
    function event_property_to_var(event, property_name) {
        if (event.hasOwnProperty(property_name)) {
          return event[property_name];
        } else {
          return '';
        }
    }
    
    var bucket      = event_property_to_var(event,'bucket');
    var file_in     = event_property_to_var(event,'file_in');
    var file_out    = event_property_to_var(event,'file_out');
    var title       = event_property_to_var(event,'title');
    var author      = event_property_to_var(event,'author');
    var author_sort = event_property_to_var(event,'author_sort');
    var genre       = event_property_to_var(event,'genre');
    var update      = true;

    if (title === '' || author === '' || author_sort === '' || genre === '' || file_out === '') {
      update = false;
    }    

    function s4() {
      return Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1);
    }
    var tmp_guid = s4() + s4() + s4() + '-' +s4() + s4() + s4();
    var tmp_filename = '/tmp/' + tmp_guid + '.' + file_in.split('.').pop();
    console.log('TempFile: ' + tmp_filename);

    var s3 = new aws.S3();
        async.waterfall(
            [
                function(callback) {
                    if (file_in === '' || bucket === '') {
                      callback('Error','file_in or bucket not provided',null);
                    }
                    callback(null, '');
                },
                function(dummy_param, callback) {
                    var file   = fs.createWriteStream(tmp_filename);         
                    file.on('finish', function() {
                      callback(null, ''); //Das ruft das CMD auf
                    });
                    s3.getObject({Bucket: bucket, Key: file_in}).
                      on('httpData', function(chunk) { file.write(chunk); }).
                      on('httpDone', function() { 
                        file.end(); //Das triggert das finish
                        console.log('Opened ' + file_in + ' to ' + tmp_filename);
                      }).
                      on('error', function(error) {callback('Error', 'Could not read file from S3 to ', tmp_filename); }).
                        send();
                },
                function(dummy_param, callback) {
                    var command = 'export LANG=en_US.UTF-8;./calibre/ebook-meta ' + tmp_filename;
                    if (update === true) {
                      // Write new data to file
                      command += ' -t "' + title + '"';
                      command += ' -a "' + author + '"';
                      command += ' --author-sort="' + author_sort + '"';
                      command += ' --tags="' + genre + '"';
                      command += ' -c "" -r "" -p "" -k "" -d "" -l "" --isbn "" -s "" --category ""';
                    }
                    console.log('Command: ' + command);
                    callback(null, command);
                },
                function(command, callback) {
                    var stdout = [],
                        stderr = [];
                    child = exec(command,{encoding:'utf8'},function(error) {
                        if (error) {
                          callback(error, 'ebook-meta exited with non-zero exit status', stderr);
                        }
                    });
                    console.log('Child Exec created');

                    child.stdout.on('data', function(data) {
                        console.log(data);
                        stdout.push(data);
                    });

                    child.stderr.on('data', function(data) {
                        console.error(data);
                        stderr.push(data);
                    });

                    child.on('close', function (code) {
                      if (code !== 0) {
                        callback('Error', 'ebook-meta exited with non-zero exit status', stderr);
                      } else {
                        callback(null, stdout);
                      }
                    });
                },
                function(stdout, callback) {
                  if (update !== true) {
                    // Data was only read from file
                    callback(null, 'Data was read', stdout);
                  } else {
                    // Data was written to the file in /tmp
                    var stream  = fs.createReadStream(tmp_filename);
                    s3.putObject({Bucket: bucket, Key: file_out, ServerSideEncryption: 'AES256', Body: stream}, function (err) {
                      if (err) {
                        callback(err, 'Could not put the changed file to s3', null);
                      } else {
                        callback(null, file_out + ' successfully written', stdout);
                      }
                    });                  
                  }
                },
                function(prev_result, stdout, callback) {
                  if (update !== true) {
                    callback(null, prev_result, stdout);
                  } else {
                    if (file_in !== file_out && file_out !== '') {
                      s3.deleteObject({Bucket: bucket, Key: file_in}, function(err) {
                        if (err) {
                          callback(err, 'Could not delete the input file ' + file_in, null);
                        } else {
                          callback(null, prev_result + ' and source file deleted', stdout);
                        }
                      });
                    } else {
                      callback(null, prev_result, stdout);
                    }
                  }
                }, 
                function(prev_result, stdout, callback) {
                  child = exec('rm -f ' + tmp_filename, function(error) {
                    if (error) { // Soft error, we do not propagate it
                      callback(null, prev_result + ' but temp file not cleaned up', stdout);
                    } else {
                      callback(null, prev_result + ' and temp file cleaned up', stdout);
                    }
                  });  
                }
            ],
            function (err, msg, data) {
              console.log('Callback Function Reached');
              console.log('Err: ' + err);
              console.log('Msg: ' + msg);
              console.log('Data: ' + data);
              var result = {};
              result.update = update;
              result.msg  = msg;
              result.data = data;
              var cleanup_cmd = 'rm -f ' + tmp_filename + ' &';
              if (err) {
                result.status = 1;
                var out = '';
                try {
                  out = msg + ' ' + JSON.stringify(data);
                } catch(error) {
                  out = msg;
                } 
                try {
                  out = out + ' ' + JSON.stringify(err);
                } catch(error) {
                }  
                context.fail(out);
              } else {
                result.status = 0;
                context.succeed(result);
              }
            }
        );
}