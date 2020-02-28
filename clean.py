file = open('puzzlesFlatFile.txt').read().split('\n')

outputFile = open('puzzleData.txt', 'w+')

for line in file:
    line = line.lower()
    outputFile.write(line + '\n')

outputFile.close()
