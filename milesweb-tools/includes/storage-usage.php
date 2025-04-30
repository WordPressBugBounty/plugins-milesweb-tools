<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
/**
 * Get total storage used by file type in the WordPress directory.
 *
 * @return array Storage usage categorized by file type.
 */
function milesweb_get_storage_info() {
    $base_dir = ABSPATH; // WordPress installation directory.
    $file_types = [
        'Images' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'],
        'CSS'    => ['css'],
        'JS'     => ['js'],
        'Others' => [], // Catch-all for unknown types.
    ];
    $storage_info = [
        'Images' => 0,
        'CSS'    => 0,
        'JS'     => 0,
        'Others' => 0,
    ];
    // Recursive directory scan
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base_dir));
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $file_size = $file->getSize();
            $extension = strtolower(pathinfo($file->getFilename(), PATHINFO_EXTENSION));
            $found_category = false;
            foreach ($file_types as $category => $extensions) {
                if (in_array($extension, $extensions)) {
                    $storage_info[$category] += $file_size;
                    $found_category = true;
                    break;
                }
            }
            // Assign to "Others" if no match
            if (!$found_category) {
                $storage_info['Others'] += $file_size;
            }
        }
    }
    // Convert bytes to MB
    foreach ($storage_info as $key => $value) {
        $storage_info[$key] = round($value / (1024 * 1024), 2); // MB
    }
    // Add total size dynamically
    $storage_info['Total'] = array_sum($storage_info);
    return $storage_info;
}
