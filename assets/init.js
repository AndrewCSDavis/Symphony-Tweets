$(document).ready(function(){
$('#order-contents').on('change',function(){	
	var arr = [];
	$(this).find(':selected').each(function(){
		arr.push($(this).attr('value'));
	});	
	var selected = arr.join();
	$(this).prev('input').attr('value',selected);
});
$('#order-contents').selectize({
    plugins: ['drag_drop'],
    delimiter: ',',
    persist: true,
    create: function(input) {
		console.log(input);
        return {
            value: input,
            text: input,
			selected : 'selected',
        }
    }
});

})