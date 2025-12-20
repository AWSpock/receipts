<?php

$storeData = $data->stores($userAuth->user()->id());

$recStore = $storeData->getRecordByName($store_id);
if ($recStore->store() === null) {
    header('Location: /store');
    die();
}

// $recStore->store_receipts($data->receipts($recAccount->id())->getRecords());

//

// if (!empty($_POST['warranty_favorite'])) {
//     $res = 0;
//     switch ($_POST['warranty_favorite']) {
//         case "Yes":
//             $res = $storeData->setFavorite($recAccount->id());
//             break;
//         case "No":
//             $res = $storeData->removeFavorite($recAccount->id());
//             break;
//     }
//     $_SESSION['last_message_text'] = $storeData->actionDataMessage;
//     if ($res === true) {
//         $_SESSION['last_message_type'] = "success";
//         header('Location: /account/' . $recAccount->id() . '/summary');
//         die();
//     } else {
//         $_SESSION['last_message_type'] = "danger";
//     }
// }