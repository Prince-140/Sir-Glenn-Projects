#!/usr/bin/env python3
"""
CSVSORT - Sort CSV files.
Usage: csvsort <file.csv> <column> [desc] [output sorted.csv]
"""

import sys
import csv
from pathlib import Path

def main():
    if len(sys.argv) < 3 or sys.argv[1] in ['--help', '-h', 'help']:
        print("CSVSORT - Sort CSV files")
        print("Usage:")
        print("  csvsort <file.csv> <column>")
        print("  csvsort <file.csv> <column> desc")
        print("  csvsort <file.csv> <column> output sorted.csv")
        return 0
    
    csv_file = sys.argv[1]
    column = sys.argv[2]
    descending = 'desc' in sys.argv
    output_file = None
    
    # Parse output file
    if 'output' in sys.argv:
        idx = sys.argv.index('output')
        if idx + 1 < len(sys.argv):
            output_file = sys.argv[idx + 1]
    
    if not output_file:
        output_file = Path(csv_file).stem + "_sorted.csv"
    
    path = Path(csv_file)
    if not path.exists():
        print(f"Error: CSV file '{csv_file}' not found.")
        return 1
    
    print(f"CSV: {csv_file}")
    print(f"Sort by: {column}")
    print(f"Order: {'descending' if descending else 'ascending'}")
    print(f"Output: {output_file}")
    print("-" * 50)
    
    try:
        # Read CSV
        with open(csv_file, 'r', encoding='utf-8') as f:
            reader = csv.DictReader(f)
            rows = list(reader)
            fieldnames = reader.fieldnames
        
        if not fieldnames:
            print("Error: CSV has no headers.")
            return 1
        
        if column not in fieldnames:
            print(f"Error: Column '{column}' not found.")
            print(f"Available columns: {', '.join(fieldnames)}")
            return 1
        
        print(f"Rows: {len(rows)}")
        
        # Sort
        try:
            # Try numeric sort first
            sorted_rows = sorted(rows, key=lambda x: float(x[column]), reverse=descending)
        except:
            # Fallback to string sort
            sorted_rows = sorted(rows, key=lambda x: str(x[column]).lower(), reverse=descending)
        
        # Write sorted CSV
        with open(output_file, 'w', encoding='utf-8', newline='') as f:
            writer = csv.DictWriter(f, fieldnames=fieldnames)
            writer.writeheader()
            writer.writerows(sorted_rows)
        
        print(f"✓ Sorted and saved to: {output_file}")
        
        # Show first few sorted values
        print("\nFirst 5 sorted values:")
        for i, row in enumerate(sorted_rows[:5]):
            value = row[column]
            if len(value) > 50:
                value = value[:47] + "..."
            print(f"  {i+1}. {value}")
        
    except Exception as e:
        print(f"Error: {e}")
        return 1
    
    return 0

if __name__ == "__main__":
    sys.exit(main())