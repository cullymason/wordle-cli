<?php

namespace App;


use Illuminate\Support\Facades\Cache;
use function Termwind\ask;
use function Termwind\render;

class WordleDecorator
{
    public string $answer;
    public string $errorText;

    public function showError(string $message)
    {

        render("<div class='mt-1 text-white bg-gray-900'><span class='text-red-600 font-bold mr-1'>Whoops</span> {$message}</div>");
    }
    public function showSuccess(string $message)
    {
        render("<div class='mt-1 text-white bg-gray-900'><span class='text-green-600 font-bold mr-1'>Yay!</span> {$message}</div>");
    }
    public function decorateLetter(string $letter, $position, $answer)
    {

        $currentWordCollection = collect(str_split(trim($answer)));
        $style="gray";
        if($currentWordCollection->contains($letter))
        {
            $style="yellow";

            if($currentWordCollection->get($position) === $letter)
            {
                $style="green";
            }
        }


        return "<span class='ml-1 bg-{$style}-700 pl-1 pr-1'>{$letter}</span>";
    }
    public function askForGuess() : string|null
    {

        return ask(<<<HTML
            <span class="mt-2 mr-1 bg-green px-1 text-black">
                Guess a 5 letter word:
            </span>
        HTML);
    }
    public function showRow(string $guess, bool $empty=false)
    {
        $collection = collect(str_split(trim($guess)));
        if($empty)
        {
            $collection=collect([" ", " "," "," "," "]);
        }
        $output = collect();
        $collection->each(fn($letter, $index) => $output->push($this->decorateLetter($letter, $index, $this->answer)));
        $output->prepend("<div class='mt-1'>");
        $output->push('</div>');
        render($output->implode(''));
    }

    public function showRemaining(int $remaining)
    {
        render("<div class='text-white mt-1'><span class='px-1 bg-slate-500'>Remaining Guesses:</span> {$remaining}</div>");
    }
}
