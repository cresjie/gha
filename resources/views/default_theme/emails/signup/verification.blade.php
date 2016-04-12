<html>
	
	<body>
		<?php $link = URL::route('_email-verification.show',$verification_token); ?>
		Click this link in order to verify your email <a href="<?=$link?>"><?=$link?></a>
	</body>

</html>