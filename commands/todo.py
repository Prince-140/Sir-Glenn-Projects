#!/usr/bin/env python3
"""
TODO - Task manager.
Usage: todo [add|list|done|remove|clear] [arguments]
"""

import sys
import json
import os
from pathlib import Path
from datetime import datetime

TODO_FILE = Path.home() / ".mytodos.json"

def load_todos():
    if TODO_FILE.exists():
        try:
            with open(TODO_FILE, 'r') as f:
                return json.load(f)
        except:
            return {"tasks": [], "next_id": 1}
    return {"tasks": [], "next_id": 1}

def save_todos(todos):
    with open(TODO_FILE, 'w') as f:
        json.dump(todos, f, indent=2)

def main():
    if len(sys.argv) < 2 or sys.argv[1] in ['--help', '-h', 'help']:
        print("TODO - Task manager")
        print("Usage:")
        print("  todo add \"Task description\"          - Add task")
        print("  todo list                             - List tasks")
        print("  todo done 1 2 3                      - Mark tasks as done")
        print("  todo remove 1 2                      - Remove tasks")
        print("  todo clear                           - Clear completed")
        print("  todo stats                           - Show statistics")
        return 0
    
    command = sys.argv[1]
    todos = load_todos()
    
    if command == 'add':
        if len(sys.argv) < 3:
            print("Error: Need task description")
            return 1
        
        description = ' '.join(sys.argv[2:])
        task = {
            "id": todos["next_id"],
            "description": description,
            "created": datetime.now().isoformat(),
            "done": False
        }
        
        todos["tasks"].append(task)
        todos["next_id"] += 1
        save_todos(todos)
        
        print(f"✓ Added task #{task['id']}: {description}")
        
    elif command == 'list':
        tasks = todos["tasks"]
        if not tasks:
            print("No tasks. Add one with: todo add \"Task\"")
            return 0
        
        print(f"\nTODO LIST ({len(tasks)} tasks)")
        print("="*50)
        
        active = [t for t in tasks if not t['done']]
        completed = [t for t in tasks if t['done']]
        
        if active:
            print("\nACTIVE:")
            for task in active:
                print(f"  [{task['id']}] {task['description']}")
        
        if completed:
            print("\nCOMPLETED:")
            for task in completed:
                print(f"  ✓ [{task['id']}] {task['description']}")
        
        print(f"\nActive: {len(active)}, Completed: {len(completed)}")
        
    elif command == 'done':
        if len(sys.argv) < 3:
            print("Error: Need task IDs")
            return 1
        
        updated = 0
        for arg in sys.argv[2:]:
            try:
                task_id = int(arg)
                for task in todos["tasks"]:
                    if task["id"] == task_id and not task["done"]:
                        task["done"] = True
                        task["completed"] = datetime.now().isoformat()
                        updated += 1
                        print(f"✓ Marked #{task_id} as done")
                        break
            except ValueError:
                print(f"Error: '{arg}' is not a valid ID")
        
        if updated > 0:
            save_todos(todos)
        
    elif command == 'remove':
        if len(sys.argv) < 3:
            print("Error: Need task IDs")
            return 1
        
        removed_ids = [int(arg) for arg in sys.argv[2:] if arg.isdigit()]
        original_count = len(todos["tasks"])
        todos["tasks"] = [t for t in todos["tasks"] if t["id"] not in removed_ids]
        removed = original_count - len(todos["tasks"])
        
        if removed > 0:
            save_todos(todos)
            print(f"✓ Removed {removed} task(s)")
        
    elif command == 'clear':
        original_count = len(todos["tasks"])
        todos["tasks"] = [t for t in todos["tasks"] if not t["done"]]
        removed = original_count - len(todos["tasks"])
        
        if removed > 0:
            save_todos(todos)
            print(f"✓ Cleared {removed} completed task(s)")
        else:
            print("No completed tasks to clear")
        
    elif command == 'stats':
        tasks = todos["tasks"]
        total = len(tasks)
        active = len([t for t in tasks if not t['done']])
        completed = total - active
        
        print(f"\nTODO STATISTICS")
        print("="*30)
        print(f"Total tasks: {total}")
        print(f"Active: {active}")
        print(f"Completed: {completed}")
        if total > 0:
            print(f"Completion: {completed/total*100:.1f}%")
        
    else:
        print(f"Error: Unknown command '{command}'")
        return 1
    
    return 0

if __name__ == "__main__":
    sys.exit(main())