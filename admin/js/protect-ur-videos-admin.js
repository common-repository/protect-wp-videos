var pur_utils = {
	upload_to_storage: null
};

(function( $ ) {
	'use strict';

    pur_utils.upload_to_storage = function (file_url, cb) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: ajax_obj.ajaxurl,
            data: {
                action: 'upload_to_storage',
                security: ajax_obj.security,
                file_url: file_url

            },
            success: function (response) {
            	cb(response);
            }

        })
    };



})( jQuery );
