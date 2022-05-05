<?php

//very basic one level json writer and reader

//investigate other classes on packagist eg json-ld reader see
//https://github.com/brick/structured-data/blob/master/src/Reader/JsonLdReader.php

function json_ld_read($data){
  $data = json_decode($data, true);
  unset ($data['@context']);

  foreach ($data AS $key => $value){
    if ($key == '@type'){
      $type = $value;
    }else{
      $output[$type][$key] = $value;
    }
  }
  return $output;
}


function json_ld_write($type, $data, $context='http://schema.org/')
{
$json_ld['@context'] = $context;
$json_ld['@type'] = $type;
$output=json_encode($json_ld + $data, JSON_UNESCAPED_SLASHES);
return $output;
}
