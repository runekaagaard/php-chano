import sys


for line in open('builtins.txt'):
    sys.stdout.write("     * "  + line)
