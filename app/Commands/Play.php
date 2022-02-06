<?php

namespace App\Commands;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use LaravelZero\Framework\Commands\Command;
use function Termwind\{render, ask, terminal};
use App\WordleValidator;


class Play extends Command
{
    public int $maximumGuesses = 6;
    private string $currentWord = "stair";
    private Collection $guesses;
    private Collection $answers;
    private string $startDate = "2022-02-05";
    private int $startWordle = 231;
    private string $gameStatus = "inProgress";
    private $validator;
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
        $this->validator = new WordleValidator();

    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {


        terminal()->clear();


        if($this->gameStatus === "inProgress")
        {
            $this->showBoard();
        }

        while($this->gameStatus === "inProgress")
        {

            if($this->guessesRemaining() < 1)
            {
                $this->gameStatus = "failure";
                $this->error('Sorry, you are out of guesses');
                break;
            }
            $guess = $this->askForGuess();
            $isValid = $this->validator->validateGuess($guess, $this->guesses);

            if($isValid)
            {
                $this->submitGuess($guess);
            }

            $this->showBoard();

            if($this->currentWord === $guess)
            {
                $this->gameStatus = "victory";
                $this->line('You did it!');
                break;
            }

        }
    }


    private function setAnswer()
    {
        $startDate = Carbon::parse($this->startDate);

        $diff = $startDate->diffInDays(Carbon::now());
        $this->startWordle = $this->startWordle+$diff;
        $this->currentWord=$this->answers->get($this->startWordle);

    }
    private function askForGuess() : string|null
    {
        render("<div class='text-white mt-1'><span class='px-1 bg-slate-500'>Remaining Guesses:</span> {$this->guessesRemaining()}</div>");
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

            if($currentWordCollection->get($position) === $letter)
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

        if($this->guessesRemaining() > 0)
        {
            foreach(range(1,$this->guessesRemaining()) as $blankRow)
            {
                $this->showRow("",true);
            }
        }
    }


    private function showError(string $message)
    {
        render("<div class='mt-1 text-white bg-gray-900'><span class='text-red-600 font-bold mr-1'>Whoops</span> {$message}</div>");
    }
}
