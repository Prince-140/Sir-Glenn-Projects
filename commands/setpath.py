#!/usr/bin/env python3
"""
SETPATH - Adds directory to system PATH.
Usage: setpath <folder> [system]
"""

import sys
import os
import winreg
from pathlib import Path

def main():
    if len(sys.argv) < 2 or sys.argv[1] in ['--help', '-h', 'help']:
        print("SETPATH - Add directory to PATH")
        print("Usage:")
        print("  setpath <folder>        - Add to user PATH")
        print("  setpath <folder> system - Add to system PATH (Admin needed)")
        return 0
    
    folder = Path(sys.argv[1]).absolute()
    scope = 'user'
    
    if 'system' in sys.argv:
        scope = 'system'
    
    if not folder.exists():
        print(f"Error: Folder '{folder}' not found.")
        return 1
    
    print(f"Adding: {folder}")
    print(f"Scope: {scope}")
    
    try:
        if scope == 'user':
            key = winreg.OpenKey(winreg.HKEY_CURRENT_USER, 'Environment', 0, winreg.KEY_READ | winreg.KEY_WRITE)
        else:
            key = winreg.OpenKey(winreg.HKEY_LOCAL_MACHINE, r'SYSTEM\CurrentControlSet\Control\Session Manager\Environment', 0, winreg.KEY_READ | winreg.KEY_WRITE)
        
        try:
            current_path, _ = winreg.QueryValueEx(key, 'Path')
        except WindowsError:
            current_path = ''
        
        paths = [p.strip() for p in current_path.split(';') if p.strip()]
        
        if str(folder) in paths:
            print(f"\nAlready in {scope} PATH.")
            winreg.CloseKey(key)
            return 0
        
        if current_path:
            new_path = f"{current_path};{folder}"
        else:
            new_path = str(folder)
        
        winreg.SetValueEx(key, 'Path', 0, winreg.REG_EXPAND_SZ, new_path)
        winreg.CloseKey(key)
        
        print(f"\n✓ Added to {scope} PATH.")
        print("Restart Command Prompt for changes to take effect.")
        
    except PermissionError:
        print("\n✗ Permission denied. Run as Administrator for system PATH.")
        return 1
    except Exception as e:
        print(f"\n✗ Error: {e}")
        return 1
    
    return 0

if __name__ == "__main__":
    sys.exit(main())