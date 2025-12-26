#!/usr/bin/env python3
"""
Setup verification script for MyDosUtils
"""

import os
import sys
from pathlib import Path

def verify_setup():
    print("="*60)
    print("MyDosUtils Setup Verification")
    print("="*60)
    
    base_dir = Path("C:/MyDosUtils")
    
    # Check directories
    required_dirs = ["commands", "batch", "utils", "data", "docs"]
    print("\n[1] Checking directory structure:")
    for dir_name in required_dirs:
        dir_path = base_dir / dir_name
        if dir_path.exists():
            print(f"  ✓ {dir_name}/")
        else:
            print(f"  ✗ {dir_name}/ - MISSING")
            return False
    
    # Check Python scripts (commands)
    print("\n[2] Checking Python command files:")
    command_files = [
        "customhelp.py", "sysinfo.py", "cleandir.py", "dupescan.py",
        "setpath.py", "appterm.py", "netping.py", "diskfree.py",
        "filetouch.py", "bulkren.py", "textfind.py", "ziparch.py",
        "pdf2txt.py", "csvsort.py", "encdec.py", "todo.py",
        "ipget.py", "randpass.py", "qrgen.py", "tempconv.py",
        "logparse.py"
    ]
    
    missing_scripts = []
    for script in command_files:
        script_path = base_dir / "commands" / script
        if script_path.exists():
            print(f"  ✓ {script}")
        else:
            print(f"  ✗ {script} - MISSING")
            missing_scripts.append(script)
    
    if missing_scripts:
        print(f"\nMissing {len(missing_scripts)} script(s)")
        return False
    
    # Check batch files
    print("\n[3] Checking batch wrapper files:")
    batch_files = [f.replace(".py", ".bat") for f in command_files]
    
    missing_batch = []
    for batch in batch_files:
        batch_path = base_dir / "batch" / batch
        if batch_path.exists():
            print(f"  ✓ {batch}")
        else:
            print(f"  ✗ {batch} - MISSING")
            missing_batch.append(batch)
    
    if missing_batch:
        print(f"\nMissing {len(missing_batch)} batch file(s)")
        return False
    
    # Check Python installation
    print("\n[4] Checking Python installation:")
    try:
        import psutil
        print("  ✓ psutil")
    except ImportError:
        print("  ✗ psutil - Install with: pip install psutil")
        return False
    
    try:
        import requests
        print("  ✓ requests")
    except ImportError:
        print("  ✗ requests - Install with: pip install requests")
        return False
    
    try:
        from cryptography.fernet import Fernet
        print("  ✓ cryptography")
    except ImportError:
        print("  ✗ cryptography - Install with: pip install cryptography")
        return False
    
    try:
        import qrcode
        print("  ✓ qrcode")
    except ImportError:
        print("  ✗ qrcode - Install with: pip install qrcode[pil]")
        return False
    
    try:
        import PyPDF2
        print("  ✓ PyPDF2")
    except ImportError:
        print("  ✗ PyPDF2 - Install with: pip install PyPDF2")
        return False
    
    # Check PATH
    print("\n[5] Checking system PATH:")
    batch_path = str(base_dir / "batch")
    path_var = os.environ.get("PATH", "")
    
    if batch_path in path_var:
        print(f"  ✓ C:\\MyDosUtils\\batch is in PATH")
    else:
        print(f"  ✗ C:\\MyDosUtils\\batch is NOT in PATH")
        print(f"\n  To add to PATH:")
        print(f"  1. Open System Properties > Environment Variables")
        print(f"  2. Edit PATH variable")
        print(f"  3. Add: C:\\MyDosUtils\\batch")
        print(f"  4. Restart Command Prompt")
    
    print("\n" + "="*60)
    print("SETUP SUMMARY")
    print("="*60)
    print("✓ Directory structure: OK")
    print(f"✓ Python scripts: {len(command_files)} files")
    print(f"✓ Batch files: {len(batch_files)} files")
    print("✓ Python packages: All installed")
    
    if batch_path in path_var:
        print("✓ PATH configuration: OK")
        print("\n✅ SETUP COMPLETE!")
        print("\nTest with: customhelp")
    else:
        print("⚠ PATH configuration: Needs manual setup")
        print("\nAfter adding to PATH, restart and test with: customhelp")
    
    return True

if __name__ == "__main__":
    verify_setup()