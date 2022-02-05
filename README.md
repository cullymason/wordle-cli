# Wordle CLI

> This is just a proof of concept of a cli version of [Wordle](https://www.powerlanguage.co.uk/wordle/)

In short, I wanted to play around with [TermWind](https://github.com/nunomaduro/termwind) and recreating the popular wordle game seemed like a good way to test it out

## Current Functionality

### "installation"

1. clone this repo
2. run `php wordle-cli`

### Guess Validation
* Must be 6 letters
* Cannot contain spaces
* must be a word
* cannot be a previous guess
* displays if the letters of the guess are contained in the target word and are in the same position

### Todo
* Exit the cli if the correct word is guessed or too many guesses
* add `--share` option that mimics the real wordle
