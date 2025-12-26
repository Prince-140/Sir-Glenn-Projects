#!/usr/bin/env python3
"""
CLEANDIR - Organizes files in a directory into subfolders based on file extension.
Usage: cleandir <PATH>
       cleandir <PATH> copy
       cleandir <PATH> copy excl .tmp .log
"""

import os
import sys
import shutil
from pathlib import Path

def main():
    # Check for help
    if len(sys.argv) < 2 or sys.argv[1] in ['--help', '-h', 'help']:
        print("CLEANDIR - Organizes files by extension")
        print("Usage:")
        print("  cleandir <folder>                     - Move files to extension folders (DEFAULT)")
        print("  cleandir <folder> copy                - Copy files instead of moving")
        print("  cleandir <folder> copy excl .tmp .log - Copy files, exclude certain extensions")
        print("  cleandir <folder> excl .tmp .log      - Move files, exclude certain extensions")
        print("\nExamples:")
        print("  cleandir C:\\Downloads")
        print("  cleandir C:\\Temp copy")
        print("  cleandir . excl .tmp .bak")
        print("  cleandir . copy excl .tmp .bak")
        return 0
    
    # Get directory path
    directory_path = sys.argv[1]
    
    # Default options
    copy_files = False  # Changed default to False (move by default)
    exclude_extensions = []
    
    # Parse arguments
    args = sys.argv[2:]
    
    # Check for 'copy' keyword
    if 'copy' in args:
        copy_files = True
        # Remove 'copy' from args to simplify parsing
        args = [arg for arg in args if arg != 'copy']
    
    # Check for excluded extensions (now 'excl' might be at position 0 in args if 'copy' was removed)
    if 'excl' in args:
        excl_index = args.index('excl') + 1
        while excl_index < len(args):
            ext = args[excl_index]
            if not ext.startswith('.'):
                ext = '.' + ext
            exclude_extensions.append(ext.lower())
            excl_index += 1
    
    directory = Path(directory_path)
    
    if not directory.exists():
        print(f"Error: Directory '{directory_path}' does not exist.")
        return 1
    
    if not directory.is_dir():
        print(f"Error: '{directory_path}' is not a directory.")
        return 1
    
    # Collect all files
    files = [f for f in directory.iterdir() if f.is_file()]
    
    if not files:
        print(f"No files found in '{directory_path}'.")
        return 0
    
    print(f"Organizing: {directory_path}")
    print(f"Files found: {len(files)}")
    print(f"Mode: {'Copy' if copy_files else 'Move'}")
    
    if exclude_extensions:
        print(f"Excluding: {', '.join(exclude_extensions)}")
    
    print("-" * 50)
    
    organized = 0
    excluded = 0
    
    for file_path in files:
        # Check if extension is excluded
        if file_path.suffix.lower() in exclude_extensions:
            print(f"SKIP: '{file_path.name}' (excluded)")
            excluded += 1
            continue
        
        # Get extension (or 'no_extension' for files without extension)
        ext = file_path.suffix[1:] if file_path.suffix else "no_extension"
        folder_name = f"{ext}_files"
        folder_path = directory / folder_name
        
        # Create folder
        folder_path.mkdir(exist_ok=True)
        
        # Destination path
        destination = folder_path / file_path.name
        
        # Handle duplicates
        counter = 1
        while destination.exists():
            new_name = f"{file_path.stem}_{counter}{file_path.suffix}"
            destination = folder_path / new_name
            counter += 1
        
        # Move or copy
        try:
            if copy_files:
                shutil.copy2(str(file_path), str(destination))
                action = "COPIED"
            else:
                shutil.move(str(file_path), str(destination))
                action = "MOVED"
            
            print(f"{action}: {file_path.name} → {folder_name}/")
            organized += 1
            
        except Exception as e:
            print(f"ERROR: {file_path.name} - {e}")
    
    print("-" * 50)
    print(f"SUMMARY: Organized {organized}, Excluded {excluded}, Total {len(files)}")
    
    return 0

if __name__ == "__main__":
    sys.exit(main())