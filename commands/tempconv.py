#!/usr/bin/env python3
"""
TEMPCONV - Convert temperatures.
Usage: tempconv <value> <from> <to>
Example: tempconv 100 C F
"""

import sys

def convert_temp(value, from_unit, to_unit):
    from_unit = from_unit.upper()
    to_unit = to_unit.upper()
    
    if from_unit == to_unit:
        return value
    
    # Convert to Celsius first
    if from_unit == 'C':
        celsius = value
    elif from_unit == 'F':
        celsius = (value - 32) * 5/9
    elif from_unit == 'K':
        celsius = value - 273.15
    else:
        return None
    
    # Convert from Celsius to target
    if to_unit == 'C':
        return celsius
    elif to_unit == 'F':
        return (celsius * 9/5) + 32
    elif to_unit == 'K':
        return celsius + 273.15
    else:
        return None

def main():
    if len(sys.argv) < 4 or sys.argv[1] in ['--help', '-h', 'help']:
        print("TEMPCONV - Temperature converter")
        print("Usage:")
        print("  tempconv <value> <from> <to>")
        print("  Units: C (Celsius), F (Fahrenheit), K (Kelvin)")
        print("\nExamples:")
        print("  tempconv 100 C F    - 100°C to Fahrenheit")
        print("  tempconv 32 F C     - 32°F to Celsius")
        print("  tempconv 0 C K      - 0°C to Kelvin")
        print("  tempconv 273.15 K C - 273.15K to Celsius")
        return 0
    
    try:
        value = float(sys.argv[1])
        from_unit = sys.argv[2].upper()
        to_unit = sys.argv[3].upper()
        
        valid_units = ['C', 'F', 'K']
        if from_unit not in valid_units or to_unit not in valid_units:
            print(f"Error: Units must be C, F, or K")
            return 1
        
        result = convert_temp(value, from_unit, to_unit)
        
        if result is None:
            print("Error: Conversion failed")
            return 1
        
        print(f"\n{value}°{from_unit} = {result:.2f}°{to_unit}")
        
        # Show all conversions
        print(f"\nOther conversions from {value}°{from_unit}:")
        print("-" * 30)
        for unit in ['C', 'F', 'K']:
            if unit != from_unit:
                conv = convert_temp(value, from_unit, unit)
                print(f"  {value:8.2f}°{from_unit} = {conv:8.2f}°{unit}")
        
        # Reference points
        print(f"\nReference points:")
        print("-" * 30)
        references = [
            ("Absolute Zero", "K", 0),
            ("Water Freezes", "C", 0),
            ("Room Temp", "C", 20),
            ("Body Temp", "C", 37),
            ("Water Boils", "C", 100),
        ]
        
        for name, ref_unit, ref_value in references:
            if from_unit != ref_unit:
                conv = convert_temp(ref_value, ref_unit, from_unit)
                print(f"  {name:12} = {conv:6.1f}°{from_unit}")
        
    except ValueError:
        print("Error: Value must be a number")
        return 1
    except Exception as e:
        print(f"Error: {e}")
        return 1
    
    return 0

if __name__ == "__main__":
    sys.exit(main())