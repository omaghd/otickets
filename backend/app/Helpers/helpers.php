<?php

function generateTicketReference($length = 8): string
{
    $characters       = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $reference        = '';

    for ($i = 0; $i < $length; $i++) {
        $reference .= $characters[rand(0, $charactersLength - 1)];
    }

    return $reference;
}
