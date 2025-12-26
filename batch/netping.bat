@echo off
REM NETPING - Pings a list of network hosts and reports status and latency
REM Usage: netping <HOST1> [HOST2 ...] [/timeout 1000] [/count 4]

python "C:\MyDosUtils\commands\netping.py" %*