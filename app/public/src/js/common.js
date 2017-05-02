var Issues = {};
Issues.pagining = function (current, last, uri) {
    if(last === 0) {
        return;
    }
    var pages = {
        start: [],
        middle: [],
        end: []
    };
    for (var i = 1; i <= last; i++) {
        if(i < 3) {
            pages.start.push(i);
        }
        else if(last - i < 3) {
            pages.end.push(i);
        }
        else if(Math.abs(i - current) < 2) {
            pages.middle.push(i);
        }
    }
    var paging = $('#pagining');
    if(current > 1) {
        paging.append('<a href="'+ uri + (current - 1)+'" class="nav">Previous</a>');
    }
    $.each(pages.start, function (key, page) {
        appendPage(page);
    });
    if(last > 2 && (!pages.middle.length || pages.middle.length && pages.start.slice(-1)[0] + 1 < pages.middle[0])) {
        paging.append('<span>...</span>');
    }
    $.each(pages.middle, function (key, page) {
        appendPage(page);
    });
    if(pages.middle.length && pages.end.length && pages.middle.slice(-1)[0] + 1 < pages.end[0]) {
        paging.append('<span>...</span>');
    }
    $.each(pages.end, function (key, page) {
        appendPage(page);
    });
    if(current < last) {
        paging.append('<a href="' + uri + (current + 1) +'" class="nav">Next</a>');
    }
    function appendPage(page) {
        var link = $('<a>').attr('href', uri + page).text(page);
        if(page == current){
            link.addClass('active');
        }
        link.appendTo(paging);
    }
};
