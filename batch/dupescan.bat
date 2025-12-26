@echo off
REM DUPESCAN - Scans a directory path and reports all files with identical content
REM Usage: dupescan <PATH> [/hash md5|sha1|sha256] [/delete]

python "C:\MyDosUtils\commands\dupescan.py" %*