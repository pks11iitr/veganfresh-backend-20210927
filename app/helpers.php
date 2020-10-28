<?php

function is_decimal( $val )
{
    return is_numeric( $val ) && floor( $val ) != $val;
}
