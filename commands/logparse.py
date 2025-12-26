#!/usr/bin/env python3
"""
LOGPARSE - Parse log files.
Usage: logparse <file.log> [error|warn|info] [search text]
"""

import sys
from pathlib import Path
import re

def main():
    if len(sys.argv) < 2 or sys.argv[1] in ['--help', '-h', 'help']:
        print("LOGPARSE - Parse log files")
        print("Usage:")
        print("  logparse <file.log>")
        print("  logparse <file.log> error")
        print("  logparse <file.log> search \"text\"")
        return 0
    
    log_file = sys.argv[1]
    level_filter = None
    search_text = None
    
    # Parse arguments
    i = 2
    while i < len(sys.argv):
        arg = sys.argv[i]
        if arg in ['error', 'warn', 'info', 'debug']:
            level_filter = arg.upper()
            i += 1
        elif arg == 'search' and i + 1 < len(sys.argv):
            search_text = sys.argv[i + 1]
            i += 2
        else:
            i += 1
    
    path = Path(log_file)
    if not path.exists():
        print(f"Error: Log file '{log_file}' not found.")
        return 1
    
    file_size = path.stat().st_size
    print(f"Log file: {log_file}")
    print(f"Size: {file_size:,} bytes")
    if level_filter:
        print(f"Filter: {level_filter}")
    if search_text:
        print(f"Search: '{search_text}'")
    print("-" * 60)
    
    try:
        with open(log_file, 'r', encoding='utf-8', errors='ignore') as f:
            lines = f.readlines()
        
        print(f"Total lines: {len(lines):,}")
        
        # Common log patterns
        patterns = [
            r'(?P<level>ERROR|WARN|WARNING|INFO|DEBUG)\b',
            r'\[(?P<level>ERROR|WARN|INFO|DEBUG)\]',
            r'\"level\":\"(?P<level>\w+)\"',
        ]
        
        compiled_patterns = [re.compile(p, re.IGNORECASE) for p in patterns]
        
        matching_lines = []
        
        for line_num, line in enumerate(lines, 1):
            line = line.rstrip('\n')
            if not line.strip():
                continue
            
            # Check level
            line_level = None
            for pattern in compiled_patterns:
                match = pattern.search(line)
                if match and 'level' in match.groupdict():
                    line_level = match.group('level').upper()
                    break
            
            # Apply filters
            matches = True
            
            if level_filter and line_level:
                # Simple level filtering
                level_order = {'ERROR': 1, 'WARN': 2, 'WARNING': 2, 'INFO': 3, 'DEBUG': 4}
                line_order = level_order.get(line_level, 99)
                filter_order = level_order.get(level_filter, 99)
                if line_order > filter_order:  # Higher number = less severe
                    matches = False
            
            if search_text and matches:
                if search_text.lower() not in line.lower():
                    matches = False
            
            if matches:
                matching_lines.append((line_num, line, line_level))
        
        print(f"Matching lines: {len(matching_lines):,}")
        
        if matching_lines:
            print("\nMATCHING LINES:")
            print("=" * 60)
            
            for line_num, line, level in matching_lines[:50]:  # Show first 50
                if len(line) > 120:
                    display = line[:117] + "..."
                else:
                    display = line
                
                level_str = f"[{level}] " if level else ""
                print(f"{line_num:6d} {level_str}{display}")
            
            if len(matching_lines) > 50:
                print(f"\n... and {len(matching_lines) - 50} more lines")
            
            # Save results
            output_file = path.stem + "_filtered.log"
            with open(output_file, 'w', encoding='utf-8') as f:
                f.write(f"# Filtered log from: {log_file}\n")
                f.write(f"# Lines: {len(matching_lines)}/{len(lines)}\n")
                f.write("="*60 + "\n\n")
                for line_num, line, level in matching_lines:
                    f.write(f"{line_num:6d} {line}\n")
            
            print(f"\n✓ Results saved to: {output_file}")
        
        else:
            print("\nNo lines matched the filters.")
        
    except Exception as e:
        print(f"Error: {e}")
        return 1
    
    return 0

if __name__ == "__main__":
    sys.exit(main())