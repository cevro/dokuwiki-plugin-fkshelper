/**
 * 
 * @returns {undefined}
 */


jQuery(function () {
    var $ = jQuery;
    $('#html5_test').each(function () {

        var datetime = document.createElement("input");
        datetime.setAttribute("type", "datetime-local");
        if (datetime.type === "text") { 
            /* FUCK FF!!! */
            if (!confirm('Tento prehliadač nepodporuje HTML5 input datetime-local. Chcete aj napriek tomu vyplniť tento formulár?')) {
                window.history.back();
            } else {
                //window.history.back();
            }
        }
        var week = document.createElement("input");
        week.setAttribute("type", "week");
        if (week.type === "text") { 
            if (!confirm('Tento prehliadač nepodporuje HTML5 input week. Chcete aj napriek tomu vyplniť tento formulár?')) {
                window.history.back();
            }            else {
                //window.history.back();
            }
        }
    });

});
