require([
	'jquery'
	], function (jQuery) {
		(function ($) {
			$(document).ready(function () {
				
					
					$('.nav-main-menu li.category-menu').hover(function () {
						if ($(window).width() > 992){
							$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level1').removeClass('current-active');
							$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level2').removeClass('current-active');
							$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level3').removeClass('current-active');
							$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level4').removeClass('current-active');
							$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level5').removeClass('current-active');
							$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level6').removeClass('current-active');
							$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level7').removeClass('current-active');
							$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level8').removeClass('current-active');
							$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level9').removeClass('current-active');
							$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level10').removeClass('current-active');
							$('.nav-main-menu li.category-menu').removeClass('current-active');
							$('.nav-main-menu li.category-menu .category-menu-container').removeClass('current-active');
							$(this).addClass('current-active');
							$(this).find('div.category-menu-container').addClass('current-active');
						}
					});

					$('.nav-main-menu li.category-menu').mouseenter(function () {
						if ($(window).width() > 992){
							$(this).find('div.category-menu-container').show();
						}
					});
					$('.nav-main-menu li.category-menu').mouseleave(function () {
						if ($(window).width() > 992){
							$(this).find('div.category-menu-container').hide();
						}
					});

					$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level1').hover(function () {
						if ($(window).width() > 992){
							$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level1').removeClass('current-active');
							$(this).addClass('current-active');
						}

					}); 

					$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level1').hover(function () {
						if ($(window).width() > 1200){
							
							var heightsub = $(this).find('> ul.dropdown-menu').height();
							var heightparent = $(this).parent().parent().parent().height();
							var heightul = $(this).parent().height();

							if(heightsub > heightparent){
								$(this).parent().parent().parent().parent().height(heightsub);
							}
							else if(heightsub < heightparent ){
								$(this).parent().parent().parent().parent().height(heightparent);
							}	
						}else if( $(window).width() > 992 && $(window).width() < 1199){
							
							var heightsub = $(this).find('> ul.dropdown-menu').height();
							var heightparent = $(this).parent().parent().parent().height();
							var heightparents = $(this).parent().parent().height();

							if(heightparents > heightsub){
								$(this).parent().parent().parent().parent().height(heightparents);
							}
							else if(heightsub > heightparents ){
								$(this).parent().parent().parent().parent().height(heightsub);
							}
						}					
					});

					$('.nav-main-menu .category-menu-container').mouseleave(function (){
						if ($(window).width() > 1200){
							var heightul12 = $(this).find('>ul.dropdown-menu').height();
							$(this).height(heightul12);
						}
					});

					$('.nav-main-menu .category-menu-container').mouseleave(function (){
						if( $(window).width() > 992 && $(window).width() < 1199){
							var heightul13 = $(this).find('>ul.dropdown-menu>li').height();
							$(this).height(heightul13);
						}
					});

					
					$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level2').hover(function () {
						if ($(window).width() > 992){
							$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level2').removeClass('current-active');
							$(this).addClass('current-active');
						}
					});
					$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level3').hover(function () {
						if ($(window).width() > 992){
							$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level3').removeClass('current-active');
							$(this).addClass('current-active');
						}
					});
					$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level4').hover(function () {
						if ($(window).width() > 992){
							$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level4').removeClass('current-active');
							$(this).addClass('current-active');
						}
					});
					$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level5').hover(function () {
						if ($(window).width() > 992){
							$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level5').removeClass('current-active');
							$(this).addClass('current-active');
						}
					});
					$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level6').hover(function () {
						if ($(window).width() > 992){
							$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level6').removeClass('current-active');
							$(this).addClass('current-active');
						}
					});
					$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level7').hover(function () {
						if ($(window).width() > 992){
							$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level7').removeClass('current-active');
							$(this).addClass('current-active');
						}
					});
					$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level8').hover(function () {
						if ($(window).width() > 992){
							$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level8').removeClass('current-active');
							$(this).addClass('current-active');
						}
					});
					$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level9').hover(function () {
						if ($(window).width() > 992){
							$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level9').removeClass('current-active');
							$(this).addClass('current-active');
						}
					});
					$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level10').hover(function () {
						if ($(window).width() > 992){
							$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level10').removeClass('current-active');
							$(this).addClass('current-active');
						}
					}); 




					// if ( $(window).width() > 992 && $(window).width() < 1199) {

					// 	$('.nav-main-menu li.category-menu').hover(function () {
					// 		$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level1').removeClass('current-active');
					// 		$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level2').removeClass('current-active');
					// 		$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level3').removeClass('current-active');
					// 		$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level4').removeClass('current-active');
					// 		$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level5').removeClass('current-active');
					// 		$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level6').removeClass('current-active');
					// 		$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level7').removeClass('current-active');
					// 		$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level8').removeClass('current-active');
					// 		$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level9').removeClass('current-active');
					// 		$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level10').removeClass('current-active');
					// 		$('.nav-main-menu li.category-menu').removeClass('current-active');
					// 		$('.nav-main-menu li.category-menu .category-menu-container').removeClass('current-active');
					// 		$(this).addClass('current-active');
					// 		$(this).find('div.category-menu-container').addClass('current-active');
					// 	});

					// 	$('.nav-main-menu li.category-menu').mouseenter(function () {
					// 		$(this).find('div.category-menu-container').show();
					// 	});
					// 	$('.nav-main-menu li.category-menu').mouseleave(function () {
					// 		$(this).find('div.category-menu-container').hide();
					// 	});
					// 	$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level1').hover(function () {
					// 		$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level1').removeClass('current-active');
					// 		$(this).addClass('current-active');

					// 		var heightsub = $(this).find('> ul.dropdown-menu').height();
					// 		var heightparent = $(this).parent().parent().parent().height();
					// 		var heightparents = $(this).parent().parent().height();

					// 		if(heightparents > heightsub){
					// 			$(this).parent().parent().parent().parent().height(heightparents);
					// 		}
					// 		else if(heightsub > heightparents ){
					// 			$(this).parent().parent().parent().parent().height(heightsub);
					// 		}
					// 	});

					// 	$('.nav-main-menu .category-menu-container').mouseleave(function (){
					// 		var heightul12 = $(this).find('>ul.dropdown-menu>li').height();
					// 		$(this).height(heightul12);

					// 	});

					// 	$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level2').hover(function () {
					// 		$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level2').removeClass('current-active');
					// 		$(this).addClass('current-active');
					// 	});
					// 	$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level3').hover(function () {
					// 		$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level3').removeClass('current-active');
					// 		$(this).addClass('current-active');
					// 	});
					// 	$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level4').hover(function () {
					// 		$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level4').removeClass('current-active');
					// 		$(this).addClass('current-active');
					// 	});
					// 	$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level5').hover(function () {
					// 		$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level5').removeClass('current-active');
					// 		$(this).addClass('current-active');
					// 	});
					// 	$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level6').hover(function () {
					// 		$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level6').removeClass('current-active');
					// 		$(this).addClass('current-active');
					// 	});
					// 	$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level7').hover(function () {
					// 		$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level7').removeClass('current-active');
					// 		$(this).addClass('current-active');
					// 	});
					// 	$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level8').hover(function () {
					// 		$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level8').removeClass('current-active');
					// 		$(this).addClass('current-active');
					// 	});
					// 	$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level9').hover(function () {
					// 		$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level9').removeClass('current-active');
					// 		$(this).addClass('current-active');
					// 	});
					// 	$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level10').hover(function () {
					// 		$('.nav-main-menu .category-menu-container ul.dropdown-menu li.level10').removeClass('current-active');
					// 		$(this).addClass('current-active');
					// 	}); 
					// } 
				});              
})(jQuery);
});