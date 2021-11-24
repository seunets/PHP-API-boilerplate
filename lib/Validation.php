<?php
function emailValid( $email )
{
   $atPos = strpos( $email, '@' );
   $dotPos = strpos( substr( $email, $atPos ), '.' );
   $lastDotPos = strrpos( $email, '.' );
   return $atPos >= 1 && $dotPos >= 2 && strlen( $email ) - $lastDotPos >= 3;
}


function nameValid( $name )
{
   return strlen( $name ) >= 5 && strlen( $name ) - strrpos( $name, ' ' ) >= 3;
}
