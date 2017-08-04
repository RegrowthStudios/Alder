window.onload = function() {
    var topOffset = 50;
    var width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
    if (width < 768) {
        topOffset = 100;
    }

    var height = ((this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height) - 1;
    height = height - topOffset;
    if (height < 1) height = 1;
    if (height > topOffset) {
        $("#page-wrapper").css("min-height", (height) + "px");
    }
};

// Makes sure that styling of the side menu is as expected (i.e. sublist focus styling on expanded not on browser definition of focus).
// Additionally fix bug that causes arrows to point wrong way.
$(document).ready(function() {
    $("#side-menu > li").click(function() {
        var subList = $(this).children("ul");
        if ($(subList).length) {
            var item = $(this).children("a");
            if ($(subList).attr("aria-expanded") === "true") {
                $(this).parent().children("li").each(function() {
                    $(this).children("a").removeClass("focused");
                });
                $(item).addClass("focused");
                $(this).addClass("active");
            } else {
                $(item).blur().removeClass("focused");
                $(this).removeClass("active");
            }
        }
    });
});
