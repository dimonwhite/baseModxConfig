<?php  return 'if ($modx->context->get(\'key\') == \'mgr\') {return;}
$url = $_SERVER[\'REQUEST_URI\'];
if (preg_match(\'~[A-Z]~\', $url)) {
   $modx->sendRedirect(strtolower($url));
}
return;
';