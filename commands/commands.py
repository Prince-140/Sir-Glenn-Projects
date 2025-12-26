#!/usr/bin/env python3
"""
CUSTOMHELP - Displays information about all available MyDosUtils commands.
Usage: customhelp
"""

import sys



def print_help():
    """Display formatted table of all available commands."""
    commands = [
        ("Files and Directory Commands",""),
        ("",""),
        ("CLEANDIR", "-- Organizes files in a directory into subfolders based on file extension."),
        ("FILETOUCH", "-- Creates an empty file or updates the timestamp of an existing file."),
        ("BULKREN", "-- Renames multiple files in a directory using a pattern."),
        ("TEXTFIND", "-- Searches through files in a directory for a specific text string or regular expression."),
        ("DUPESCAN", "-- Scans a directory path and reports all files with identical content (hashes)."),
        ("ZIPARCH", "-- Zips or unzips a file or directory with an optional password."),
        ("CSVSORT", "-- Sorts the rows of a specified CSV file based on a given column name."),
        ("ENCDEC", "-- Simple command-line tool to encrypt or decrypt a text file using a key."),
         ("",""),
        ("System Related Commands",""),
       ("",""),
        ("SYSINFO", "-- Displays a detailed report of the current operating system and environment."),
        ("SETPATH", "-- Adds a specified directory permanently to the system's PATH environment variable."),
        ("APPTERM", "-- Gracefully or forcefully ends a process by name."),
        ("NETPING", "-- Pings a list of network hosts and reports status and latency."),
        ("IPGET", "-- Displays the machine's external (public) IP address."),
        ("DISKFREE", "-- Displays used, free, and total space for all mounted drives."),
         ("",""),
         ("Other tools",""),
         ("",""),
        ("PDF2TXT", "-- Converts a specified PDF file into a plain text file."),
        ("TODO", "-- A simple command to add, list, and mark tasks as done."),
        ("RANDPASS", "-- Generates a random, secure password of a specified length."),
        ("QRGEN", "-- Takes a text string (like a URL) and generates a QR code image file."),
        ("TEMPCONV", "-- Converts a temperature between Celsius, Fahrenheit, and Kelvin."),
        ("LOGPARSE", "-- Reads a log file and extracts lines matching a severity level or time range."),
    ]
    
    print("\n" + "="*80)
    print("EXTERAL COMMANDS".center(80))
    print("="*80)
    print("\n{:<12} {}".format("COMMAND", "DESCRIPTION"))
    print("{:<12} {}".format("-------", "-----------"))
    
    for cmd, desc in commands:
        print("{:<12} {}".format(cmd, desc))
    
    


def main():
    """Main entry point for the customhelp command."""
    print_help()
    return 0

if __name__ == "__main__":
    sys.exit(main())