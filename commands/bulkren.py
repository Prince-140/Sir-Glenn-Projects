#!/usr/bin/env python3
"""
BULKREN - Bulk rename files.
Usage: bulkren <folder> [add prefix suffix] [filter *.txt]
"""

import sys
import os
from pathlib import Path

def main():
    if len(sys.argv) < 2 or sys.argv[1] in ['--help', '-h', 'help']:
        print("BULKREN - Bulk rename files")
        print("Usage:")
        print("  bulkren <folder>")
        print("  bulkren <folder> add PREFIX SUFFIX")
        print("  bulkren <folder> filter *.pdf")
        print("  bulkren <folder> add PREFIX SUFFIX filter *.jpg")
        return 0
    
    folder = sys.argv[1]
    prefix = ""
    suffix = ""
    file_filter = "*"
    
    # Parse arguments
    i = 2
    while i < len(sys.argv):
        arg = sys.argv[i]
        if arg == 'add' and i + 2 < len(sys.argv):
            prefix = sys.argv[i + 1]
            suffix = sys.argv[i + 2]
            i += 3
        elif arg == 'filter' and i + 1 < len(sys.argv):
            file_filter = sys.argv[i + 1]
            i += 2
        else:
            i += 1
    
    path = Path(folder)
    if not path.exists() or not path.is_dir():
        print(f"Error: Folder '{folder}' not found.")
        return 1
    
    # Get matching files
    if file_filter == "*":
        files = [f for f in path.iterdir() if f.is_file()]
    else:
        # Simple pattern matching
        import fnmatch
        pattern = file_filter
        files = []
        for f in path.iterdir():
            if f.is_file() and fnmatch.fnmatch(f.name, pattern):
                files.append(f)
    
    if not files:
        print(f"No files matching '{file_filter}' in '{folder}'.")
        return 0
    
    print(f"Folder: {folder}")
    print(f"Files: {len(files)}")
    print(f"Filter: {file_filter}")
    if prefix or suffix:
        print(f"Rename: {prefix}[filename]{suffix}[extension]")
    print("-" * 50)
    
    if not prefix and not suffix:
        ans = input("No prefix/suffix specified. Continue? (y/n): ")
        if ans.lower() != 'y':
            return 0
    
    renamed = 0
    for f in files:
        new_name = f"{prefix}{f.stem}{suffix}{f.suffix}"
        new_path = path / new_name
        
        if new_path.exists() and new_path != f:
            print(f"SKIP: {f.name} → {new_name} (already exists)")
            continue
        
        try:
            f.rename(new_path)
            print(f"RENAMED: {f.name} → {new_name}")
            renamed += 1
        except Exception as e:
            print(f"ERROR: {f.name} - {e}")
    
    print(f"\nRenamed: {renamed}/{len(files)} files")
    return 0

if __name__ == "__main__":
    sys.exit(main())