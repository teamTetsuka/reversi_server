<?php
if($_SERVER['REQUEST_METHOD'] != 'GET'){
    echo json_encode(array());
}

$params = explode("/", $_SERVER['PATH_INFO']);

// 環境変数参照
$vcap_services = getenv('VCAP_SERVICES');
if($vcap_services === false){
    exit();
}

// 環境変数からDB設定を取得
$vcap_services_json = json_decode($vcap_services);
$db = $vcap_services_json->{"mysql-5.1"}[0]->credentials;

// DB接続 $pdo = null;
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
    echo json_encode(array());
    exit();
}

switch($params[1]){
    case 's':
        // 相手待ちがいるか
        $sql = "SELECT * FROM bracket WHERE quantity=:quantity LIMIT 1";
        $mod_value = array('quantity' => 1);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($mod_value);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if(empty($result) || $result == false){ // いなかった場合は新規レコードを作成
            $sql = "INSERT INTO bracket (quantity, p) VALUES (:quantity, :p)";
            $mod_value = array('quantity' => 1, 'p' => 1);
            break;
        }

        var_dump($result);

        // いた場合はレコードを更新
        $stmt = $pdo->prepare("UPDATE bracket SET quantity=:quantity, p=:p WHERE id=:id");
        $stmt->execute(array('quantity' => 2, 'p' => 2, 'id' => $result['id']));
        $stmt = $pdo->prepare("SELECT * FROM bracket WHERE id=:id");
        $stmt->execute(array('id' => $result['id']));
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit();
    case 'g':
        $sql = "SELECT * FROM bracket WHERE id=:id";
        $mod_value = array('id' => $_GET['id']);
        break;
    case 'p':
        $sql = "UPDATE bracket SET p=:p, r=:r, c=:c WHERE id=:id";
        $mod_value = array(
            'id' => $_GET['id'],
            'p'  => $_GET['p'],
            'r'  => $_GET['r'],
            'c'  => $_GET['c']
        );
        break;
    default:
        echo jsonencode(array());
        exit();
}

$stmt = $pdo->prepare($sql);
$stmt->execute($mod_value);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
