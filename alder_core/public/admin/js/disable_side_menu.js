setTimeout(function () {
    $(".disabled > a").off("click").click(function(e) {
        e.preventDefault();
    });
}, 60);
