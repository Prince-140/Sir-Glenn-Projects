#!/usr/bin/env python3
"""
PDF2TXT - Convert PDF to text.
Usage: pdf2txt <pdf_file> [output.txt] [pages 1-3,5]
"""

import sys
from pathlib import Path

def main():
    if len(sys.argv) < 2 or sys.argv[1] in ['--help', '-h', 'help']:
        print("PDF2TXT - Convert PDF to text")
        print("Usage:")
        print("  pdf2txt <file.pdf>")
        print("  pdf2txt <file.pdf> output.txt")
        print("  pdf2txt <file.pdf> pages 1-3,5")
        return 0
    
    pdf_file = sys.argv[1]
    output_file = None
    pages_arg = None
    
    # Parse arguments
    i = 2
    while i < len(sys.argv):
        arg = sys.argv[i]
        if arg == 'pages' and i + 1 < len(sys.argv):
            pages_arg = sys.argv[i + 1]
            i += 2
        elif not arg.startswith('-') and output_file is None:
            output_file = arg
            i += 1
        else:
            i += 1
    
    path = Path(pdf_file)
    if not path.exists():
        print(f"Error: PDF file '{pdf_file}' not found.")
        return 1
    
    if not output_file:
        output_file = path.with_suffix('.txt').name
    
    print(f"PDF: {pdf_file}")
    print(f"Output: {output_file}")
    if pages_arg:
        print(f"Pages: {pages_arg}")
    print("-" * 50)
    
    try:
        # Try PyPDF2 first
        try:
            import PyPDF2
            
            with open(pdf_file, 'rb') as file:
                pdf_reader = PyPDF2.PdfReader(file)
                total_pages = len(pdf_reader.pages)
                
                print(f"Pages in PDF: {total_pages}")
                
                # Parse page range
                pages_to_extract = []
                if pages_arg:
                    for part in pages_arg.split(','):
                        if '-' in part:
                            start, end = part.split('-')
                            pages_to_extract.extend(range(int(start), int(end) + 1))
                        else:
                            pages_to_extract.append(int(part))
                else:
                    pages_to_extract = range(1, total_pages + 1)
                
                # Extract text
                text_content = []
                for page_num in pages_to_extract:
                    if 1 <= page_num <= total_pages:
                        page = pdf_reader.pages[page_num - 1]
                        text = page.extract_text()
                        if text.strip():
                            text_content.append(f"\n--- Page {page_num} ---\n{text}")
                
                # Write to file
                with open(output_file, 'w', encoding='utf-8') as f:
                    f.write(f"PDF: {pdf_file}\n")
                    f.write(f"Pages extracted: {len(pages_to_extract)}\n")
                    f.write("="*50 + "\n")
                    for text in text_content:
                        f.write(text)
                
                print(f"✓ Converted to: {output_file}")
                
        except ImportError:
            print("Error: Install PyPDF2: pip install PyPDF2")
            return 1
            
    except Exception as e:
        print(f"Error: {e}")
        return 1
    
    return 0

if __name__ == "__main__":
    sys.exit(main())