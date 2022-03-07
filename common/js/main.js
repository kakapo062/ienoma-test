$(function () {

//アコーディオン
$(".accordion").hide();
$(".qa_item").on("click", function(e){
    $('.accordion',this).slideToggle('fast');
    if($(this).hasClass('open')){
        $(this).removeClass('open');
    }else{
        $(this).addClass('open');
    }
});

//追従CTA
$(window).scroll(function () {
	if ($(this).scrollTop() > 300) {
		$('#float_cta').addClass("-show");
	} else {
		$('#float_cta').removeClass("-show");
	}
});

// 続きを読む
$('.ng_content').readmore({
    speed: 250,
    collapsedHeight: 70,
    moreLink: '<a href="#" class="ng_content_more">もっとみる</a>',
    lessLink: '<a href="#" class="ng_content_more">閉じる</a>'
});
});

$(document).ready(function(){
    $('a[href^="#"]').on('click',function (e) {
        e.preventDefault();

        var target = this.hash;
        var $target = $(target);
        var adjust = -150;
        $('html, body').stop().animate({
            'scrollTop': $target.offset().top + adjust
        }, 300, 'swing', function () {
        window.location.hash = target;
        });
    });
});
