<?php
$dir = new RecursiveDirectoryIterator(__DIR__ . '/../resources/views');
$ite = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($ite, '/\.blade\.php$/');
$count = 0;
foreach($files as $file) {
    $path = $file->getPathname();
    $content = file_get_contents($path);
    $newContent = str_replace('assets/images/favicon.svg', 'assets/images/indahsarisalonimg.jpg', $content);
    if($content !== $newContent) {
        file_put_contents($path, $newContent);
        $count++;
    }
}
echo "Berhasil mengganti ikon di $count file halamann.\n";
