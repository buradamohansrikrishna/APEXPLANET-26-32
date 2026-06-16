<?php
$dir = new RecursiveDirectoryIterator(__DIR__);
$iterator = new RecursiveIteratorIterator($dir);
$regex = new RegexIterator($iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

$replacements = [
    '--neon-cyan:#FF5A00;' => '--neon-cyan:#FF5A00;',
    '--neon-cyan: #FF5A00;' => '--neon-cyan: #FF5A00;',
    '--bg-dark:#F8FAFC;' => '--bg-dark:#F8FAFC;',
    '--bg-dark: #F8FAFC;' => '--bg-dark: #F8FAFC;',
    '--bg-secondary:#FFFFFF;' => '--bg-secondary:#FFFFFF;',
    '--bg-secondary: #FFFFFF;' => '--bg-secondary: #FFFFFF;',
    '#00F7FF' => '#FF4747',
    '#F8FAFC' => '#F8FAFC',
    '#FFFFFF' => '#FFFFFF',
    'rgba(0,247,255,' => 'rgba(255,71,71,',
    '--text-primary:#0F172A;' => '--text-primary:#0F172A;',
    '--text-primary: #0F172A;' => '--text-primary: #0F172A;'
];

$count = 0;
foreach ($regex as $file) {
    $path = $file[0];
    if (strpos($path, 'retheme.php') !== false) continue;
    
    $content = file_get_contents($path);
    $new_content = str_replace(array_keys($replacements), array_values($replacements), $content);
    
    if ($content !== $new_content) {
        file_put_contents($path, $new_content);
        $count++;
    }
}
echo "Rethemed $count files successfully.\n";
