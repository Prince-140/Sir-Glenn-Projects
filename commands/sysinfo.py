
"""
SYSINFO - Displays a detailed report of the current operating system and environment.
"""

import sys
import platform
import os
import socket
import psutil
import datetime



def main():

    try:
       
        if len(sys.argv) > 1 and sys.argv[1] in ['--help']:
            print("SYSINFO - Displays system information")
            print("Usage: sysinfo [basic|full]")
            print("  basic: Basic system info (default)")
            print("  full:  Detailed system info")
            return 0
        
        

        Gwapo()
        return 0
    

    
    except Exception as e:
        print(f"Error: {e}")
        return 1






def Gwapo(detail_level='basic'):
    """Collect and display system information."""

    if len(sys.argv) > 1:
        detail_level = sys.argv[1].lower()
        if detail_level not in ['basic', 'full']:
            detail_level = 'basic'
    
    print("\n" + "="*60)
    print("DEVICE SYSTEM INFORMATION".center(60))
    print("="*60)
    print(f"Detail Level: {detail_level.upper()}")
    print("-" * 60)
    
 
    print("\nSYSTEM")
    print(f"  Operating System: {platform.system()} {platform.release()}")
    print(f"  Version: {platform.version()}")
    print(f"  Architecture: {platform.machine()}")
    print(f"  Processor: {platform.processor()}")
    print(f"  Hostname: {socket.gethostname()}")
    

    print("\nUSER")
    print(f"  Username: {os.getlogin()}")
    print(f"  User Home: {os.path.expanduser('~')}")
    
    if detail_level == 'full':
     
        print("\nDETAILED SYSTEM")
        print(f"  Platform: {platform.platform()}")
        print(f"  Python Version: {platform.python_version()}")
        print(f"  Build Number: {platform.win32_ver()[1] if platform.system() == 'Windows' else 'N/A'}")
        
       
        print("\nMEMORY")
        memory = psutil.virtual_memory()
        print(f"  Total: {memory.total / (1024**3):.2f} GB")
        print(f"  Available: {memory.available / (1024**3):.2f} GB")
        print(f"  Used: {memory.percent}%")
        
      
        print("\nDISK")
        partitions = psutil.disk_partitions()
        for partition in partitions[:5]:  
            try:
                usage = psutil.disk_usage(partition.mountpoint)
                print(f"  {partition.device} ({partition.mountpoint}):")
                print(f"    Total: {usage.total / (1024**3):.2f} GB")
                print(f"    Used: {usage.used / (1024**3):.2f} GB ({usage.percent}%)")
                print(f"    Free: {usage.free / (1024**3):.2f} GB")
            except:
                print(f"  {partition.device}: Access Denied")
        
     
        print("\nNETWORK")
        addrs = psutil.net_if_addrs()
        for interface, addresses in list(addrs.items())[:3]:  
            print(f"  {interface}:")
            for addr in addresses:
                if addr.family == socket.AF_INET:
                    print(f"    IPv4: {addr.address}")
                elif addr.family == socket.AF_INET6:
                    print(f"    IPv6: {addr.address}")
        
      
        print("\nENVIRONMENT")
        env_vars = list(os.environ.items())[:20]
        for key, value in env_vars:
            if len(value) > 50:
                value = value[:47] + "..."
            print(f"  {key}={value}")

    print(f"\nCURRENT DIRECTORY")
    print(f"  Path: {os.getcwd()}")
    
  
    print(f"\nSYSTEM UPTIME")
    boot_time = datetime.datetime.fromtimestamp(psutil.boot_time())
    uptime = datetime.datetime.now() - boot_time
    print(f"  Boot Time: {boot_time.strftime('%Y-%m-%d %H:%M:%S')}")
    print(f"  Uptime: {str(uptime).split('.')[0]}")
    
    print("\n" + "="*60)
    return True



if __name__ == "__main__":
    sys.exit(main())