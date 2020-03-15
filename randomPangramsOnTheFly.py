#!/usr/bin/env python3

# Names: Sophia Trump
# File: randomPangramsOnTheFly.py
# Description: Generates a puzzle on the fly. Takes a cleaned dictionary file and
# generates puzzles by selecting 4 random consonants and 2 random vowels, giving
# each of these 7 random letters the chance to be the key letter. Breaks once
# a puzzle is successfully generated.

import sys
import random
import timeit
import string

# change to set so lookup time is O(1) instead of O(n)
def getViablePangrams(words):
    pangrams = set()

    for word in words:
        # is it a pangram?
        if(len(set(word)) == 7):
            # don't include if the set is in there already
            if(set(word) in pangrams):
                continue
            else:
                # must be frozenset not set so that it is immutable and can be added to the set
                pangrams.add(frozenset(word))
    return pangrams

def getPuzzleWords(pangram, dictionary, keyLetter):
    listAnswers = []
    listPangrams = []

    # go through each word in the dictionary and append
    # words that can be made from the puzzle letters
    # (i.e., equivalent sets or subsets of the puzzle letters)
    for dictionaryWord in dictionary:
        # only take words that have the key letter
        if(keyLetter in dictionaryWord):
            # if the letters in the dictionary word have
            # all the letters in the puzzle (PANGRAM!)
            # NOTE: put this first so can keep track of pangrams
            if(set(dictionaryWord) == set(pangram)):
                listPangrams.append(dictionaryWord)
                listAnswers.append(dictionaryWord)
            # if the letters in the dictionary word are a subset of
            # the letters in the puzzle
            elif(set(dictionaryWord).issubset(set(pangram))):
                listAnswers.append(dictionaryWord)

    return (listAnswers, listPangrams)

def getRandomLetters():
    CONSONANT_LIST = ['q','w','r','t','p','s','d','f','g','h','j','k','l','m','n','b','v','c','x', 'z']
    VOWEL_LIST = ['a','e','i','o','u', 'y']

    # pick 5 random consonants and 2 random vowels
    randomConsonants = random.sample(CONSONANT_LIST,5)
    randomVowels = random.sample(VOWEL_LIST,2)
    # combine the random selections
    randomSeven = randomConsonants + randomVowels
    return randomSeven

def generatePuzzles(dictionaryFile):
    f = open("RANDOMALLPUZZLES.txt", "w+")

    # keep track of how many puzzles were created
    numSuccessfulPuzzles = 0

    # get the list of words from the dictionary file
    listWords = open(dictionaryFile).read().split('\n')

    # get the pangrams for the dictionaryFile
    actualPangrams = getViablePangrams(listWords);

    # keep track of the failed guess attempts
    failedGuessAttempts = set()

    for i in range(0, 1000000, 1):
        randLetters = getRandomLetters()
        # check it has at least 7 unique letters first
        if len(set(randLetters)) != 7:
            continue
        else:
            # only add the random 7 letters if it is a pangram
            # (each element in actualPangrams is a frozenset so search with the set of the random letters)
            # also, frozenset('abc') == set('abc')
            if set(randLetters) in actualPangrams:
                # give each letter a chance to be the key letter
                for letter in set(randLetters):
                    (puzzleAnswers, puzzlePangrams) = getPuzzleWords(set(randLetters), listWords, letter)
                    # check it yields between 10 and 50 words
                    # and that it created at least 1 pangram
                    if((len(puzzleAnswers)) < 15 or \
                       (len(puzzleAnswers)) > 50 or \
                       len(puzzlePangrams) == 0):
                        continue
                    else:
                        numSuccessfulPuzzles += 1

                        # write the relevant data to a file
                        f.write("PUZZLE LETTERS: " + str(set(randLetters)) + "\n")
                        f.write("KEY LETTER: " + letter + "\n\n")
                        f.close()

                        # we found 1 puzzle successfully, now we are done!
                        return numSuccessfulPuzzles

def main():
    if(len(sys.argv) != 2):
        print("Invalid number of command line arguments.")
        exit(1)
    else:
        start = timeit.default_timer()
        numPuzzlesCreated = generatePuzzles(sys.argv[1])
        # was it unsuccessful in generating any puzzles?
        if(numPuzzlesCreated < 1):
            print("RANDOM PANGRAMS: UNABLE TO GENERATE A PUZZLE.")
            exit(1)
        stop = timeit.default_timer()
        print('Total run time (seconds): ', stop - start)

if __name__ == "__main__":
    main()
