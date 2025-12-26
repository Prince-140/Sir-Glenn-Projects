@echo off
REM QRGEN - Takes a text string and generates a QR code image file
REM Usage: qrgen <TEXT> <OUTPUT_FILE> [/size 200] [/fill black] [/back white]

python "C:\MyDosUtils\commands\qrgen.py" %*