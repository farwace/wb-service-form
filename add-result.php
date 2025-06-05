<?php

require_once 'db.php';
$isSuccess = false;

$doAction = function () use (&$isSuccess) {
    global $pdo;

    $insertData = [
        'location' => htmlspecialchars($_POST['location']),
        'barcode1' => !empty($_POST['barcode'][0]) ? htmlspecialchars($_POST['barcode'][0]) : '',
        'barcode2' => !empty($_POST['barcode'][1]) ? htmlspecialchars($_POST['barcode'][1]) : '',
        'barcode3' => !empty($_POST['barcode'][2]) ? htmlspecialchars($_POST['barcode'][2]) : '',
        'palette' => !empty($_POST['palette']) ? htmlspecialchars($_POST['palette']) : '',
        'wb_box' => !empty($_POST['wb-box']) ? htmlspecialchars($_POST['wb-box']) : '',
        'count' => !empty($_POST['count']) ? htmlspecialchars($_POST['count']) : '',
        'product_name' => !empty($_POST['product-name']) ? htmlspecialchars($_POST['product-name']) : '',
    ];

    $querySelect = $pdo->prepare("SELECT count(*) as cnt FROM locations where location = :location");
    $querySelect->execute(['location' => htmlspecialchars($_POST['location'])]);
    $cnt = $querySelect->fetch(PDO::FETCH_ASSOC);
    if(isset($cnt['cnt'])){
        if($cnt['cnt'] == 0){
        $query = $pdo->prepare('INSERT INTO locations (location, barcode1, barcode2, barcode3, palette, wb_box, count, product_name, date_create, date_update)
                                        VALUES (:location, :barcode1, :barcode2, :barcode3, :palette, :wb_box, :count, :product_name, NOW(), NOW())');
        }
        else{
            $query = $pdo->prepare('UPDATE locations SET barcode1 = :barcode1, barcode2 = :barcode2, barcode3 = :barcode3, palette = :palette, wb_box = :wb_box, count = :count, product_name = :product_name, date_update = NOW() WHERE location = :location');
        }

        if ($query->execute($insertData)) {
            $isSuccess = true;
        }
    }
};

if(!empty($_POST['location'])){
    $doAction();
}

$data = [
    'success' => $isSuccess
];

header('Content-Type: application/json; charset=utf-8');
echo json_encode($data);