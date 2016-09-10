/**
 * Created by khalil on 8/14/16.
 */
(function ($) {

    $.fn.scrollThisTo = function (options) {

        var settings = $.extend({
            offset: "200",
            speed: "slow",
            callback: function () {
            }
        }, options);

        return $('html, body').animate({
            scrollTop: this.offset().top
        }, settings.speed, 'swing', settings.callback);

    };

}(jQuery));