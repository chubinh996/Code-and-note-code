<script>
	jQuery("a.tab-click-design").click(function() {
		jQuery('html, body').animate({
			scrollTop: jQuery("div#design-fortress-page").offset().top - 500
		}, 1000);
	});
	jQuery("a.tab-click-quality").click(function() {
		jQuery('html, body').animate({
			scrollTop: jQuery("div#quality-fortress-page").offset().top - 500
		}, 1000);
	});
	jQuery("a.tab-click-functionality").click(function() {
		jQuery('html, body').animate({
			scrollTop: jQuery("div#functionality-fortress-page").offset().top - 500
		}, 1000);
	});
</script>