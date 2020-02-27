#!/usr/bin/env python3

import sys
import re
import os.path
from os import path

def getPuzzleWords(pangram, dictionary, keyLetter):
    listAnswers = []
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
                listAnswers.append(dictionaryWord)
            # if the letters in the dictionary word are a subset of
            # the letters in the puzzle
            elif(set(dictionaryWord).issubset(set(pangram))):
                listAnswers.append(dictionaryWord)

    return (listAnswers)

def getViablePangrams(words):
    pangrams = []

    for word in words:
        numVowels = 0
        uncommonLetters = 0
        # is it a pangram?
        if(len(set(word)) == 7):
            # don't include if the set is in there already
            if(set(word) in pangrams):
                continue
            else:
                pangrams.append(set(word))
    return pangrams

def generatePuzzles(dictionaryFile):

    # Start with clean file each time
    if path.exists("puzzleData.txt"):
        os.remove("puzzleData.txt")

    f = open("puzzleData.txt", "w+")

    # find the sum of the answers so can calculate the avg number of answers
    # per puzzle attempt
    numPuzzlesCreated = 0
    numSuccessfulPuzzles = 0

    # get the list of words from the dictionary file
    listWords = open(dictionaryFile).read().split('\n')

    # create a list of pangrams such that they seem like
    # they would create a viable puzzle
    viablePangramList = getViablePangrams(listWords)

    # get all the words that can be made from each of the pangram letters
    for pangram in viablePangramList:
        # give each letter a chance to be the key letter
        for keyLetter in pangram:
            puzzleSolutions = getPuzzleWords(pangram, listWords, keyLetter)
            numPuzzlesCreated += 1
            print(numPuzzlesCreated, '/', 7*len(viablePangramList))
            # check it yields between 10 and 50 words
            if((len(puzzleSolutions)) < 15 or \
               (len(puzzleSolutions)) > 50):
                continue
            else:
                numSuccessfulPuzzles += 1
                f.write('#')
                f.write('\n')
                f.write(''.join(pangram))
                f.write('\n')
                f.write(str(keyLetter))
                f.write('\n')
                f.write('\n'.join(puzzleSolutions))
                f.write('\n')


    f.close()

    print(numSuccessfulPuzzles, "puzzles successfully created.")

def main():
    if(len(sys.argv) != 2):
        print("Invalid number of command line arguments.")
        exit(1)
    else:
        generatePuzzles(sys.argv[1])

if __name__ == "__main__":
    main()
