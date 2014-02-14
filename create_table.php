<?php

// 環境変数参照
$vcap_services = getenv('VCAP_SERVICES');
if($vcap_services == false){
    exit();
}

// 環境変数からDB設定を取得
$vcap_services_json = json_decode($vcap_services);
$db = $vcap_services_json->{"mysql-5.1"}[0]->credentials;

// DB接続
$pdo = null;
try{
    $pdo = new PDO(
        "mysql:host={$db->host};port={$db->port};dbname={$db->name}",
        $db->username,
        $db->password,
        array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET utf8")
    );
} catch(Exception $e){
    exit();
}

// table作成
$sql = "CREATE TABLE bracket (id mediumint not null, quantity int, p int, r int, c int, primary key(id))";
$stmt = $pdo->prepare($sql);
if($stmt->execute(array())){
    echo "テーブル作成成功\n";
} else{
    echo "テーブル作成失敗\n";
}

// ダミーデータinsert
$sql = "INSERT INTO bracket (id, quantity, p) VALUES (:id, :quantity, :p)";
$params = array(
    'id' => 'hoge',
    'quantity' => 2,
    'p' => 1
);
$stmt = $pdo->prepare($sql);
if($stmt->execute($params)){
    echo "ダミーデータ登録成功\n";
} else{
    echo "ダミーデータ登録失敗\n";
}

// update
$sql = "UPDATE bracket SET p=`2` WHERE id=`hoge`";
$stmt = $pdo->prepare($sql);
if($stmt->execute(array())){
    echo "ダミーデータ更新成功<br />";
} else{
    echo "ダミーデータ更新失敗<br />";
}
