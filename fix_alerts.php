<?php
$dir = new RecursiveDirectoryIterator('resources/views');
$ite = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($ite, '/.*\.blade\.php/', RegexIterator::GET_MATCH);
$count = 0;
foreach($files as $file) {
    $path = $file[0];
    $content = file_get_contents($path);
    if (strpos($content, 'alert-dismissible') !== false || strpos($content, 'dismiss="alert"') !== false) {
        // remove alert-dismissible class
        $content = str_replace('alert-dismissible ', '', $content);
        $content = str_replace(' alert-dismissible', '', $content);
        
        // remove the button that dismisses the alert
        $content = preg_replace('/<button[^>]*dismiss="alert"[^>]*>.*?<\/button>\s*/is', '', $content);
        
        file_put_contents($path, $content);
        echo "Updated $path\n";
        $count++;
    }
}
echo "Total updated files: $count\n";
