$(function(){
  $('.cast-photo__show').slick({
    dots:true,
    customPaging: function(slick,index) {
        slick.$slides.eq(index).css("background","white")
        var targetImage = slick.$slides.eq(index).attr('src');
        return '<img src=' + targetImage +'>';
    }
});

});

$(function(){
	var $img = $(".slick-track img");
	var imgWidth = $img.width();
	$img.css('height', imgWidth);
});
