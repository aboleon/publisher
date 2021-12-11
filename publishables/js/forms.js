function check(element) {
	var errors = 0;
	$(element).find('input.required,textarea.required,select.required').each(function() {
		var errText   = $('span.errorInfo.mandatory').text();
		var block     = $(this).parent('div');
		var errField  = block.find('span.error');
		var customError = block.find('span.errorInfo');
		if(customError.length > 0)
		{
			errText = customError.text();
		}
		var value = $(this).val();
		if(!value || value==0) {
			errField.text(errText).removeClass('hidden');
			if (errField.length<1) {
				block.append('<span class="error text-danger">'+errText+'</span>');
			}
			errors = 1;
		} else {
			if(errField.is(':visible')) {
				errField.addClass('hidden');
			}
		}
	});
	return errors;
}
function currentDate()
{
	var today = new Date();
	var dd = today.getDate();
	var mm = today.getMonth()+1; //January is 0!
	var yyyy = today.getFullYear();
	if(dd<10) {
		dd='0'+dd
	}
	if(mm<10) {
		mm='0'+mm
	}
	return dd+'/'+mm+'/'+yyyy;
}
$(function() {
	if($('input.date').length > 0)
	{
		var d = new Date();
		$('.date').datepicker({
			format: "dd/mm/yyyy",
			language: "fr",
			autoclose: true,
			todayHighlight: true,
			startDate: currentDate()
		});
	}
	$('form.PublisherForm button').off().on('click', function(e) {
		e.preventDefault(); //console.log('Start PublisherForm');
		var base_url = $('base').attr('href');
		var form = $(this).parents('form'),
		errors = check(form),
		data = form.serialize(),
		metabox = $('.PublisherFormResponse');
		console.log('errors', errors);
		if (errors < 1) {
			$.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				url: base_url,
				type: 'POST',
				dataType:'json',
				data: data,
				success: function(result)
				{
					console.log(result);
					metabox.html('<div class="alert alert-'+result.messageCode+'">'+result.message+'</div>');//.delay(3000).fadeOut();
					form.find('input,textarea').not('input.permanent,input[name=_token]').val('');
					form.find('select').prop('selectedIndex',0);
					form.find('input:checkbox').removeAttr('checked');
				},
				complete:function()
				{
					metabox.fadeIn();
				},
				error: function (xhr, ajaxOptions, thrownError) {
					var result = ' Error status : '+ xhr.status+ ", Thrown Error : "+ thrownError +", Error : "+ xhr.responseText;
					// $('#FormResponse').html('<div class="alert alert-danger" style="margin-top:10px;padding:8px 15px">'+result+'</div>');
					console.log(result);
				}
			});
		}
	});
});
