@echo off
REM IPGET - Displays the machine's external (public) IP address
REM Usage: ipget [/detail brief|full] [/service ipify|icanhazip]

python "C:\MyDosUtils\commands\ipget.py" %*