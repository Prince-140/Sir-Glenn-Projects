#!/usr/bin/env python3
"""
RANDPASS - Generate random passwords.
Usage: randpass [length] [count] [complex]
"""

import sys
import random
import string

def main():
    if len(sys.argv) > 1 and sys.argv[1] in ['--help', '-h', 'help']:
        print("RANDPASS - Generate passwords")
        print("Usage:")
        print("  randpass              - Generate 16-char password")
        print("  randpass 20           - Generate 20-char password")
        print("  randpass 12 5         - Generate 5 passwords of 12 chars")
        print("  randpass 16 1 complex - Generate complex password")
        return 0
    
    # Default values
    length = 16
    count = 1
    complex_chars = False
    
    # Parse arguments
    i = 1
    while i < len(sys.argv):
        arg = sys.argv[i]
        if arg.isdigit():
            if i == 1:
                length = int(arg)
            elif i == 2:
                count = int(arg)
            i += 1
        elif arg == 'complex':
            complex_chars = True
            i += 1
        else:
            i += 1
    
    if length < 4:
        print("Warning: Very short passwords are insecure!")
        length = 8
    
    if count > 10:
        print("Warning: Generating many passwords at once.")
        ans = input("Continue? (y/n): ")
        if ans.lower() != 'y':
            return 0
    
    print(f"Generating {count} password(s) of {length} characters")
    if complex_chars:
        print("Character set: Complex (letters, numbers, symbols)")
    else:
        print("Character set: Alphanumeric")
    print("-" * 50)
    
    # Character sets
    if complex_chars:
        chars = string.ascii_letters + string.digits + "!@#$%^&*()_+-=[]{}|;:,.<>?"
    else:
        chars = string.ascii_letters + string.digits
    
    for i in range(count):
        password = ''.join(random.choice(chars) for _ in range(length))
        
        # Calculate approximate strength
        strength = "Weak"
        if length >= 12 and complex_chars:
            strength = "Strong"
        elif length >= 8:
            strength = "Medium"
        
        print(f"\nPassword {i+1}: {password}")
        print(f"  Length: {length}, Strength: {strength}")
        
        # Show character types
        has_lower = any(c.islower() for c in password)
        has_upper = any(c.isupper() for c in password)
        has_digit = any(c.isdigit() for c in password)
        has_symbol = any(not c.isalnum() for c in password) if complex_chars else False
        
        types = []
        if has_lower: types.append("lowercase")
        if has_upper: types.append("uppercase")
        if has_digit: types.append("digits")
        if has_symbol: types.append("symbols")
        
        print(f"  Contains: {', '.join(types)}")
    
    print("\n💡 Tips:")
    print("  • Use a password manager")
    print("  • Don't reuse passwords")
    print("  • Enable 2FA where possible")
    
    return 0

if __name__ == "__main__":
    sys.exit(main())