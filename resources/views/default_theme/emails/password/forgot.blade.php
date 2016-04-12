<?php $link = URL::to('password/reset') . "?token={$reset->token}" ;?>
Click this link to reset your password: <a href="<?=$link?>"><?=$link?></a>