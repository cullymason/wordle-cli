
![Wordle CLI Banner](https://banners.beyondco.de/Wordle%20CLI.png?theme=light&packageManager=&packageName=composer+global+require+cullymason%2Fwordle-cli&pattern=hideout&style=style_1&description=Wordle+in+your+terminal&md=1&showWatermark=0&fontSize=100px&images=chat&widths=auto)
> This is just a proof of concept of a cli version of [Wordle](https://www.powerlanguage.co.uk/wordle/)

In short, I wanted to play around with [TermWind](https://github.com/nunomaduro/termwind) and recreating the popular wordle game seemed like a good way to test it out

## Current Functionality

### Installation

 `composer global require cullymason/wordle-cli`

### Guess Validation
* Must be 6 letters
* Cannot contain spaces
* must be a word
* cannot be a previous guess
* displays if the letters of the guess are contained in the target word and are in the same position

### Todo
* add `--share` option that mimics the real wordle
