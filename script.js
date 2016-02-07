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
            } else {
                //window.history.back();
            }
        }
    });


    $('span.org').mousemove(function (event) {
        var person_id = $(this).attr('id');
        var $divImg = $('div#img_' + person_id + '.orgIconFloat');
        var pos = ___getPageScroll();
        $divImg.css({left: pos[0] + event.clientX + 10 + 'px', top: pos[1] + event.clientY + 10 + 'px'});
    }).mouseleave(function (event) {
        $('div.orgIconFloat').hide();
    }).mouseenter(function (event) {
        var person_id = $(this).attr('id');
        if ($('div#img_' + person_id + '.orgIconFloat').length) {
            var $divImg = $('div#img_' + person_id + '.orgIconFloat');
            $divImg.show();
        } else {

            $.post(DOKU_BASE + 'lib/exe/ajax.php',
                    {
                        call: 'plugin_fksnewsfeed',
                        target: 'person',
                        name: 'local',
                    
                        person_id: person_id

                    },
            function (data) {

                var src = data['src'];
                console.log(src);
                var img = document.createElement('img');
                var divImg = document.createElement('div');
                $(divImg).addClass('orgIconFloat');
                $(divImg).attr('id', 'img_' + person_id);
                /**FIXME path to photos!!!*/
                img.src = src;
                console.log(divImg, img);

                divImg.appendChild(img);

                $('body').append(divImg);
                //that.appendChild(divImg);
                $(divImg).css({position: 'absolute'});

            },
                    'json');


        }
    });

    function ___getPageScroll() {
        var xScroll, yScroll;
        if (self.pageYOffset) {
            yScroll = self.pageYOffset;
            xScroll = self.pageXOffset;
        } else if (document.documentElement && document.documentElement.scrollTop) {	 // Explorer 6 Strict
            yScroll = document.documentElement.scrollTop;
            xScroll = document.documentElement.scrollLeft;
        } else if (document.body) {// all other Explorers
            yScroll = document.body.scrollTop;
            xScroll = document.body.scrollLeft;
        }
        var arrayPageScroll = new Array(xScroll, yScroll);
        return arrayPageScroll;
    }
    ;
    
    
  
});

