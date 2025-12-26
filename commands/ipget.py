#!/usr/bin/env python3
"""
IPGET - Get IP address.
Usage: ipget [detail]
"""

import sys
import socket
import requests

def main():
    if len(sys.argv) > 1 and sys.argv[1] in ['--help', '-h', 'help']:
        print("IPGET - Get IP addresses")
        print("Usage:")
        print("  ipget          - Show public IP")
        print("  ipget detail   - Show detailed info")
        return 0
    
    detail = len(sys.argv) > 1 and sys.argv[1] == 'detail'
    
    print("\n" + "="*50)
    print("IP ADDRESS INFORMATION")
    print("="*50)
    
    # Get local IP
    try:
        s = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
        s.connect(("8.8.8.8", 80))
        local_ip = s.getsockname()[0]
        s.close()
        print(f"\nLocal IP: {local_ip}")
    except:
        print("\nLocal IP: Could not determine")
    
    # Get public IP
    try:
        response = requests.get("https://api.ipify.org?format=json", timeout=5)
        if response.status_code == 200:
            public_ip = response.json()["ip"]
            print(f"Public IP: {public_ip}")
            
            if detail:
                # Try to get location info
                try:
                    geo_response = requests.get(f"https://ipapi.co/{public_ip}/json/", timeout=5)
                    if geo_response.status_code == 200:
                        geo = geo_response.json()
                        print(f"\nLocation:")
                        print(f"  City: {geo.get('city', 'Unknown')}")
                        print(f"  Region: {geo.get('region', 'Unknown')}")
                        print(f"  Country: {geo.get('country_name', 'Unknown')}")
                        print(f"  ISP: {geo.get('org', 'Unknown')}")
                except:
                    pass
        else:
            print("Public IP: Could not retrieve")
    except:
        print("Public IP: Could not retrieve")
        print("\nCheck internet connection or try:")
        print("  curl https://api.ipify.org")
    
    print("\n" + "="*50)
    return 0

if __name__ == "__main__":
    sys.exit(main())