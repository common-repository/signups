var uniqueKey = '';

(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
    $('.wp-signups-datepicker').datepicker();

    $('.wp-signups-timepicker').timepicker({
        timeFormat: 'h:mm p',
        interval: 10,
        //minTime: '10',
        //maxTime: '6:00pm',
        //defaultTime: $.now(),
        //startTime: $.now(),
        dynamic: false,
        dropdown: true,
        scrollbar: true
	});

    //if ($('.slots li').is('*')) {

    var last_css_id = $(".slots li").last().attr('id');
    var row_key = last_css_id.substr(last_css_id.indexOf("-") + 1);


    $(".add-slot").live('click', function() {
        row_key++;
        var uniqueKey = Math.floor(Math.random() * 0x75bcd15);
        /*function stringGen(len)
        {
            var text = " ";
            var charset = "abcdefghijklmnopqrstuvwxyz0123456789";
            for( var i=0; i < len; i++ )
                text += charset.charAt(Math.floor(Math.random() * charset.length));

            return text;
        }

        console.log(stringGen(3));

        console.log(uniqueKey);*/

        var new_row = '<li id="slot-' + row_key + ' ui-sortable-handle">' +
            'what? &nbsp;<input type="text" name="slot[' + row_key + '][title]" value="" size="20"> &nbsp;&nbsp;&nbsp;' +
            '# of available spots: <input type="text" name="slot[' + row_key + '][qty]" value="" size="5">' +
            '<input type="hidden" name="slot[' + row_key + '][id]" value="' + uniqueKey + '">' +
            ' <span class="dashicons dashicons-plus add-slot"></span> <span class="dashicons dashicons-minus remove-slot"></span>' +
            '</li>';
        $(this).parent("li").after(new_row);
        return false;
    });

    $(".remove-slot").live('click', function() {
        if ($('.slots li').length == 1) {
            $(this).prev().trigger('click');
        }
        $(this).parent("li").remove();
        return false;
    });

    $('.slots').sortable({
        distance: 5,
        opacity: 0.6,
        cursor: 'move'
    });

})( jQuery );
