/* DOKUWIKI:include_once tablesort/jquery.tablesorter.min.js*/
/* DOKUWIKI:include_once tablesort/jquery.tablesorter.staticrow.min.js*/
;
/* DOKUWIKI:include_once dwmediaselector.js*/

jQuery(function () {
    // $('.content table').tablesorter();
    jQuery('.content table').tablesorter({
            widgets: ['staticRow'],
            sortInitialOrder: 'desc'
        }
    );
    document.querySelectorAll('.person').forEach((el) => {
        el.addEventListener('mouseenter', (event) => {
            const src = el.getAttribute('data-src');
            const tooltip = document.createElement('div');
            tooltip.style.position = 'absolute';
            tooltip.style.top = (event.pageY + 5) + 'px';
            tooltip.style.left = (event.pageX + 10) + 'px';
            tooltip.style.width = '70px';

            const img = document.createElement('img');
            img.setAttribute('src', src);
            tooltip.appendChild(img);

            document.querySelector('html').appendChild(tooltip);
            el.addEventListener('mouseleave', () => {
                tooltip.remove();
            });
        });
    });
});
