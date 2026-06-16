<?php
$dir = new RecursiveDirectoryIterator(__DIR__);
$iterator = new RecursiveIteratorIterator($dir);
$regex = new RegexIterator($iterator, '/^.+\.(php|css)$/i', RecursiveRegexIterator::GET_MATCH);

$replacements = [
    // Colors
    '--neon-cyan:#FF4747;' => '--neon-cyan:#FF5A00;',
    '--neon-cyan: #FF4747;' => '--neon-cyan: #FF5A00;',
    '--neon-cyan:#00F7FF;' => '--neon-cyan:#FF5A00;',
    '--neon-cyan: #00F7FF;' => '--neon-cyan: #FF5A00;',
    
    // Backgrounds
    '--bg-dark:#0D0F14;' => '--bg-dark:#F8FAFC;',
    '--bg-dark: #0D0F14;' => '--bg-dark: #F8FAFC;',
    '--bg-dark:#050816;' => '--bg-dark:#F8FAFC;',
    '--bg-dark: #050816;' => '--bg-dark: #F8FAFC;',
    
    '--bg-secondary:#161922;' => '--bg-secondary:#FFFFFF;',
    '--bg-secondary: #161922;' => '--bg-secondary: #FFFFFF;',
    '--bg-secondary:#0B1020;' => '--bg-secondary:#FFFFFF;',
    '--bg-secondary: #0B1020;' => '--bg-secondary: #FFFFFF;',
    
    '--bg-card:rgba(255,255,255,0.03);' => '--bg-card:#FFFFFF;',
    '--bg-card: rgba(255,255,255,0.03);' => '--bg-card: #FFFFFF;',
    '--bg-card:rgba(255,255,255,0.04);' => '--bg-card:#FFFFFF;',
    '--bg-card: rgba(255,255,255,0.04);' => '--bg-card: #FFFFFF;',
    
    '--bg-glass:rgba(255,255,255,0.04);' => '--bg-glass:#FFFFFF;',
    '--bg-glass: rgba(255,255,255,0.04);' => '--bg-glass: #FFFFFF;',
    '--bg-glass:rgba(255,255,255,0.05);' => '--bg-glass:#FFFFFF;',
    '--bg-glass: rgba(255,255,255,0.05);' => '--bg-glass: #FFFFFF;',

    '--border-glass:rgba(255,255,255,0.08);' => '--border-glass:#E2E8F0;',
    '--border-glass: rgba(255,255,255,0.08);' => '--border-glass: #E2E8F0;',
    '--bg-glass-border:rgba(255,255,255,0.08);' => '--bg-glass-border:#E2E8F0;',
    '--bg-glass-border: rgba(255,255,255,0.08);' => '--bg-glass-border: #E2E8F0;',
    '--bg-glass-border:rgba(255,255,255,0.10);' => '--bg-glass-border:#E2E8F0;',
    '--bg-glass-border: rgba(255,255,255,0.10);' => '--bg-glass-border: #E2E8F0;',
    
    // Texts
    '--text-primary:#F8FAFC;' => '--text-primary:#0F172A;',
    '--text-primary: #F8FAFC;' => '--text-primary: #0F172A;',
    '--text-primary:#F0F4FF;' => '--text-primary:#0F172A;',
    '--text-primary: #F0F4FF;' => '--text-primary: #0F172A;',
    
    '--text-secondary:#94A3B8;' => '--text-secondary:#475569;',
    '--text-secondary: #94A3B8;' => '--text-secondary: #475569;',
    
    // Shadows
    '0 0 20px rgba(0,247,255,0.3)' => '0 10px 15px -3px rgba(0,0,0,0.1)',
    '0 0 20px rgba(255,71,71,0.4)' => '0 10px 15px -3px rgba(0,0,0,0.1)',
    
    // Hardcoded colors
    '#050816' => '#F8FAFC',
    '#0a1a3e' => '#F1F5F9',
    '#0D0F14' => '#F8FAFC',
    '#161922' => '#FFFFFF',
    '#0B1020' => '#FFFFFF',
    
    // Make text dark for light mode specifically where hardcoded
    'color:#F0F4FF' => 'color:#0F172A',
    'color:#F8FAFC' => 'color:#0F172A',
    'color: var(--text-primary)' => 'color: #0F172A',
];

$count = 0;
foreach ($regex as $file) {
    $path = $file[0];
    if (strpos($path, 'make_light_mode.php') !== false) continue;
    
    $content = file_get_contents($path);
    $new_content = str_replace(array_keys($replacements), array_values($replacements), $content);
    
    if ($content !== $new_content) {
        file_put_contents($path, $new_content);
        $count++;
    }
}
echo "Transformed $count files to Light Mode Flat Design successfully.\n";
