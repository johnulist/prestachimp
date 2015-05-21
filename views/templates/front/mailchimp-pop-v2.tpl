{if $content_only}
	<div id="newsletter-wrap-footer">
		<div class="mc-label">
			<img src="{$base_dir}modules/prestachimp/images/pop.jpg" alt="" class="img-responsive" />
		</div>
		<br />
		<form action="{$mailchimp}" role="form" method="post" id="Newsletter_SubscribeForm">
			<div class="form-group">
				<input class="form-control" name="EMAIL" id="EMAIL" type="email" placeholder="Please enter your email">
			</div>
			<button id="btn_Subscribe" type="submit" class="btn btn-info btn-block">Subscribe</button>
		</form>
	</div>
{/if}