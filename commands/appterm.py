#!/usr/bin/env python3
"""
APPTERM - Terminates processes.
Usage: appterm <process_name> [force]
"""

import sys
import psutil

def main():
    if len(sys.argv) < 2 or sys.argv[1] in ['--help', '-h', 'help']:
        print("APPTERM - Terminate processes")
        print("Usage:")
        print("  appterm <name>      - Gracefully terminate")
        print("  appterm <name> force - Force terminate")
        return 0
    
    name = sys.argv[1]
    force = 'force' in sys.argv
    
    print(f"Looking for: {name}")
    if force:
        print("Mode: FORCE terminate")
    else:
        print("Mode: Graceful terminate")
    print("-" * 50)
    
    found = []
    for proc in psutil.process_iter(['pid', 'name']):
        try:
            if name.lower() in proc.info['name'].lower():
                found.append(proc)
        except (psutil.NoSuchProcess, psutil.AccessDenied):
            continue
    
    if not found:
        print(f"No processes found with name: {name}")
        print("\nTry: tasklist | findstr {name}")
        return 1
    
    print(f"Found {len(found)} process(es):")
    for proc in found:
        print(f"  PID {proc.info['pid']}: {proc.info['name']}")
    
    if not force:
        ans = input(f"\nTerminate {len(found)} process(es)? (y/n): ")
        if ans.lower() != 'y':
            print("Cancelled.")
            return 0
    
    killed = 0
    for proc in found:
        try:
            if force:
                proc.kill()
                action = "Force killed"
            else:
                proc.terminate()
                action = "Terminated"
            
            print(f"{action} PID {proc.info['pid']}")
            killed += 1
        except Exception as e:
            print(f"Error PID {proc.info['pid']}: {e}")
    
    print(f"\n{killed}/{len(found)} processes terminated.")
    return 0

if __name__ == "__main__":
    sys.exit(main())