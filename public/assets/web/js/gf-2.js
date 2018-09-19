$(function(){
  $('.cast-photo__show').slick({
    dots:true,
    customPaging: function(slick,index) {
        slick.$slides.eq(index).css("background","red")
        var targetImage = slick.$slides.eq(index).attr('src');
        return '<img src=' + targetImage +'>';
    }
});

});
