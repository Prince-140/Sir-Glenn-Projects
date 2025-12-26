#!/usr/bin/env python3
"""
TEXTFIND - Search text in files.
Usage: textfind <text> [path .] [case sensitive]
"""

import sys
import os
from pathlib import Path

def main():
    if len(sys.argv) < 2 or sys.argv[1] in ['--help', '-h', 'help']:
        print("TEXTFIND - Search text in files")
        print("Usage:")
        print("  textfind <text>")
        print("  textfind <text> path C:\\Search")
        print("  textfind <text> case sensitive")
        return 0
    
    search_text = sys.argv[1]
    search_path = "."
    case_sensitive = False
    
    # Parse arguments
    i = 2
    while i < len(sys.argv):
        arg = sys.argv[i]
        if arg == 'path' and i + 1 < len(sys.argv):
            search_path = sys.argv[i + 1]
            i += 2
        elif arg == 'case' and i + 1 < len(sys.argv) and sys.argv[i + 1] == 'sensitive':
            case_sensitive = True
            i += 2
        else:
            i += 1
    
    path = Path(search_path)
    if not path.exists():
        print(f"Error: Path '{search_path}' not found.")
        return 1
    
    print(f"Searching for: '{search_text}'")
    print(f"Path: {search_path}")
    print(f"Case: {'sensitive' if case_sensitive else 'insensitive'}")
    print("-" * 60)
    
    # Supported text extensions
    text_exts = {'.txt', '.py', '.js', '.html', '.css', '.json', '.xml', 
                 '.md', '.csv', '.log', '.ini', '.cfg', '.conf', '.bat', '.ps1'}
    
    matches = 0
    files_searched = 0
    
    for root, dirs, files in os.walk(search_path):
        for file in files:
            file_path = Path(root) / file
            
            # Skip non-text files
            if file_path.suffix.lower() not in text_exts:
                continue
            
            files_searched += 1
            
            try:
                with open(file_path, 'r', encoding='utf-8', errors='ignore') as f:
                    content = f.read()
                
                # Search
                if case_sensitive:
                    found = search_text in content
                else:
                    found = search_text.lower() in content.lower()
                
                if found:
                    matches += 1
                    # Show first line containing match
                    lines = content.split('\n')
                    for line_num, line in enumerate(lines[:10], 1):  # Check first 10 lines
                        if case_sensitive:
                            if search_text in line:
                                preview = line[:100] + "..." if len(line) > 100 else line
                                print(f"{file_path} (line {line_num}): {preview}")
                                break
                        else:
                            if search_text.lower() in line.lower():
                                preview = line[:100] + "..." if len(line) > 100 else line
                                print(f"{file_path} (line {line_num}): {preview}")
                                break
                    
            except Exception as e:
                pass  # Skip unreadable files
    
    print(f"\nFound {matches} matches in {files_searched} files")
    return 0

if __name__ == "__main__":
    sys.exit(main())