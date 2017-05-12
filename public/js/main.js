/*
 * jQuery File Upload Plugin JS Example
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

/* global $, window */



function createUpload(str , file_path) {
    $('#fileupload'+str).fileupload({
        url: '/upload-image' ,
        downloadTemplateId: 'template-download-'+str,
    });
}


