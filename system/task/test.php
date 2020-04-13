<?php 
 $fp = @fopen("test.txt", "a+"); 
 date_default_timezone_set(PRC); 
 $data = date("Y-m-d H:i:s",time());
 fwrite($fp , $data. " 让PHP定时运行吧！<br>");
 fclose($fp);
 ?>

