<?php

function template_verification_control_recaptcha($verify_id, $verify_context)
{
	echo '
		<div class="g-recaptcha" data-sitekey="', $verify_context['site_key'], '"></div>';
}
