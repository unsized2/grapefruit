<?php
use Defuse\Crypto\Key;

function urban($name)
{
    $keyAscii = file_get_contents(CREDENTIALS.'/'.$name.'.txt'); // ... load the contents of /etc/daveapp-secret-key.txt
    return Key::loadFromAsciiSafeString($keyAscii);
}

?>
