<?php

$date = new DateTime();
echo "Run Date: " . $date->format('Y-m-d H:i:s') . "\n\n";

include_once(__DIR__ . "/../../php/DataAccess/Database.php");

$db = new DatabaseV2();

$dir_path = $db->file_dir() . "/*";

$shard1s = glob($dir_path, GLOB_ONLYDIR);

if ($shard1s) {

    $sql = "
        SELECT a.`id`
        FROM receipt a
        WHERE a.`shard1` = ?
            AND a.`shard2` = ?
            AND a.`file_path` LIKE ?
    ";

    $db->set_sql($sql);
    $db->prepare();

    foreach ($shard1s as $shard1) {
        echo "Shard1: " . basename($shard1) . "\n";

        $shard2s = glob($shard1 . "/*", GLOB_ONLYDIR);

        if ($shard2s) {
            foreach ($shard2s as $shard2) {
                echo "Shard2: " . basename($shard2) . "\n";

                $files = array_filter(glob($shard2 . "/*"), 'is_file');

                if ($files) {
                    foreach ($files as $file) {
                        echo "File: " . basename($file) . "\n";

                        $result = $db->execute([
                            basename($shard1),
                            basename($shard2),
                            "%/" . basename($file)
                        ], "sss");

                        $id = -1;

                        if ($result) {
                            $id = $result->fetch_array(MYSQLI_ASSOC)['id'];
                        }

                        if ($id > -1) {
                            echo "Exists: " . $id;
                        } else {
                            echo "Nope.. ";
                            if (unlink($file)) {
                                echo "Removed!";
                            } else {
                                echo "Failed to remove..";
                            }
                        }
                        echo "\n";
                    }
                } else {
                    echo "No Files Found.. ";
                    if (rmdir($shard2)) {
                        echo "Removed!";
                    } else {
                        echo "Failed to remove..";
                    }
                    echo "\n";
                }
            }
        } else {
            echo "No Shard2 Found.. ";
            if (rmdir($shard1)) {
                echo "Removed!";
            } else {
                echo "Failed to remove..";
            }
            echo "\n";
        }
        echo "\n";
    }
} else {
    echo "No Shard1 Found..\n";
}
