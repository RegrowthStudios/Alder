$("#side-menu > li:not(.disabled)").on("click", function(e) {
    e.preventDefault();

    $("#leave-confirm-modal").modal({
        backdrop: 'static',
        keyboard: false
    }).one("click", '#leave', function(e) {
        window.location = "./index.html";
    });
});
