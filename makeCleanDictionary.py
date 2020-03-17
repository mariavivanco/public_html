#!/usr/bin/env python3

# Names: Sophia Trump, Eunsoo Jang, Maria Vivanco, Emily Lobel
# File: makeCleanDictionary.py
# Description: Takes 3 dictionary files and cleans them, saving the mega combined cleaned dictionary
# into a file called "cleanedDictionary.txt". 
# Run in the cmd with python3 makeCleanDictionary.py <path to dictionary file>

import sys
import re

def makeCleanDictionary(dictionaryFile1, dictionaryFile2, dictionaryFile3):
    # create a list of words from the dictionary, which is delimited by newline
    listUnixDictionaryWords = open(dictionaryFile1).read().split('\n')
    listScrabbleWords = open(dictionaryFile2).read().split('\n')
    listGitWords = open(dictionaryFile3).read().split('\n')
    print("Checking", len(listUnixDictionaryWords), "words from dictionary 1...")
    print("Checking", len(listScrabbleWords), "words from dictionary 2...")
    print("Checking", len(listGitWords), "words from dictionary 3...")
    # create common dictionary without repeats
    dictionaryWordsNoRepeats = list(set(listUnixDictionaryWords) | set(listScrabbleWords) | set(listGitWords))

    # create counters for use in print statements below,
    # used for general info
    numPangrams = 0
    numPerfectPangrams = 0
    numIgnoredWords = 0
    numWordsInCleanedDict = 0

    # open the files to write the cleaned dictionary in,
    # as well as (for bookkeeping) files for ignored words and words with
    # exactly 7 unique letters 
    f = open("cleanedDictionary.txt", "w+")

    # go through each word in the dictionary
    # and append those that meet the rules of
    # a 'cleaned' word (no proper nouns, special characters, less than 4 letters)
    for messyWord in dictionaryWordsNoRepeats:
        # Less than 4 letters?
        if(len(messyWord) < 4):
            numIgnoredWords += 1
        # Special characters? (non alphabetic)
        elif(re.search('[^a-zA-Z]', messyWord) is not None):
            numIgnoredWords += 1
        # Proper noun?
        elif(re.match('[A-Z]', messyWord) is not None):
            numIgnoredWords += 1
        # Passes the above, so is a viable word.
        # Add it to the cleaned dictionary
        else:
            f.write(messyWord + '\n')
            numWordsInCleanedDict += 1
            # Comprised of exactly 7 unique letters?
            if(len(set(messyWord)) == 7):
               numPangrams += 1
               # Is it a perfect pangram?
               if(len(messyWord) == 7):
                   numPerfectPangrams +=1
                   
    # close the files
    f.close()

    # print how many words were ignored for the cleaned dictionary
    print(f"{numIgnoredWords} words cleaned from the original files.")

    # print how many words were added to the dictionary
    print(f"The cleaned dictionary has {numWordsInCleanedDict} words.")

    # now, print how many words there are in the dictionary
    # with exactly 7 unique letters! (i.e., that are pangrams)
    print(f"The cleaned dictionary has {numPangrams} pangrams.")

    # print the number of perfect pangrams
    print(f"The cleaned dictionary has {numPerfectPangrams} perfect pangrams.")


def main():
    # there should be 4 arguments (3 param, 1 name of the program)
    if(len(sys.argv) != 4):
        print("Invalid number of command line arguments.")
        exit(1)
    else:
        makeCleanDictionary(sys.argv[1], sys.argv[2], sys.argv[3])

if __name__ == "__main__":
    main()
