/**
 * Created by gaupoit on 1/11/17.
 */

var purUtil = (function ($) {
    'use strict'
    var api = {
        _generate_url: function(src, cb) {
            $.ajax({
                type: 'post',
                dataType: 'json',
                url: ajax_obj.ajaxurl,
                data: {
                    action: 'regenerate_private_url',
                    security: ajax_obj.security,
                    isCasting: true,
                    src: src
                }
                ,
                success: function (response) {
                    cb(response);
                }
            })
        }
    }

    return api;
})(jQuery);
