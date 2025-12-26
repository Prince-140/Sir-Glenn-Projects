#!/usr/bin/env python3
"""
ENCDEC - Encrypt/decrypt files.
Usage: encdec <file> <key> [decrypt] [output file.txt]
"""

import sys
import base64
from pathlib import Path

def main():
    if len(sys.argv) < 3 or sys.argv[1] in ['--help', '-h', 'help']:
        print("ENCDEC - Encrypt and decrypt files")
        print("Usage:")
        print("  encdec <file> <key>              - Encrypt file")
        print("  encdec <file.enc> <key> decrypt  - Decrypt file")
        print("  encdec <file> <key> output file.txt - Specify output")
        return 0
    
    input_file = sys.argv[1]
    key = sys.argv[2]
    decrypt = 'decrypt' in sys.argv
    output_file = None
    
    # Parse output file
    if 'output' in sys.argv:
        idx = sys.argv.index('output')
        if idx + 1 < len(sys.argv):
            output_file = sys.argv[idx + 1]
    
    path = Path(input_file)
    if not path.exists():
        print(f"Error: File '{input_file}' not found.")
        return 1
    
    if not output_file:
        if decrypt:
            output_file = path.stem + "_decrypted" + (path.suffix if path.suffix != '.enc' else '.txt')
        else:
            output_file = path.name + ".enc"
    
    print(f"Input: {input_file}")
    print(f"Output: {output_file}")
    print(f"Mode: {'DECRYPT' if decrypt else 'ENCRYPT'}")
    print(f"Key: {key}")
    print("-" * 50)
    
    try:
        # Simple XOR encryption (for demonstration)
        # In production, use proper crypto like cryptography.fernet
        
        with open(input_file, 'rb') as f:
            data = f.read()
        
        # Simple XOR with key
        key_bytes = key.encode('utf-8')
        result = bytearray()
        
        for i in range(len(data)):
            result.append(data[i] ^ key_bytes[i % len(key_bytes)])
        
        if decrypt:
            # For decryption, we're just reversing the XOR
            # In this simple example, encrypt and decrypt are the same
            pass
        
        with open(output_file, 'wb') as f:
            f.write(result)
        
        print(f"✓ {'Decrypted' if decrypt else 'Encrypted'} to: {output_file}")
        print(f"  Original size: {len(data):,} bytes")
        print(f"  Output size: {len(result):,} bytes")
        
        # Warning about simple encryption
        print("\n⚠ WARNING: This uses simple XOR encryption.")
        print("For real security, use proper encryption tools.")
        
    except Exception as e:
        print(f"Error: {e}")
        return 1
    
    return 0

if __name__ == "__main__":
    sys.exit(main())