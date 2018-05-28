/*-----------------------------------------------------------------------------------*/
/* 		Mian Js Start 
/*-----------------------------------------------------------------------------------*/
$(document).ready(function($) {
"use strict"
/*-----------------------------------------------------------------------------------*/
/*		STICKY NAVIGATION
/*-----------------------------------------------------------------------------------*/
$(".sticky").sticky({topSpacing:0});
/*-----------------------------------------------------------------------------------*/
/* 	LOADER
/*-----------------------------------------------------------------------------------*/
$("#loader").delay(500).fadeOut("slow");
/*-----------------------------------------------------------------------------------*/
/*  FULL SCREEN
/*-----------------------------------------------------------------------------------*/
$('.full-screen').superslides({});
/*-----------------------------------------------------------------------------------*/
/*    Parallax
/*-----------------------------------------------------------------------------------*/
jQuery.stellar({
   horizontalScrolling: false,
   scrollProperty: 'scroll',
   positionProperty: 'position',
});

/*-----------------------------------------------------------------------------------
    HOME SIMPLE TEXT SLIDER
/*-----------------------------------------------------------------------------------*/
$('.home-slide').flexslider({
	mode: 'fade',
	auto: true
});
/*-----------------------------------------------------------------------------------*/
/*	SERVICE LIST
/*-----------------------------------------------------------------------------------*/
$('.best-services .list').colio({
	id: 'demo_1',
	theme: 'black',
	placement: 'inside'
});
/*-----------------------------------------------------------------------------------*/
/* 	GALLERY SLIDER
/*-----------------------------------------------------------------------------------*/
$('.two-slider').owlCarousel({
    loop:true,
    margin:50,
    nav:true,
	navText: ["<i class='fa fa-angle-left'></i>","<i class='fa fa-angle-right'></i>"],
    responsive:{
        0:{
            items:1
        },
        700:{
            items:1
        },
        1000:{
            items:2
        }
}});
/*-----------------------------------------------------------------------------------*/
/*	CUBE PORTFOLIO
/*-----------------------------------------------------------------------------------*/
$('.ajax-work').cubeportfolio({
     filters: '#ajax-work-filter',
     loadMore: '#ajax-loadMore',
     loadMoreAction: 'click',
     layoutMode: 'grid',
     defaultFilter: '*',
     animationType: 'scaleSides',
     gapHorizontal: 0,
     gapVertical: 0,
     caption: 'zoom',
     displayType: 'lazyLoading',
     displayTypeSpeed: 40,
     // singlePage popup
     singlePageDelegate: '.cbp-singlePage',
     singlePageDeeplinking: true,
     singlePageStickyNavigation: true,
     singlePageCounter: '<div class="cbp-popup-singlePage-counter">{{current}} of {{total}}</div>',
     singlePageCallback: function(url, element) {
// to update singlePage content use the following method: this.updateSinglePage(yourContent)
var t = this;
 $.ajax({
     url: url,
     type: 'GET',
     timeout: 10000
 })
 .done(function(result) {
	 //console.log(result);
    t.updateSinglePage(result);
 })
  .fail(function() {
     t.updateSinglePage('AJAX Error! Please refresh the page!');
      });
   },
});
/*-----------------------------------------------------------------------------------*/
/* 	SLIDER REVOLUTION
/*-----------------------------------------------------------------------------------*/
jQuery('.tp-banner').show().revolution({
	dottedOverlay:"none",
	delay:10000,
	startwidth:1170,
	startheight:900,
	navigationType:"",
	navigationArrows:"solo",
	navigationStyle:"preview1",
	parallax:"mouse",
	parallaxBgFreeze:"on",
	parallaxLevels:[7,4,3,2,5,4,3,2,1,0],												
	keyboardNavigation:"on",						
	shadow:0,
	fullWidth:"on",
	fullScreen:"off",
	shuffle:"off",						
	autoHeight:"off",						
	forceFullWidth:"off",	
	fullScreenOffsetContainer:""	
});
/*-----------------------------------------------------------------------------------*/
/* 	SLIDER REVOLUTION
/*-----------------------------------------------------------------------------------*/
var tpj=jQuery;							
	var revapi104;
	tpj(document).ready(function() {
		if(tpj("#rev_slider_104_1").revolution == undefined){
			revslider_showDoubleJqueryError("#rev_slider_104_1");
		}else{
			revapi104 = tpj("#rev_slider_104_1").show().revolution({
				dottedOverlay:"none",
				delay:10000,
				startwidth:1170,
				startheight:900,
				sliderLayout:"fullscreen",
				navigationType:"bullet",
				navigationArrows:"solo",
				navigationStyle:"preview4",
				keyboardNavigation:"on",						
				shadow:0,
				fullWidth:"o",
				fullScreen:"off",
				shuffle:"off",
				navigation: {
					keyboardNavigation:"on",
					keyboard_direction: "horizontal",
					mouseScrollNavigation:"off",
					onHoverStop:"off",
				touch:{
					touchenabled:"on",
					swipe_threshold: 75,
					swipe_min_touches: 1,
					swipe_direction: "horizontal",
					drag_block_vertical: false
				},
				bullets: {
					enable:true,
					hide_onmobile:true,
					hide_under:960,
					style:"",
					hide_onleave:false,
					direction:"vertical",
					h_align:"right",
					v_align:"center",
					h_offset:30,
					v_offset:30,
					space:5
				}},	
				parallax: {
					type:"mouse",
					origo:"slidercenter",
					speed:2000,
					levels:[2,3,4,5,6,7,12,16,10,50],
					disable_onmobile:"on"
				},				
				autoHeight:"off",						
				forceFullWidth:"off",	
				fullScreenOffsetContainer:"",
				fallbacks: {
					simplifyAll:"off",
					nextSlideOnWindowFocus:"off",
					disableFocusListener:false,
				}});
	var newCall = new Object(),
		cslide;
	newCall.callback = function() { 
	var proc = revapi104.revgetparallaxproc(),
		fade = 1+proc,
		scale = 1+(Math.abs(proc)/10);
	    punchgs.TweenLite.set(revapi104.find('.slotholder, .rs-background-video-layer'),{opacity:fade,scale:scale});}
		newCall.inmodule = "parallax";
		newCall.atposition = "start";
		revapi104.bind("revolution.slide.onloaded",function (e) {
		revapi104.revaddcallback(newCall);
	});				}
});	
});
/*-----------------------------------------------------------------------------------*/
/*    CONTACT FORM
/*-----------------------------------------------------------------------------------*/
function checkmail(input){
  var pattern1=/^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
  	if(pattern1.test(input)){ return true; }else{ return false; }}     
    function proceed(){
    	var name = document.getElementById("name");
		var email = document.getElementById("email");
		var company = document.getElementById("company");
		var msg = document.getElementById("message");
		var errors = "";
	if(name.value == ""){ 
		name.className = 'error';
	  	  return false;}    
		  else if(email.value == ""){
		  email.className = 'error';
		  return false;}
		    else if(checkmail(email.value)==false){
		        alert('Please provide a valid email address.');
		        return false;}
		    else if(company.value == ""){
		        company.className = 'error';
		        return false;}
		   else if(msg.value == ""){
		        msg.className = 'error';
		        return false;}
		   else 
		  {
$.ajax({
	type: "POST",
	url: "php/submit.php",
	data: $("#contact_form").serialize(),
	success: function(msg){
	//alert(msg);
    if(msg){
		$('#contact_form').fadeOut(1000);
        $('#contact_message').fadeIn(1000);
        	document.getElementById("contact_message");
         return true;
        }}});
}};

/*-----------------------------------------------------------------------------------
    Animated progress bars
/*-----------------------------------------------------------------------------------*/
$('.progress-bars').waypoint(function() {
  $('.progress').each(function(){
    $(this).find('.progress-bar').animate({
      width:$(this).attr('data-percent')
     },100);
});},
	{ 
	offset: '100%',
    triggerOnce: true 
});


/*-----------------------------------------------------------------------------------*/
/* 	TESTIMONIAL SLIDER
/*-----------------------------------------------------------------------------------*/
$(".single-slide").owlCarousel({ 
    items : 1,
	autoplay:true,
	loop:true,
	autoplayTimeout:5000,
	autoplayHoverPause:true,
	singleItem	: true,
    navigation : true,
	navText: ["<i class='fa fa-angle-left'></i>","<i class='fa fa-angle-right'></i>"],
	pagination : true,
	animateOut: 'fadeOut'	
});
$('.item-slide').owlCarousel({
    loop:true,
    margin:30,
    nav:false,
	navText: ["<i class='fa fa-angle-left'></i>","<i class='fa fa-angle-right'></i>"],
    responsive:{
        0:{
            items:1
        },
        400:{
            items:2
        },
		900:{
            items:3
        },
        1200:{
            items:4
        }
    }
});
/*-----------------------------------------------------------------------------------*/
/* 		Active Menu Item on Page Scroll
/*-----------------------------------------------------------------------------------*/
$(window).scroll(function(event) {
		Scroll();
});	
$('.scroll a').on('click',function() {  
	$('html, body').animate({scrollTop: $(this.hash).offset().top -0}, 800);
		return false;
});
// User define function
function Scroll() {
var contentTop      =   [];
var contentBottom   =   [];
var winTop      =   $(window).scrollTop();
var rangeTop    =   1;
var rangeBottom =   100;
$('nav').find('.scroll a').each(function(){
	contentTop.push( $( $(this).attr('href') ).offset().top);
		contentBottom.push( $( $(this).attr('href') ).offset().top + $( $(this).attr('href') ).height() );
})
$.each( contentTop, function(i){
if ( winTop > contentTop[i] - rangeTop ){
	$('nav li.scroll')
	  .removeClass('active')
		.eq(i).addClass('active');			
}}  )};


/*-----------------------------------------------------------------------------------*/
/* 		MENU FULL SCREEN KEFT TO RIGHT
/*-----------------------------------------------------------------------------------*/
jQuery(document).ready(function($){
	var isLateralNavAnimating = false;	
	//open/close lateral navigation
	$('.cd-nav-trigger').on('click', function(event){
		event.preventDefault();
		//stop if nav animation is running 
		if( !isLateralNavAnimating ) {
			if($(this).parents('.csstransitions').length > 0 ) isLateralNavAnimating = true; 
			$('body').toggleClass('navigation-is-open');
			$('.cd-navigation-wrapper').one('webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend', function(){
				//animation is over
				isLateralNavAnimating = false;
			});
		}
	});
});