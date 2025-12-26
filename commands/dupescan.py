#!/usr/bin/env python3
"""
DUPESCAN - Finds duplicate files by checking their content hash.
Usage: dupescan <folder> [hash] [delete]
       hash: md5, sha1, or sha256 (default: md5)
       delete: remove duplicates (keep first)
"""

import os
import sys
import hashlib
from pathlib import Path

def main():
    # Show help
    if len(sys.argv) < 2 or sys.argv[1] in ['--help', '-h', 'help']:
        print("DUPESCAN - Find duplicate files")
        print("Usage:")
        print("  dupescan <folder>                    - Scan for duplicates")
        print("  dupescan <folder> sha256             - Use SHA256 hash")
        print("  dupescan <folder> delete             - Delete duplicates")
        print("  dupescan <folder> sha256 delete      - Use SHA256 and delete")
        print("\nExamples:")
        print("  dupescan C:\\Photos")
        print("  dupescan C:\\Backup delete")
        print("  dupescan . sha256 delete")
        return 0
    
    # Get folder path
    folder_path = sys.argv[1]
    
    # Default options
    hash_algo = 'md5'
    delete = False
    
    # Parse simple arguments
    if 'sha256' in sys.argv:
        hash_algo = 'sha256'
    elif 'sha1' in sys.argv:
        hash_algo = 'sha1'
    
    if 'delete' in sys.argv:
        delete = True
    
    path = Path(folder_path)
    
    if not path.exists():
        print(f"Error: Folder '{folder_path}' not found.")
        return 1
    
    if not path.is_dir():
        print(f"Error: '{folder_path}' is not a folder.")
        return 1
    
    print(f"Scanning: {folder_path}")
    print(f"Hash: {hash_algo}")
    if delete:
        print("Mode: DELETE duplicates (keeping first)")
    else:
        print("Mode: Report only (no deletion)")
    print("-" * 60)
    
    # Hash files
    hash_map = {}
    file_count = 0
    
    for root, dirs, files in os.walk(folder_path):
        for file in files:
            file_path = Path(root) / file
            try:
                # Calculate hash
                hash_func = hashlib.new(hash_algo)
                with open(file_path, 'rb') as f:
                    while chunk := f.read(8192):
                        hash_func.update(chunk)
                file_hash = hash_func.hexdigest()
                
                # Store in dictionary
                if file_hash not in hash_map:
                    hash_map[file_hash] = []
                hash_map[file_hash].append(file_path)
                file_count += 1
                
            except Exception as e:
                print(f"Error: {file_path.name} - {e}")
    
    print(f"Files scanned: {file_count}")
    print(f"Unique files: {len(hash_map)}")
    
    # Find duplicates
    duplicates = []
    wasted = 0
    
    for file_hash, files in hash_map.items():
        if len(files) > 1:
            duplicates.append((file_hash, files))
            try:
                wasted += files[0].stat().st_size * (len(files) - 1)
            except:
                pass
    
    print(f"Duplicate groups: {len(duplicates)}")
    
    if duplicates:
        print("\nDUPLICATES FOUND:")
        print("=" * 60)
        
        for idx, (file_hash, files) in enumerate(duplicates, 1):
            print(f"\nGroup {idx}: {file_hash[:16]}...")
            files.sort(key=lambda x: len(str(x)))
            
            original = files[0]
            try:
                size = original.stat().st_size
                print(f"  Size: {size:,} bytes")
            except:
                size = 0
            
            print(f"  Original: {original}")
            
            for dup in files[1:]:
                print(f"  Duplicate: {dup}")
                
                if delete:
                    try:
                        dup.unlink()
                        print(f"    [DELETED]")
                    except Exception as e:
                        print(f"    Error: {e}")
        
        print("\n" + "=" * 60)
        print(f"SUMMARY: {len(duplicates)} duplicate groups")
        print(f"Wasted space: {wasted:,} bytes ({wasted/(1024**2):.1f} MB)")
        
        if delete:
            print("Duplicates have been deleted.")
        else:
            print("Use 'delete' argument to remove duplicates.")
    else:
        print("\nNo duplicates found! 🎉")
    
    return 0

if __name__ == "__main__":
    sys.exit(main())