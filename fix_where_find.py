from pathlib import Path
import re
path = Path('app/Http/Controllers/Nazframcontroller.php')
text = path.read_text(encoding='utf-8')
# Replace 2-argument Eloquent where() with explicit operator and boolean
text, where_count = re.subn(r"->where\(\s*('|")(.*?)\1\s*,\s*([^,\)]+?)\s*\)", lambda m: f"->where({m.group(1)}{m.group(2)}{m.group(1)}, '=', {m.group(3).strip()}, 'and')", text)
# Replace 1-arg Model::find() with explicit columns
text, find_count = re.subn(r"\b([A-Za-z0-9_\\]+::find\()([^,\)\n]+?)\)", lambda m: f"{m.group(1)}{m.group(2).strip()}, ['*'])", text)
path.write_text(text, encoding='utf-8')
print(f'where_replacements={where_count}')
print(f'find_replacements={find_count}')
