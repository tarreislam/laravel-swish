<?php


namespace Tarre\Swish\Client\Helpers;


use Illuminate\Support\Str;

class Helper
{
    public static function SwishOrderedUUID4()
    {
        return strtoupper(str_replace('-', '', Str::orderedUuid()));
    }

}