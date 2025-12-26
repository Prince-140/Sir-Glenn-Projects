#!/usr/bin/env python3
"""
DISKFREE - Shows disk space.
Usage: diskfree [MB|GB|TB]
"""

import sys
import psutil
import datetime

def main():
    if len(sys.argv) > 1 and sys.argv[1] in ['--help', '-h', 'help']:
        print("DISKFREE - Disk space information")
        print("Usage:")
        print("  diskfree          - Show in GB")
        print("  diskfree MB       - Show in MB")
        print("  diskfree GB       - Show in GB (default)")
        print("  diskfree TB       - Show in TB")
        return 0
    
    unit = 'GB'
    if len(sys.argv) > 1:
        if sys.argv[1].upper() in ['MB', 'GB', 'TB', 'B', 'KB']:
            unit = sys.argv[1].upper()
    
    print("\n" + "="*60)
    print("DISK SPACE".center(60))
    print("="*60)
    print(f"Time: {datetime.datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    print(f"Units: {unit}")
    print("-" * 60)
    
    try:
        # Convert divisor based on unit
        divisors = {'B': 1, 'KB': 1024, 'MB': 1024**2, 'GB': 1024**3, 'TB': 1024**4}
        divisor = divisors[unit]
        
        print(f"{'Drive':<6} {'Total':>10} {'Used':>10} {'Free':>10} {'Use%':>6}")
        print("-" * 60)
        
        total_all = used_all = free_all = 0
        
        for part in psutil.disk_partitions():
            try:
                usage = psutil.disk_usage(part.mountpoint)
                
                total = usage.total / divisor
                used = usage.used / divisor
                free = usage.free / divisor
                
                total_all += usage.total
                used_all += usage.used
                free_all += usage.free
                
                print(f"{part.device:<6} {total:>9.1f}{unit} {used:>9.1f}{unit} {free:>9.1f}{unit} {usage.percent:>5}%")
                
            except:
                print(f"{part.device:<6} {'ACCESS DENIED':>48}")
        
        print("-" * 60)
        total = total_all / divisor
        used = used_all / divisor
        free = free_all / divisor
        percent = (used_all / total_all * 100) if total_all > 0 else 0
        
        print(f"{'TOTAL':<6} {total:>9.1f}{unit} {used:>9.1f}{unit} {free:>9.1f}{unit} {percent:>5.1f}%")
        
        # Health status
        print("\nHEALTH STATUS:")
        for part in psutil.disk_partitions():
            try:
                usage = psutil.disk_usage(part.mountpoint)
                status = "✓ OK"
                if usage.percent > 90:
                    status = "✗ CRITICAL"
                elif usage.percent > 80:
                    status = "⚠ WARNING"
                print(f"  {part.device}: {usage.percent}% - {status}")
            except:
                pass
        
    except ImportError:
        print("Error: Install psutil: pip install psutil")
        return 1
    
    print("\n" + "="*60)
    return 0

if __name__ == "__main__":
    sys.exit(main())