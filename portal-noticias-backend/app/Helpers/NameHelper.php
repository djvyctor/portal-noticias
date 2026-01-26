<?php

namespace App\Helpers;

class NameHelper
{
    // formata o nome do usuário (primeira letra maiúscula de cada palavra)
    public static function format(string $name): string
    {
        return ucwords(strtolower(trim($name)));
    }
}
