<?php

$date = new DateTime();
echo "Run Date: " . $date->format('Y-m-d H:i:s') . "\n\n";

echo "Disabled!\n";
die();

include_once(__DIR__ . "/../../php/DataAccess/Database.php");

$db = new DatabaseV2();

$dir_path = $db->file_dir() . "/*";

$sql = "
    SELECT a.*
    FROM receipt_bckp a
";

$olds = $db->query($sql)->fetch_all(MYSQLI_ASSOC);

$insert_fails = [];
$dir_fails = [];
$copy_fails = [];

if ($olds) {
    // echo json_encode($olds);

    $sql = "
        INSERT INTO receipt (account_id,store,date,amount,shard1,shard2,file_path,file_size,file_modified,file_type,file_name,created,updated)
        VALUES ((SELECT id FROM account WHERE name = ?),?,?,?,?,?,?,?,?,?,?,?,?)
    ";

    $db->set_sql($sql);
    $db->prepare();

    foreach ($olds as $old) {
        echo "File: " . $old['filepath'] . "\n";

        $new_file_name = bin2hex(random_bytes(16));

        $shard1 = substr($new_file_name, 0, 2);
        $shard2 = substr($new_file_name, 2, 2);

        $new_path = $shard1 . "/" . $shard2 . "/" . $new_file_name;

        $full_path = $db->file_dir() . $new_path;
        echo "New File: " . $full_path . "\n";

        $db->beginTransaction();

        $result = $db->execute([
            $old['account'],
            $old['store'],
            $old['date'],
            $old['amount'],
            $shard1,
            $shard2,
            $full_path,
            $old['filesize'],
            $old['filemodified'],
            $old['filetype'],
            'receipt',
            $old['created'],
            $old['updated']
        ], "sssdsssisssss");

        if ($result === false) {
            echo "Failed to insert record..";
            array_push($insert_fails, (object)[
                "rec" => $old,
                "new" => $full_path
            ]);
            $db->rollback();
        } else {
            echo "Inserted record: " . $result . "\n";

            if (!is_dir(dirname($full_path))) {
                if (!mkdir(dirname($full_path), 0700, true)) {
                    echo "Failed to create directories..\n";
                    array_push($dir_fails, (object)[
                        "rec" => $old,
                        "new" => $full_path
                    ]);
                    $db->rollback();
                }
            }
            if (is_dir(dirname($full_path))) {
                if (!copy($old['filepath'], $full_path)) {
                    echo "Failed to copy file..\n";
                    array_push($copy_fails, (object)[
                        "rec" => $old,
                        "new" => $full_path
                    ]);
                    $db->rollback();
                } else {
                    echo "File copied!\n";
                    $db->commit();
                }
            } else {
                $db->rollback();
            }
        }

        echo "\n";

        // if (!is_dir(dirname($full_path))) {
        //     if (!mkdir(dirname($full_path), 0770, true)) {
        //         $this->actionDataMessage = "Failed to create directories";
        //         return false;
        //     }
        // }
        // if (!move_uploaded_file($rec->file()['tmp_name'], $full_path)) {
        //     $this->actionDataMessage = "Failed to create file";
        //     // return false;
        // }

        // return true;

        echo "\n";
    }

    echo "Insert Fails: " . json_encode($insert_fails) . "\n\n";
    echo "Dir Fails: " . json_encode($dir_fails) . "\n\n";
    echo "Copy Fails: " . json_encode($copy_fails) . "\n\n";
} else {
    echo "No Old Documents?";
}

echo "\n";

// $shard1s = glob($dir_path, GLOB_ONLYDIR);

// if ($shard1s) {

//     $sql = "
//         SELECT a.`id`
//         FROM document a
//         WHERE a.`shard1` = ?
//             AND a.`shard2` = ?
//             AND a.`file_path` LIKE ?
//     ";

//     $db->set_sql($sql);
//     $db->prepare();

//     foreach ($shard1s as $shard1) {
//         echo "Shard1: " . basename($shard1) . "\n";

//         $shard2s = glob($shard1 . "/*", GLOB_ONLYDIR);

//         if ($shard2s) {
//             foreach ($shard2s as $shard2) {
//                 echo "Shard2: " . basename($shard2) . "\n";

//                 $files = array_filter(glob($shard2 . "/*"), 'is_file');

//                 if ($files) {
//                     foreach ($files as $file) {
//                         echo "File: " . basename($file) . "\n";

//                         $result = $db->execute([
//                             basename($shard1),
//                             basename($shard2),
//                             "%/" . basename($file)
//                         ], "sss");

//                         $id = -1;

//                         if ($result) {
//                             $id = $result->fetch_array(MYSQLI_ASSOC)['id'];
//                         }

//                         if ($id > -1) {
//                             echo "Exists: " . $id;
//                         } else {
//                             echo "Nope.. ";
//                             if (unlink($file)) {
//                                 echo "Removed!";
//                             } else {
//                                 echo "Failed to remove..";
//                             }
//                         }
//                         echo "\n";
//                     }
//                 } else {
//                     echo "No Files Found.. ";
//                     if (rmdir($shard2)) {
//                         echo "Removed!";
//                     } else {
//                         echo "Failed to remove..";
//                     }
//                     echo "\n";
//                 }
//             }
//         } else {
//             echo "No Shard2 Found.. ";
//             if (rmdir($shard1)) {
//                 echo "Removed!";
//             } else {
//                 echo "Failed to remove..";
//             }
//             echo "\n";
//         }
//         echo "\n";
//     }
// } else {
//     echo "No Shard1 Found..";
// }
