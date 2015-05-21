$(document).ready(function(){

	//CHECK THE CURRENT SETTINGS
	if ($('#PCHIMP_SEND_VOUCHER_on').is(':checked')) {
		$('.form-group label > span, #PCHIMP_VOUCHERCODE, #PCHIMP_MIN_PURCHASE, #PCHIMP_VOUCHERTITLE, #PCHIMP_DISCOUNTTYPE, #PCHIMP_DISCOUNTVALUE').removeClass('hidden');
	}

	//WHEN THE SEND VOUCHER IS CHANGED
	$('input[name="PCHIMP_SEND_VOUCHER"]').on('change', function(){
		if ($(this).val()) {
			$('.form-group label > span, #PCHIMP_VOUCHERCODE, #PCHIMP_MIN_PURCHASE, #PCHIMP_VOUCHERTITLE, #PCHIMP_DISCOUNTTYPE, #PCHIMP_DISCOUNTVALUE').removeClass('hidden');
		}else{
			$('.form-group label > span, #PCHIMP_VOUCHERCODE, #PCHIMP_MIN_PURCHASE, #PCHIMP_VOUCHERTITLE, #PCHIMP_DISCOUNTTYPE, #PCHIMP_DISCOUNTVALUE').addClass('hidden');
		};
	})
});