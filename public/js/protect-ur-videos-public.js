(function( $ ) {
	'use strict';
    $( window ).load(function() {
        var players = videojs.players;
        Object.keys(players).forEach(function (key) {
            var curr = null;
            var isSeeking = false;
            var player = players[key];

            player.on('seeking', function () {
                isSeeking = true;
            });

            player.on('error', function () {
                var error = this.error();
                if(error.code === 2 || error.code === 4) {
                    _generate_url(this.src(), player);
                }
            });

            player.on('loadstart', function (evt) {
                if(curr) {
                    player.currentTime(curr);
                    player.play();
                    curr = null;
                }
            });

            function _generate_url(src, player) {
                $.ajax({
                    type: 'post',
                    dataType: 'json',
                    url: ajax_obj.ajaxurl,
                    data: {
                        action: 'regenerate_private_url',
                        security: ajax_obj.security,
                        src: src
                    }
                    ,
                    success: function (response) {
                        curr = player.currentTime();
                        player.src(response);
                        isSeeking = false;
                        player.play();
                    }
                })
            }
        });
    });
    $(function() {

    });
})( jQuery );
