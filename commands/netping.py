#!/usr/bin/env python3
"""
NETPING - Pings network hosts.
Usage: netping <host1> <host2> ... [timeout 1000] [count 4]
"""

import sys
import subprocess
import platform
import re
from datetime import datetime

def ping_host(host, timeout=1000, count=4):
    system = platform.system().lower()
    
    if system == "windows":
        cmd = ['ping', '-n', str(count), '-w', str(timeout), host]
    else:
        cmd = ['ping', '-c', str(count), '-W', str(timeout/1000), host]
    
    try:
        result = subprocess.run(cmd, capture_output=True, text=True, timeout=10)
        
        if system == "windows":
            if "Request timed out" in result.stdout:
                return {'host': host, 'status': 'DOWN', 'latency': None}
            
            match = re.search(r'Average = (\d+)ms', result.stdout)
            if match:
                return {'host': host, 'status': 'UP', 'latency': int(match.group(1))}
        else:
            if "100% packet loss" in result.stdout:
                return {'host': host, 'status': 'DOWN', 'latency': None}
            
            match = re.search(r'min/avg/max/[^=]+= [\d.]+/([\d.]+)/', result.stdout)
            if match:
                return {'host': host, 'status': 'UP', 'latency': float(match.group(1))}
        
        return {'host': host, 'status': 'UNKNOWN', 'latency': None}
        
    except subprocess.TimeoutExpired:
        return {'host': host, 'status': 'TIMEOUT', 'latency': None}
    except Exception:
        return {'host': host, 'status': 'ERROR', 'latency': None}

def main():
    if len(sys.argv) < 2 or sys.argv[1] in ['--help', '-h', 'help']:
        print("NETPING - Ping multiple hosts")
        print("Usage:")
        print("  netping google.com")
        print("  netping 8.8.8.8 1.1.1.1")
        print("  netping host1 host2 timeout 2000")
        print("  netping host1 host2 count 2")
        return 0
    
    # Parse arguments
    hosts = []
    timeout = 1000
    count = 4
    
    i = 1
    while i < len(sys.argv):
        arg = sys.argv[i]
        if arg == 'timeout' and i + 1 < len(sys.argv):
            timeout = int(sys.argv[i + 1])
            i += 2
        elif arg == 'count' and i + 1 < len(sys.argv):
            count = int(sys.argv[i + 1])
            i += 2
        elif not arg.startswith('-'):
            hosts.append(arg)
            i += 1
        else:
            i += 1
    
    if not hosts:
        print("Error: No hosts specified.")
        return 1
    
    print(f"Pinging {len(hosts)} host(s)")
    print(f"Timeout: {timeout}ms, Count: {count}")
    print("-" * 50)
    
    results = []
    for host in hosts:
        print(f"Pinging {host}...", end=' ', flush=True)
        result = ping_host(host, timeout, count)
        results.append(result)
        
        if result['status'] == 'UP':
            print(f"✓ UP ({result['latency']}ms)")
        elif result['status'] == 'DOWN':
            print(f"✗ DOWN")
        else:
            print(f"? {result['status']}")
    
    print("\n" + "="*50)
    up = [r for r in results if r['status'] == 'UP']
    print(f"Summary: {len(up)}/{len(hosts)} hosts reachable")
    
    if up:
        avg_latency = sum(r['latency'] for r in up if r['latency']) / len(up)
        print(f"Average latency: {avg_latency:.1f}ms")
    
    return 0

if __name__ == "__main__":
    sys.exit(main())