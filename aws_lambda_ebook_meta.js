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
    
    var file_in     = event_property_to_var(event,'file_in');
    var file_out    = event_property_to_var(event,'file_out');
    var title       = event_property_to_var(event,'title');
    var author      = event_property_to_var(event,'author');
    var author_sort = event_property_to_var(event,'author_sort');
    var genre       = event_property_to_var(event,'genre');
    var update      = true;

    if (title === '' || author === '' || author_sort === '' || genre === '') {
      update = false;
    }

    var s3 = new aws.S3({params: {Bucket: 'bookbucketname'}});
        async.waterfall(
            [
                function(callback) {
                    if (file_in === '' || file_out === '') {
                      callback('Error','file_in or file_out not provided',null);
                    }
                    callback(null, '');
                },
                function(dummy_param, callback) {
                    function s4() {
                      return Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1);
                    }
                    var tmp_guid = s4() + s4() + s4() + '-' +s4() + s4() + s4();
                    console.log('TempGuid: ' + tmp_guid);
                    callback(null, tmp_guid);
                },
                function(guid, callback) {
                    var filename = '/tmp/' + guid + '.' + file_in.split('.').pop();
                    var file   = fs.createWriteStream(filename);
                    var stream = s3.getObject({Key: 'books/' + file_in}).createReadStream();
                      stream.pipe(file).on('end', function() {
                        //Nothing
                      }).on('error', function() {callback('Error', 'Could not read file from S3 to /tmp', filename); });
                      console.log('Opened' + file_in + ' to ' + filename);
                      callback(null, filename); //Das ruft jetzt das CMD auf
                },
                function(tmp_filename, callback) {
                    var command = 'export LANG=en_US.UTF-8;./calibre/ebook-meta ' + tmp_filename;
                    if (update === true) {
                      // Write new data to file
                      command += ' -t "' + title + '"';
                      command += ' -a "' + author + '"';
                      command += ' --author-sort="' + author_sort + '"';
                      command += ' --tags="' + genre + '"';
                      command += ' -c "" -r "" -p "" -k "" -d "" -l "" --isbn "" -s "" --category ""';
                    }
                    console.log('Command' + command);
                    callback(null, tmp_filename, command);
                },
                function(tmp_filename, command, callback) {
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
                      }
                      callback(null, tmp_filename, stdout);
                    });
                },
                function(tmp_filename, stdout, callback) {
                  if (update !== true) {
                    // Data was only read from file
                    callback(null, 'Data was read', stdout);
                  } else {
                    // Data was written to the file
                    var stream  = fs.createReadStream(tmp_filename);
                    s3.putObject({Key: 'books/' + file_out, Body: stream}, function (err) {
                      if (err) {
                        callback(err, 'Could not put file back to s3', null);
                      }
                      callback(null, file_out + ' successfully written', null);
                    });                  
                  }
                }
            ],
            function (err, msg, data) {
              console.log('Callback Function Reached');
              console.log('Err: ' + err);
              console.log('Msg ' + msg);
              console.log('Data ' + data);
              var result = {};
              result.update = update;
              result.msg  = msg;
              result.data = data;
              if (err) {
                result.status = 1;
                var out = '';
                try {
                  out = msg + JSON.stringify(data);
                } catch(err) {
                  out = msg;
                }
                context.fail(out);
              } else {
                result.status = 0;
                context.succeed(result);
              }
            }
        );
}
