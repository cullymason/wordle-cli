<?php

namespace App;
use Illuminate\Support\Collection;
use JetBrains\PhpStorm\Pure;
use function Termwind\render;
use App\WordleDecorator;

class WordleValidator
{
    public WordleDecorator $decorator;
    const GUESS_LENGTH = 5;

    public function __construct()
    {
        $this->decorator = new WordleDecorator();
    }



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
            $this->decorator->showError($errorMessage);
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

}
