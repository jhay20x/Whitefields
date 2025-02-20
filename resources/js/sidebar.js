let menuState = sessionStorage.getItem("menuState");

if (!menuState) {
    $('.overlay').fadeIn();
    $('.overlay').removeClass('active');
} else {
    $("#sidebar").toggleClass('hideSidebar');
    $(".overlay").toggleClass('active');
}

$('.sidebarCollapse, .overlay').on('click', function () {            
    let menuState = sessionStorage.getItem("menuState");
    
    if (menuState) {
        $("#sidebar").toggleClass('hideSidebar');
        $('.overlay').fadeIn();
        $('.overlay').removeClass('active');
        sessionStorage.removeItem("menuState");
    }
    else {
        $("#sidebar").toggleClass('hideSidebar');
        $(".overlay").toggleClass('active');
        sessionStorage.setItem("menuState", true);
    }
});

$(window).on('beforeunload', function(){
    sessionStorage.removeItem("menuState");
});