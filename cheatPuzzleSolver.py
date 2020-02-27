#!/bin/python

import sys

puzzleLetters=sys.argv[1]
puzzleLetters=puzzleLetters.lower()
keyLetter=puzzleLetters[0]
dict = sys.argv[2]
dictionary=list(open(dict).read().split('\n'))
listAnswers = []
listPangrams = []

# go through each word in the dictionary and append
# words that can be made from the puzzle letters
# (i.e., equivalent sets or subsets of the puzzle letters)
for dictionaryWord in dictionary:
    #dictionaryWord=dictionaryWord[:-1]
    # only take words that have the key letter
    if(keyLetter in dictionaryWord):
        # if the letters in the dictionary word have
        # all the letters in the puzzle (PANGRAM!)
        # NOTE: put this first so can keep track of pangrams
        if(set(dictionaryWord) == set(puzzleLetters)):
            listPangrams.append(dictionaryWord)
        # if the letters in the dictionary word are a subset of
        # the letters in the puzzle
        elif(set(dictionaryWord).issubset(set(puzzleLetters))):
            listAnswers.append(dictionaryWord)

print("PANGRAMS:\n")
for pangram in listPangrams:
    print(pangram)

print("\nOTHER ANSWERS:\n")
for answer in listAnswers:
     print(answer)
