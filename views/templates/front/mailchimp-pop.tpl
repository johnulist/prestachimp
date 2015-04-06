{if $content_only}
	<div id="newsletter-wrap-footer">
		<div class="col-md-4">
			<div class="container">
				<div class="row mailchimp-bg">
					<div class="mc-pop-title">
						<h2>Sign Up to Our Newsletter</h2>
						<p class="lead">Be the first to know about new products, <br /> online exclusives, promotions, and much more!</p>						
					</div>
					<div class="col-md-6 col-xs-12 mc-form">
						<form action="{$mailchimp}" role="form" method="post" id="Newsletter_SubscribeForm">
							<div class="row">
								<div class="form-group">
									<input class="form-control" name="EMAIL" id="EMAIL" type="email" placeholder="Please enter your email">
								</div>
								<button id="btn_Subscribe" type="submit" class="btn btn-warning btn-block">Subscribe</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
{/if}