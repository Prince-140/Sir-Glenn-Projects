#!/usr/bin/env python3
"""
FILETOUCH - Creates or updates file timestamp.
Usage: filetouch <file> [update]
"""

import sys
import os
from pathlib import Path
import datetime

def main():
    if len(sys.argv) < 2 or sys.argv[1] in ['--help', '-h', 'help']:
        print("FILETOUCH - Create or update file")
        print("Usage:")
        print("  filetouch <file>     - Create file or update timestamp")
        print("  filetouch <file> update - Only update existing file")
        return 0
    
    filename = sys.argv[1]
    update_only = 'update' in sys.argv
    
    path = Path(filename)
    
    if path.exists():
        if update_only:
            # Update timestamp only
            current_time = datetime.datetime.now().timestamp()
            os.utime(filename, (current_time, current_time))
            print(f"✓ Updated timestamp: {filename}")
        else:
            # Update timestamp
            current_time = datetime.datetime.now().timestamp()
            os.utime(filename, (current_time, current_time))
            print(f"✓ Updated: {filename}")
    else:
        if update_only:
            print(f"Error: File '{filename}' doesn't exist.")
            print("Remove 'update' to create it.")
            return 1
        
        # Create new file
        try:
            path.parent.mkdir(parents=True, exist_ok=True)
            path.touch()
            print(f"✓ Created: {filename}")
            print(f"  Path: {path.absolute()}")
        except Exception as e:
            print(f"Error: {e}")
            return 1
    
    # Show file info
    if path.exists():
        stat = path.stat()
        created = datetime.datetime.fromtimestamp(stat.st_ctime)
        modified = datetime.datetime.fromtimestamp(stat.st_mtime)
        print(f"  Size: {stat.st_size:,} bytes")
        print(f"  Created: {created.strftime('%Y-%m-%d %H:%M:%S')}")
        print(f"  Modified: {modified.strftime('%Y-%m-%d %H:%M:%S')}")
    
    return 0

if __name__ == "__main__":
    sys.exit(main())