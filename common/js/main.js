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

$(document).ready(function(){

$('[name=name]').on('change',function() {
    var name_val = $(this).val();
    if( name_val != '' ){
        $(this).closest('dl').addClass('-ok')
        $(this).closest('dl').removeClass('-err');
        $(this).next('.err_mes').remove();
    } else {
        $(this).closest('dl').addClass('-err')
        $(this).closest('dl').removeClass('-ok');
        var elm = err_empty( '氏名' );
        $(this).after( elm );
    }
    btn_act();
});

$('[name=phone]').on('change',function() {
    $(this).next('.err_mes').remove();
    var phone_val = $(this).val();
    if( phone_val == '' ){
        $(this).closest('dl').addClass('-err')
        $(this).closest('dl').removeClass('-ok');
        var elm = err_empty( '電話番号' );
        $(this).after( elm );
    } else if( !phone_val.match(/^0.*/) || !phone_val.match(/^[0-9]{10,11}$/) ) {
        $(this).closest('dl').addClass('-err')
        $(this).closest('dl').removeClass('-ok');
        var elm = err_format();
        $(this).after( elm );
    } else {
        $(this).closest('dl').addClass('-ok')
        $(this).closest('dl').removeClass('-err');
        $(this).next('.err_mes').remove();
    }
    btn_act();

});

$('[name=email]').on('change',function() {
    $(this).next('.err_mes').remove();
    var email_val = $(this).val();
    if( email_val == '' ){
        $(this).closest('dl').addClass('-err')
        $(this).closest('dl').removeClass('-ok');
        var elm = err_empty( 'メールアドレス' );
        $(this).after( elm );
    } else if( !email_val.match(/^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/) ) {
        $(this).closest('dl').addClass('-err')
        $(this).closest('dl').removeClass('-ok');
        var elm = err_format();
        $(this).after( elm );
    } else {
        $(this).closest('dl').addClass('-ok')
        $(this).closest('dl').removeClass('-err');
    }
    btn_act();
});

function err_empty( str ){
var err_mes = '<p class="err_mes">'+ str + 'を入力してください</p>';
return err_mes;
}

function err_format(){
var err_mes = '<p class="err_mes">正しい形式で入力してください</p>';
return err_mes;
}

function btn_act(){
var ok = $('.-ok').length;
console.log(ok);
if( ok == 3 ){
    $('.submit').prop("disabled", false);
    $('.submit').addClass('hover-on');
} else {
    $('.submit').prop("disabled", true);
    $('.submit').removeClass('hover-on');
}
}

});