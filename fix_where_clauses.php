<?php
$file = 'app/Http/Controllers/Nazframcontroller.php';
$content = file_get_contents($file);

// Remove all ', 'and') patterns from where clauses
$content = preg_replace("/->where\(([^)]+?),\s*'and'\)/", "->where(\$1)", $content);

// Also handle cases with multiple 'and' in deeply nested contexts
$content = str_replace(", 'and')", ")", $content);

file_put_contents($file, $content);
echo "All where() clause issues fixed.\n";
?>
