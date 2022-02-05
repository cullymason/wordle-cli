<?php

namespace App\Commands;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\BrowserKit\AbstractBrowser;
use function Termwind\{render, ask, terminal};



class Play extends Command
{
    public int $maximumGuesses = 6;
    private string $currentWord = "stair";
    private Collection $guesses;
    private Collection $answers;
    private string $startDate = "2022-02-05";
    private int $startWordle = 231;
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'play';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Starts wordle game';

    public function __construct()
    {
        parent::__construct();
        $this->guesses= collect();
        $this->answers=collect(config('answers'));
        $this->setAnswer();

    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        dd($this->currentWord);

        terminal()->clear();
        render("<div class='p-4 bg-blue-600 text-white'>Wordle CLI</div>");
        $this->showBoard();
        while(sizeof($this->guesses) <= $this->maximumGuesses)
        {

            $guess = $this->askForGuess();

            $isValid = $this->validateGuess($guess);

            if($isValid)
            {
                $this->submitGuess($guess);
            }

            $this->showBoard();

        }
    }


    private function setAnswer()
    {
        $startDate = Carbon::parse($this->startDate);
        $currentDate = Carbon::now()->addDay(3);

        $diff = $startDate->diffInDays($currentDate);

        $this->currentWord=$this->answers->get($this->startWordle);

    }
    private function askForGuess() : string|null
    {
        return ask(<<<HTML
            <span class="mt-2 mr-1 bg-green px-1 text-black">
                Guess a 5 letter word:
            </span>
        HTML);
    }

    private function submitGuess(string $guess)
    {
        $this->guesses->push($guess);
    }
    private function decorateLetter(string $letter, $position)
    {

        $currentWordCollection = collect(str_split(trim($this->currentWord)));
        $style="gray";
        if($currentWordCollection->contains($letter))
        {
            $style="yellow";

            if($currentWordCollection->search($letter) === $position)
            {
                $style="green";
            }
        }


        return "<span class='ml-1 bg-{$style}-700 pl-1 pr-1'>{$letter}</span>";
    }

    private function guessesRemaining() : int
    {
        return $this->maximumGuesses - $this->guesses->count();
    }
    private function showRow(string $word, bool $empty=false)
    {
        $collection = collect(str_split(trim($word)));
        if($empty)
        {
            $collection=collect([" ", " "," "," "," "]);
        }
        $output = collect();
        $collection->each(fn($letter, $index) => $output->push($this->decorateLetter($letter, $index)));
        $output->prepend("<div class='mt-1'>");
        $output->push('</div>');
        render($output->implode(''));
    }
    private function showBoard()
    {
        $this->guesses->each(fn($word) => $this->showRow($word));

        foreach(range(1,$this->guessesRemaining()) as $blankRow)
        {
              $this->showRow("",true);
        }
    }
    private function validateGuess(string $guess) : bool
    {
        if(str_contains($guess, ' '))
        {
            $this->error("word cannot contain spaces.");
            return false;
        }
        $guessArray = str_split(trim($guess));

        if(sizeof($guessArray) < 5)
        {
            $this->error('That guess is not long enough');
            return false;
        }

        if(sizeof($guessArray) > 5)
        {
            $this->error('That guess is too long');
            return false;
        }
        // check if valid word
        $pspell = pspell_new("en");

        if(! pspell_check($pspell, $guess))
        {
            $this->error('That is not a valid word');
            return false;
        }
        if($this->guesses->contains($guess))
        {
            $this->error('You already guessed that');
            return false;
        }

        return true;
    }
}
