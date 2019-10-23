<?php 


function getTagDescription($ip) {
  $op = "";                                                          
  $command = escapeshellcmd("python2.7 /home/ec2-user/environment/project/product_tagger/product_tagger.py \"{$ip}\"");                                                           
  exec($command, $op);                                                                  
  $color = explode ("'", $op[4])[3];                                                     
  $desc =  explode ("'", $op[3])[3];                                                    
  $brand =  explode ("'", $op[2])[3];                                                    
  $product = explode("'", $op[1])[3];                                                    
  $details = array(
    "color" => $color,
    "brand" => $brand,
    "product" => $product
  );

  return $details;                                                 
} 

?>
