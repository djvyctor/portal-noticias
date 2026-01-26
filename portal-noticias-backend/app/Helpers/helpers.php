<?php

if (!function_exists('format_name')) {
    // formata o nome do usuario
    function format_name(string $name): string
    {
        return ucwords(strtolower(trim($name)));
    }
}
