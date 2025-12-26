@echo off
REM LOGPARSE - Reads a log file and extracts lines matching a severity level or time range
REM Usage: logparse <LOG_FILE> [/level ERROR|WARN|INFO] [/time "2024-01-01 00:00:00"]

python "C:\MyDosUtils\commands\logparse.py" %*