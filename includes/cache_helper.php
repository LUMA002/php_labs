<?php
 
 // Функція для очищення кешу списку елементів
function clearItemsCache()
{
    $cache_dir = './cache/';

    // чи існує директорія з кешем
    if (is_dir($cache_dir)) {
        // шукаємо всі файли, що починаються з items_list_
        $files = glob($cache_dir . 'items_list_*.json');

        // видаляємо кожен файл кешу
        foreach ($files as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }
}
