<?php

namespace Tarre\Swish\Client\Contracts;

interface Transformable
{
    public function transforms(): array;
}