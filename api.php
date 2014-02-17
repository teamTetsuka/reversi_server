<?php
if($_SERVER['REQUEST_METHOD'] != 'GET'){
    echo json_encode(array('result' => 'error'));
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
    echo json_encode(array('result' => 'error'));
    exit();
}

switch($params[1]){
    case 's':
        $sql = "SELECT * FROM bracket WHERE quantity=1 and rownum=1";
        $mod_value = array();
        $stmt = $pdo->prepare("SELECT * FROM bracket WHERE quantity=1 and rownum=1");
        $stmt->execute(array());
        if(empty($stmt->fetchAll(PDO::FETCH_ASSOC)) || $stmt->fetchAll(PDO::FETCH_ASSOC) == false){
            $sql = "INSERT INTO bracket (quantity, p) VALUES (1, 1)";
            $mod_value = array();
        }
        break;
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
        echo jsonencode(array('result' => 'error'));
        echo '不正なアクセスです';
        exit();
}

$stmt = $pdo->prepare($sql);
$stmt->execute($mod_value);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
