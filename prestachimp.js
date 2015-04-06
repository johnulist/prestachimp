// Activate fancyBox
$(".popchimp")
.fancybox({
    openEffect: 'elastic',
    closeEffect: 'elastic',
    prevEffect: 'fade',
    nextEffect: 'fade',
    fitToView: false, // 
    maxWidth: "90%", // 
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
			return false
		}
	}
});

$(window).load(function(){
	
	//POP UP THE MAILCHIMP NEWSLETTER TO FANCYBOX
	if($('#header').length > 0)
		$(".popchimp").eq(0).trigger('click');	

	$("#btn_Subscribe").on('click', function(e){
	
		e.preventDefault();

		$(this).attr('disabled', true);

		if ($("input[name='EMAIL']").val().trim().length == 0){

			$('#Newsletter_SubscribeForm').prepend('<p class="alert alert-warning">Sorry, you cannot leave the input field empty. Please try again.</p>').find('p.alert-warning').delay(2000).slideUp(function(){
				$(this).remove();
			});

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

			showFeedbackMessage( data.result, data.msg );
			$('#btn_Subscribe').attr('disabled', false);

		}).error(function(XMLHttpRequest, textStatus){
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
	$('.mc-form').prepend(html);
}