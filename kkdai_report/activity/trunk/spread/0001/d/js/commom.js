$(document).ready(function(e) {
    $('.xf-close').click(function(){
	$('.xf').hide();
	$('.block_jx').hide();
	});
	
$('#tc-wx').click(function(){
	$('.tc-wx-open').show();
	});	
	
$('#tc-wx-close').click(function(){
	$('.tc-wx-open').hide();
	});	
	
$('#tc-gz').click(function(){
	$('.tc-gz-open').show();
	});	
	
$('#tc-gz-close').click(function(){
	$('.tc-gz-open').hide();
	});	
	
$('.submit').click(function(){
	$('.content02').show();
	$('.content01').hide();
	});	
	

});
function getLink(){
	window.location="http://m.xiaoxialicai.com/";
	}