(function() {

	var pur_global = {
		custom_uploader: {},
        isUploadedVideo: false
	};

	tinymce.create('tinymce.plugins.purvideojs', {
		init: function(ed, url) {
			ed.addButton('purvideojs', {
				title: 'Insert your protected videos',
				image: url+'/pur-video-js.png',
				onclick: function() {
                    var width = jQuery(window).width(), H = jQuery(window).height(), W = ( 720 < width ) ? 720 : width;
                    W = W - 80;
                    H = H - 250;
                    tb_show('Insert your protected video', '#TB_inline?inlineId=purvideoJSpopup&width=' + W + '&height=' + H);
                    jQuery("#TB_ajaxContent").css({
                        height: '100%',
                        width: '100%'
                    });
					setDefaultVars();
					return false;
				}
			});
		},
		createControl: function(n, cm) {
			return null;
		},
		getInfo: function() {
			return {
				longname: 'VideoJS for WordPress',
				author: 'buildwps',
				authorurl: 'www.buildwps.com',
				infourl: 'www.buildwps.com',
				version: '1.0'
			};
		}
	});
	tinymce.PluginManager.add('purvideojs', tinymce.plugins.purvideojs);
	
	jQuery(function() {
		//get the checkbox defaults
		var autoplay_default = jQuery('#videojs-autoplay-default').val();
		if ( autoplay_default == 'on' )
			autoplay_checked = ' checked';
		else
			autoplay_checked = '';
		
		var preload_default = jQuery('#videojs-preload-default').val();
		if ( preload_default == 'on' )
			preload_checked = ' checked';
		else
			preload_checked = '';

		
		var form = jQuery('<div id="purvideoJSpopup">\
		<div class="notice notice-error is-dismissible pur_notice" style="display: none">\
		    <p class="pur_message"></p>\
		</div>\
		<table id="purTbl" class="form-table">\
			<tr>\
				<th><label for="videojs-mp4">Upload MP4 Source</label> <input type="hidden" id="pur-videojs-mp4"></th>\
				<td><input type="button" name="videojs-mp4" id="pur-videojs-mp4-btn" value="Upload video"><br>\
				<small id="pur-video-name"></small><br>\
			</tr>\
			<tr>\
				<th><label for="videojs-width">Width</label></th>\
				<td><input type="text" name="videojs-width" id="pur-videojs-width"><br>\
				<small>The width of the video (Default: 512px).</small></td>\
			</tr>\
			<tr>\
				<th><label for="videojs-height">Height</label></th>\
				<td><input type="text" name="videojs-height" id="pur-videojs-height"><br>\
				<small>The height of the video (Default: 308px).</small></td>\
			</tr>\
			<tr>\
				<th><label for="videojs-autoplay">Autoplay</label></th>\
				<td><input id="pur-videojs-autoplay" name="videojs-autoplay" type="checkbox"'+autoplay_checked+' /></td>\
			</tr>\
			<tr>\
				<th><label for="videojs-controls">Show Player Controls</label></th>\
				<td><input id="pur-videojs-controls" name="videojs-controls" type="checkbox" checked /></td>\
			</tr>\
			<tr>\
				<th><label for="videojs-id">ID</label></th>\
				<td><input type="text" name="videojs-id" id="pur-videojs-id"><br>\
				<small>Add a custom ID to your video player.</small></td>\
			</tr>\
			<tr>\
				<th><label for="videojs-class">Class</label></th>\
				<td><input type="text" name="videojs-class" id="pur-videojs-class"><br>\
				<small>Add a custom class to your player.</small></td>\
			</tr>\
		</table>\
		<p class="submit">\
				<input type="button" id="pur-videojs-submit" class="button-primary" value="Insert Video" name="submit" />\
		</p>\
		</div>');
		var table = form.find('table');
		form.appendTo('body').hide();

		
		form.find('#pur-videojs-submit').click(function(){
			var button = this;
			if(pur_global.isUploadedVideo == false) {
                showFailedMessage('Please upload one video!')
            } else {
                disableInserBtn(button);
                _upload_to_storage(table.find('#pur-videojs-' + 'mp4').val(), function (res) {
                    if(res.success) {
                        var shortcode = '[protected_video';

                        //text options
                        var options = {
                            'mp4'      : '',
                            'width'    : '',
                            'height'   : '',
                            'id'       : '',
                            'class'    : ''
                        };

                        for(var index in options) {
                            var value = table.find('#pur-videojs-' + index).val();

                            // attaches the attribute to the shortcode only if it's different from the default value
                            if ( value !== options[index] )
                                shortcode += ' ' + index + '="' + value + '"';
                        }

                        //checkbox options
                        options = {
                            'autoplay' : autoplay_default,
                            'controls' : 'on'
                        };

                        for(var index in options) {
                            var value = table.find('#pur-videojs-' + index).is(':checked');

                            if ( value == true )
                                checked = 'on';
                            else
                                checked = '';

                            // attaches the attribute to the shortcode only if it's different from the default value
                            if ( checked !== options[index] )
                                shortcode += ' ' + index + '="' + value + '"';
                        }

                        //close the shortcode
                        shortcode += ']';

                        // inserts the shortcode into the active editor
                        tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);

                        // closes Thickbox
                        tb_remove();
                    } else {
                        enableInsertBtn(button);
                        var errorMsg = res.data;
                        showFailedMessage(errorMsg);
                    }
                });
            }
		});

        jQuery('#pur-videojs-mp4-btn')
			.click(function(e) {
				e.preventDefault();

				//extend the wp.media obj
                pur_global.custom_uploader = wp.media.frames.file_frame = wp.media({
					title: 'Choose video',
					frame: 'select',
					library: {
						type: 'video'
					},
					button: {
						text: 'Choose Video'
					},
					multiple: false
				});

                pur_global.custom_uploader.on('select', function () {
					var attachment = pur_global.custom_uploader.state().get('selection').first().toJSON();
                    jQuery('#pur-videojs-mp4').val(attachment.url);
                    jQuery('#pur-video-name').text('You have chosen video ' + attachment.filename);
                    jQuery('#pur-videojs-mp4-btn').val('Change video');
                    pur_global.isUploadedVideo = true;
                    showFailedMessage('');
                });

                pur_global.custom_uploader.open();
			});

	});
	
	function setDefaultVars() {
        var options = {
            'mp4'      : '',
            'width'    : '',
            'height'   : '',
            'id'       : '',
            'class'    : '',
            'autoplay' : '',
            'controls' : 'on'
        };

        for(var index in options) {
            jQuery('#pur-videojs-' + index).val('');
        }

        pur_global.isUploadedVideo = false;
        setDefaultVideo();
        showFailedMessage('');
        enableInsertBtn(jQuery('#pur-videojs-submit'));
    }
    
    function setDefaultVideo() {
        jQuery('#pur-video-name').text('');
        jQuery('#pur-videojs-mp4-btn').val('Upload video');
    }

	function disableInserBtn(button) {
        jQuery(button).val('Inserting video');
        jQuery(button).prop('disabled', true);
    }

    function enableInsertBtn(button) {
        jQuery(button).val('Insert video');
        jQuery(button).prop('disabled', false);
    }

    function showFailedMessage(message) {
        if(message != '') {
            jQuery('.pur_notice').show();
        } else {
            jQuery('.pur_notice').hide();
        }
        jQuery('.pur_message').html(message);
    }


	function _upload_to_storage(url, cb) {
        pur_utils.upload_to_storage(url, cb);
    }


})();
