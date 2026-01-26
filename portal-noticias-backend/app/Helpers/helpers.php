<?php

if (!function_exists('format_name')) {
    // formata o nome do usuário (primeira letra maiúscula de cada palavra)
    function format_name(string $name): string
    {
        return \App\Helpers\NameHelper::format($name);
    }
}
