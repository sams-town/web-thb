<?php
// Script Auto Deploy Git cPanel
$output = shell_exec("cd /home/rsthbid/public_html_git && git pull origin main 2>&1");
echo "<pre>$output</pre>";