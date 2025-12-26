#!/usr/bin/env python3
"""
QRGEN - Generate QR codes.
Usage: qrgen <text> [output.png] [size 200]
"""

import sys
from pathlib import Path

def main():
    if len(sys.argv) < 2 or sys.argv[1] in ['--help', '-h', 'help']:
        print("QRGEN - Generate QR codes")
        print("Usage:")
        print("  qrgen \"Hello World\"")
        print("  qrgen \"https://example.com\" qrcode.png")
        print("  qrgen \"Text\" output.jpg size 300")
        return 0
    
    text = sys.argv[1]
    output_file = "qrcode.png"
    size = 200
    
    # Parse arguments
    i = 2
    while i < len(sys.argv):
        arg = sys.argv[i]
        if arg == 'size' and i + 1 < len(sys.argv) and sys.argv[i + 1].isdigit():
            size = int(sys.argv[i + 1])
            i += 2
        elif not arg.startswith('-') and output_file == "qrcode.png":
            output_file = arg
            i += 1
        else:
            i += 1
    
    # Validate size
    if size < 50:
        size = 100
        print("Warning: Size increased to 100px (minimum)")
    elif size > 1000:
        size = 500
        print("Warning: Size reduced to 500px (maximum)")
    
    print(f"Text: {text[:50]}{'...' if len(text) > 50 else ''}")
    print(f"Output: {output_file}")
    print(f"Size: {size}x{size} pixels")
    print("-" * 50)
    
    try:
        import qrcode
        
        qr = qrcode.QRCode(
            version=1,
            error_correction=qrcode.constants.ERROR_CORRECT_H,
            box_size=10,
            border=4,
        )
        qr.add_data(text)
        qr.make(fit=True)
        
        img = qr.make_image(fill_color="black", back_color="white")
        
        # Resize if needed
        if size != 200:
            img = img.resize((size, size))
        
        img.save(output_file)
        
        file_size = Path(output_file).stat().st_size if Path(output_file).exists() else 0
        print(f"✓ QR code saved to: {output_file}")
        print(f"  File size: {file_size:,} bytes")
        
        # Show usage hint
        if text.startswith(('http://', 'https://')):
            print("  This QR code contains a URL")
        elif text.startswith('mailto:'):
            print("  This QR code contains an email")
        elif text.startswith('tel:'):
            print("  This QR code contains a phone number")
        elif len(text) < 100:
            print(f"  This QR code contains: {text}")
        else:
            print("  This QR code contains text data")
        
    except ImportError:
        print("Error: Install qrcode: pip install qrcode[pil]")
        return 1
    except Exception as e:
        print(f"Error: {e}")
        return 1
    
    return 0

if __name__ == "__main__":
    sys.exit(main())