<?php

namespace App;

use function Termwind\render;

class WordleDecorator
{
    private function showError(string $message)
    {
        render("<div class='mt-1 text-white bg-gray-900'><span class='text-red-600 font-bold mr-1'>Whoops</span> {$message}</div>");
    }
}
