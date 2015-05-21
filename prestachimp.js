/*SEE THEMES/SENDAH/JS/MODULES/PRESTACHIMP/PRESTACHIMP.JS*/

// Activate fancyBox
if ($(window).width() < 480 ) {
	$(".popchimp")
	.fancybox({
	    openEffect: 'elastic',
	    closeEffect: 'elastic',
	    prevEffect: 'fade',
	    nextEffect: 'fade',
	    fitToView: false, // 
	    width: "100%", //
	    height: "40%", 
	    type: 'iframe',
	    scrolling: 'no',
	    iframe: {
	        preload: false
	    },
		afterLoad : function(){
			//SET A COOKIE WHEN THE NEWSLETTER IS POPPED UP
			$.cookie('mchimp-pop', 'loaded');
		},
		beforeLoad : function(){
			//CHECK FOR THE COOKIE BEFORE LOADING THE POP UP AGAIN
			if ($.cookie('mchimp-pop') == 'loaded'){
				return true
			}
		}
	});
}else{
	$(".popchimp")
	.fancybox({
	    openEffect: 'elastic',
	    closeEffect: 'elastic',
	    prevEffect: 'fade',
	    nextEffect: 'fade',
	    fitToView: false, // 
	    width: "30%", //
	    height: "40%", 
	    type: 'iframe',
	    scrolling: 'no',
	    iframe: {
	        preload: false
	    },
		afterLoad : function(){
			//SET A COOKIE WHEN THE NEWSLETTER IS POPPED UP
			$.cookie('mchimp-pop', 'loaded');
		},
		beforeLoad : function(){
			//CHECK FOR THE COOKIE BEFORE LOADING THE POP UP AGAIN
			if ($.cookie('mchimp-pop') == 'loaded'){
				return true
			}
		}
	});
}

$(document).ready(function(){
	
	/*============== THIS HANDLES THE MAILCHIMP MAILING LIST SIGNUP BOX IN THE PAGE FOOTER ==============*/
	$('#mc-form-footer').on('submit', function(){

		//DISABLE THE SUBMIT BUTTON
		$(this).find('button[type="submit"]').attr('disabled', true);

		var url 	 = $(this).attr('action'),
			email 	 = $(this).find('input[name="EMAIL"]').val();

		fetchRequest(url, email, '.mc-error-footer');

		return false;
	});

	/*============== THIS HANDLES THE MAILCHIMP MAILING LIST SIGNUP BOX IN THE HOMEPAGE ==============*/
	$('#mc-form-home').on('submit', function(){
		
		//DISABLE THE SUBMIT BUTTON
		$(this).find('button[type="submit"]').attr('disabled', true);

		var url 	= $(this).attr('action'),
			email 	= $(this).find('input[name="EMAIL"]').val();

		fetchRequest(url, email, '.mc-error-home');

		return false;
	});	
});

/*
* @title Send mailchimp request via ajax
* @param string URL The processing page
* @param string Email The email that the user inputted in the field
* @param string Location The location where the error message to be shown
*/
function fetchRequest(url, email, location)
{
	$.ajax({
		type: 'POST',
		url: url,
		data: {
			EMAIL 	: email,
			ajax	: true
		},
		dataType: 'json'

	}).success(function(data){

		console.log(data); return false;

		//HANDLE THE RESULT IF SUCCESS OR NOT
		if (data.result) {
			$(location).html('<p class="alert alert-success">'+ data.msg +'</p>');
		}else{
			$(location).html('<p class="alert alert-danger">'+ data.msg +'</p>');
		};

		$('form').find('button[type="submit"]').attr('disabled', false);
		
	}).error(function(XMLHttpRequest, textStatus){
		console.log(XMLHttpRequest);
	});	
}

$(window).load(function(){

	/*============== THIS HANDLES THE MAILCHIMP MAILING LIST SIGNUP BOX ON POPUP ==============*/

	//POP UP THE MAILCHIMP NEWSLETTER TO FANCYBOX
	if($('#header').length > 0)
		$(".popchimp").eq(0).trigger('click');	

	$("#btn_Subscribe").on('click', function(e){
	
		e.preventDefault();

		$(this).attr('disabled', true);

		if ($("input[name='EMAIL']").val().trim().length == 0){

			alert('Sorry, you cannot leave the email field empty');

			$(this).attr('disabled', false);
			return false;
		}

		$.ajax({
			type: 'post',
			url: $("#Newsletter_SubscribeForm").attr('action'),
			data: {
				EMAIL 	: $("input[name='EMAIL']").val().trim(),
				ajax	: true
			},
			dataType: 'json'

		}).success(function(data){

			alert(data.msg);
			$('#btn_Subscribe').attr('disabled', false);
			
			$.fancybox.close();

			return false;

		}).error(function(XMLHttpRequest, textStatus){
			alert(XMLHttpRequest); return false;

			console.log(XMLHttpRequest);
			$('#btn_Subscribe').attr('disabled', false);
		});
	})
});

function showFeedbackMessage( alertType, msg )
{
	toggleFormVisibility(alertType);

	if ($(".alert").length == 0)
	{
		createFeedbackWrapper( alertType );
	}		
	else
	{
		hideFeedback();
		clearFeedbackMsg();
	}

	setFeedbackMsg( alertType, msg );
	showFeedback();
}

function toggleFormVisibility( alertType )
{
	if ( alertType &&  !$('#Newsletter_SubscribeForm').hasClass('hidden') )
		hideForm();
	else if ( !alertType &&  $('#Newsletter_SubscribeForm').hasClass('hidden') )
		showForm();
}

function showForm()
{
	$('#Newsletter_SubscribeForm').show('slow');
}

function hideForm()
{
	$('#Newsletter_SubscribeForm').hide('slow');
}

function showFeedback()
{	
	$('.alert').slideDown('fast');
	$('.alert').removeClass('hidden');
}

function hideFeedback()
{
	$('.alert').slideUp('fast');
	$('.alert').addClass('hidden');
}

function setFeedbackMsg( alertType, msg )
{
	alertType 	 = alertType ? 'success' : 'danger' ;
	alertTypeAlt = alertType ? 'danger' : 'success' ;

	$('.alert').removeClass('alert-'+alertTypeAlt);
	$('.alert').addClass('alert-'+alertType);
	$('.alert').html(msg);
}

function clearFeedbackMsg()
{
	$('.alert').empty();
}

function createFeedbackWrapper( alertType )
{
	var html = $('<div></div>');
	$(html).addClass('alert hidden');
	$('.mc-form > form').prepend(html);
} 