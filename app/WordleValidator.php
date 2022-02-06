<?php

namespace App;
use Illuminate\Support\Collection;
use JetBrains\PhpStorm\Pure;
use function Termwind\render;


class WordleValidator
{

    const GUESS_LENGTH = 5;


    /**
     * @param string|null $guess
     * @param Collection $guesses
     *
     * @return bool
     */
    public function validateGuess(string|null $guess, Collection $guesses) : bool
    {
        $errorMessage = match(true) {
            $guess === null => "You forgot to guess",
            $this->containsSpaces($guess) => "word cannot contain spaces.",
            $this->tooShort($guess) =>'That guess is not long enough',
            $this->tooLong($guess) => 'That guess is too long',
            $this->invalidWord($guess) => 'That is not a valid word',
            $guesses->contains($guess) => 'You already guessed that',
            default => null
        };

        if($errorMessage !== null)
        {
            $this->showError($errorMessage);
            return false;
        }

        return true;
    }


    /**
     * @param string $guess
     *
     * @return bool
     */
    private function containsSpaces(string $guess) : bool
    {
        return str_contains($guess, ' ');
    }

    /**
     * @param string $guess
     *
     * @return bool
     */
    #[Pure] private function tooShort(string $guess) : bool
    {
        return  sizeof($this->toArray($guess)) < self::GUESS_LENGTH;
    }

    /**
     * @param string $guess
     *
     * @return bool
     */
    #[Pure] private function tooLong(string $guess) : bool
    {
        return  sizeof($this->toArray($guess)) > self::GUESS_LENGTH;
    }

    /**
     * @param string $guess
     *
     * @return bool
     */
    private function invalidWord(string $guess) :bool
    {
        $pspell = pspell_new("en");
        return ! pspell_check($pspell, $guess);
    }

    /**
     * @param string $guess
     *
     * @return array
     */
    private function toArray(string $guess) : array
    {
        return str_split(trim($guess));
    }

    /**
     * @param string $message
     *
     * @return void
     */
    private function showError(string $message)
    {
        render("<div class='mt-1 text-white bg-gray-900'><span class='text-red-600 font-bold mr-1'>Whoops</span> {$message}</div>");
    }
}
