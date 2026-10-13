<?php

$directories = ['app', 'bootstrap', 'config', 'database', 'routes', 'tests', 'scripts'];
$failed = false;
foreach ($directories as $directory) {
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__.'/../'.$directory, FilesystemIterator::SKIP_DOTS)) as $file) {
        if ($file->getExtension() !== 'php') {
            continue;
        }
        exec(escapeshellarg(PHP_BINARY).' -l '.escapeshellarg($file->getPathname()).' 2>&1', $output, $exitCode);
        if ($exitCode !== 0) {
            $failed = true;
            fwrite(STDERR, implode("\n", $output)."\n");
        }
        $output = [];
    }
}
echo $failed ? "PHP lint failed.\n" : "PHP lint passed.\n";
exit($failed ? 1 : 0);
