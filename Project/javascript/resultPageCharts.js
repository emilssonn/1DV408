"use strict";

var Init = function() {
	var ResultDivs = $( ".qResult" );
	$(ResultDivs).each(function(index) {
		var pieData = new Array();
		$(this).find("span").each(function( index ) {
			pieData.push({
				value: parseInt($(this).text()),
				color: $(this).data('color')});
		});
		var myPie = new Chart($(this).find("canvas")[0].getContext("2d")).Pie(pieData);
	});

}

Init();
