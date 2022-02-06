<?php

namespace App\Commands;
use App\WordleDecorator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use LaravelZero\Framework\Commands\Command;
use function Termwind\{terminal};
use App\WordleValidator;


class Play extends Command
{
    public int $maximumGuesses = 6;
    private string $currentWord = "stair";
    private Collection $guesses;
    private string $startDate = "2022-02-05";
    private int $startWordle = 231;
    private string $gameStatus = "inProgress";
    private $validator;
    private $decorator;
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
        $this->setAnswer();
        $this->validator = new WordleValidator();
        $this->decorator = new WordleDecorator();
        $this->decorator->answer = $this->currentWord;

    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Collection $guesses)
    {

        while($this->gameStatus === "inProgress")
        {
            terminal()->clear();

            if($this->guessesRemaining() < 1)
            {
                $this->gameStatus = "fail";
                break;
            }

            $this->showBoard();
            $this->decorator->showRemaining($this->guessesRemaining());
            if(Cache::has('errorMessage'))
            {
                $this->decorator->showError(Cache::get('errorMessage'));
            }

            $guess = $this->decorator->askForGuess();
            $isValid = $this->validator->validateGuess($guess, $this->guesses);
            if($isValid)
            {
                $this->submitGuess($guess);
            }



            if($this->currentWord === $guess)
            {
                $this->gameStatus = "victory";

                break;
            }

        }
        terminal()->clear();
        $this->showBoard();
        if($this->gameStatus == "fail")
        {
            $this->decorator->showError("Sorry, you are out of guesses");
        }
        if($this->gameStatus === "victory")
        {

            $this->decorator->showSuccess('You did it!');
        }
    }
    
    private function setAnswer()
    {
        $answers = collect(config('answers'));
        $startDate = Carbon::parse($this->startDate);
        $diff = $startDate->diffInDays(Carbon::now());
        $this->startWordle = $this->startWordle+$diff;
        $this->currentWord=$answers->get($this->startWordle);

    }


    private function submitGuess(string $guess)
    {
        $this->guesses->push($guess);
    }


    private function guessesRemaining() : int
    {
        return $this->maximumGuesses - $this->guesses->count();
    }

    private function showBoard()
    {
        $this->guesses->each(fn($word) => $this->decorator->showRow($word));

        if($this->guessesRemaining() > 0)
        {
            foreach(range(1,$this->guessesRemaining()) as $blankRow)
            {
                $this->decorator->showRow("",true);
            }
        }
    }
}
