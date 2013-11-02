"use strict";
$.extend($.datepicker,{_checkOffset:function(inst,offset,isFixed){return offset}});
$('#endDateId').datetimepicker({
	minDate: new Date(),
	dateFormat: "yy-mm-dd"
});
