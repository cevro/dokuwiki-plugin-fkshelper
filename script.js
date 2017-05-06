/* DOKUWIKI:include_once tablesort/jquery.tablesorter.min.js*/
/* DOKUWIKI:include_once tablesort/jquery.tablesorter.staticrow.min.js*/
;
jQuery(function () {
    var $ = jQuery;
    // $('.content table').tablesorter();
    $('.content table').tablesorter({
            widgets: ['staticRow'],
            sortInitialOrder: 'desc'
        }
    );
    $('#html5_test').each(function () {

        var datetime = document.createElement("input");
        datetime.setAttribute("type", "datetime-local");
        if (datetime.type === "text") {
            /* FUCK FF!!! */
            if (!confirm('Tento prehliadač nepodporuje HTML5 input datetime-local. Chcete aj napriek tomu vyplniť tento formulár?')) {
                window.history.back();
            }
        }
        var week = document.createElement("input");
        week.setAttribute("type", "week");
        if (week.type === "text") {
            if (!confirm('Tento prehliadač nepodporuje HTML5 input week. Chcete aj napriek tomu vyplniť tento formulár?')) {
                window.history.back();
            }
        }
    });

    $('.person').on('mouseenter', function (event) {
        "use strict";
        const src = $(this).attr('data-src');
        const $tooltip = $(document.createElement('div'));
        var display = true;
        $('<img/>').attr({src: src}).load(function () {
            if(display){
                $tooltip.css({
                    position: 'absolute',
                    top: event.pageY + 5,
                    left: event.pageX + 10,
                    width: '70px'
                }).append(this);
                $('html').append($tooltip);
            }
        });
        $(this).on('mouseleave', function () {
            display = false;
            $tooltip.remove();
        });
    });
});
