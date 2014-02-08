<?php
$params = explode("/", $_SERVER['PATH_INFO']);
var_dump($params);

// 環境変数参照
$vcap_services = getenv('VCAP_SERVICES');
if($vcap_services === false){
    exit();
}

// 環境変数からDB設定を取得
$vcap_services_json = json_decode($vcap_services);

// DB接続
$pdo = null;
try {
    $pdo = new PDO(
        "mysql:host={$db->host};port={$db->port};dbname={$db->name}",
        $db->username,
        $db->password,
        array(
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET utf8"
        )
    );
} catch (Exception $e) {
    exit();
}

// RESTful的な
if(!isset($params[1])){
    echo '不正なアクセスです';
}

switch($params[1]){
    case 's':
        $stmt = $pdo->prepare("SELECT * FROM oppose WHERE quantity=1 and rownum=1");
        $stmt->execute(array());
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;
    case 'g':
        $stmt = $pdo->prepare("SELECT * FROM oppose WHERE id=$params[2]");
        $stmt->execute(array());
        echo $json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;
    case 'p':
        break;
    default:
        echo '不正なアクセスです';
}
