$(function() {
	if (!!$('.action-alert').length) {
		window.setTimeout(function() {
		    $(".action-alert").fadeTo(500, 0).slideUp(500, function(){
		        $(this).remove(); 
		    });
		}, 5000);
	}
});

