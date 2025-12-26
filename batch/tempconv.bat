@echo off
REM TEMPCONV - Converts a temperature between Celsius, Fahrenheit, and Kelvin
REM Usage: tempconv <VALUE> <FROM_UNIT> <TO_UNIT>

python "C:\MyDosUtils\commands\tempconv.py" %*
