#!/usr/bin/env python3
"""
ZIPARCH - Zip/unzip files.
Usage: ziparch <source> <destination> [password secret] [extract]
"""

import sys
import zipfile
from pathlib import Path

def main():
    if len(sys.argv) < 3 or sys.argv[1] in ['--help', '-h', 'help']:
        print("ZIPARCH - Zip and unzip files")
        print("Usage:")
        print("  ziparch <file> <archive.zip>            - Zip file")
        print("  ziparch <folder> <archive.zip>          - Zip folder")
        print("  ziparch <file> <archive.zip> password secret - Password protect")
        print("  ziparch <archive.zip> <folder> extract  - Extract archive")
        return 0
    
    source = sys.argv[1]
    dest = sys.argv[2]
    password = None
    extract = 'extract' in sys.argv
    
    # Parse password
    i = 3
    while i < len(sys.argv):
        arg = sys.argv[i]
        if arg == 'password' and i + 1 < len(sys.argv):
            password = sys.argv[i + 1].encode('utf-8')
            i += 2
        else:
            i += 1
    
    source_path = Path(source)
    dest_path = Path(dest)
    
    if not source_path.exists():
        print(f"Error: Source '{source}' not found.")
        return 1
    
    if extract:
        print(f"Extracting: {source}")
        print(f"To: {dest}")
        
        if not zipfile.is_zipfile(source):
            print(f"Error: '{source}' is not a zip file.")
            return 1
        
        try:
            dest_path.mkdir(parents=True, exist_ok=True)
            
            with zipfile.ZipFile(source, 'r') as zipf:
                if password:
                    zipf.setpassword(password)
                
                # List contents
                file_list = zipf.namelist()
                print(f"Files: {len(file_list)}")
                
                # Extract
                zipf.extractall(dest)
                print(f"✓ Extracted to: {dest}")
                
        except Exception as e:
            print(f"Error: {e}")
            return 1
    
    else:
        print(f"Creating archive: {dest}")
        print(f"From: {source}")
        if password:
            print("Password protected")
        
        try:
            with zipfile.ZipFile(dest, 'w', zipfile.ZIP_DEFLATED) as zipf:
                if password:
                    zipf.setpassword(password)
                
                if source_path.is_file():
                    zipf.write(source, source_path.name)
                    print(f"Added: {source_path.name}")
                else:
                    # Add directory
                    for file in source_path.rglob('*'):
                        if file.is_file():
                            arcname = file.relative_to(source_path)
                            zipf.write(file, arcname)
                            print(f"Added: {arcname}")
            
            size = dest_path.stat().st_size if dest_path.exists() else 0
            print(f"✓ Created: {dest} ({size:,} bytes)")
            
        except Exception as e:
            print(f"Error: {e}")
            return 1
    
    return 0

if __name__ == "__main__":
    sys.exit(main())