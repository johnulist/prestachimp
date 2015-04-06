<div class="container">
	<div id="newsletter-wrap">
		<div class="col-lg-12">
			{if Tools::getValue('action') == "mc" && Tools::getValue('status') == 1}
				<p class="alert alert-success">{Tools::getValue('msg')}</p>
			{elseif Tools::getValue('action') == "mc" && Tools::getValue('status') == 0}
				<p class="alert alert-danger">{Tools::getValue('msg')}</p>
			{/if}
		</div>
		<div class="col-lg-6 mc-label">
			<h3>Subscribe to our Newsletter</h3>
		</div>
		<div class="col-lg-6">
			<form action="{$mailchimp}" role="form" method="post" id="mc-form-home">
				<div class="mc-error-home"></div>
				<div class="form-group">
					<input class="form-control" name="EMAIL" id="EMAIL" type="email" placeholder="Enter your email address">
				</div>
				<button type="submit" class="btn btn-warning btn-block btn-mc-home">Subscribe</button>
			</form>
		</div>
	</div>
</div>