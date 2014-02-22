<?php
if($_SERVER['REQUEST_METHOD'] != 'GET'){
    echo json_encode(array('result' => 'failure'));
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
    echo json_encode(array('result' => 'failure'));
    exit();
}

switch($params[1]){
    case 's':
        // 相手待ちがいるか
        $sql = "SELECT * FROM bracket WHERE pc=:pc LIMIT 1";
        $mod_value = array('pc' => 1);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($mod_value);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if(empty($result) || $result == false){ // いなかった場合は新規レコードを作成
            $stmt = $pdo->prepare("INSERT INTO bracket (pc, p, n) VALUES (:pc, :p, :n)");
            $stmt->execute(array('pc' => 1, 'p' => 1, 'n' => 0));
            $stmt = $pdo->prepare("SELECT * FROM bracket WHERE pc=:pc LIMIT 1");
            $stmt->execute(array('pc' => 1));
        } else{ // いた場合はレコードを更新
            $stmt = $pdo->prepare("UPDATE bracket SET pc=:pc, p=:p, n=:n WHERE id=:id");
            $stmt->execute(array('pc' => 2, 'p' => 2, 'n' => 1, 'id' => $result[0]['id']));
            $stmt = $pdo->prepare("SELECT * FROM bracket WHERE id=:id");
            $stmt->execute(array('id' => $result[0]['id']));
        }

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($result[0]);
        exit();
    case 'g':
        $sql = "SELECT * FROM bracket WHERE id=:id";
        $mod_value = array('id' => $_GET['id']);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($mod_value);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($result[0]);
        exit();
    case 'p':
        $sql = "SELECT * FROM bracket WHERE id=:id";
        $mod_value = array('id' => $_GET['id']);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($mod_value);
        $tmp = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $sql = "UPDATE bracket SET p=:p, r=:r, c=:c, n=:n WHERE id=:id";
        $mod_value = array(
            'id' => $_GET['id'],
            'p'  => $_GET['p'],
            'r'  => $_GET['r'],
            'c'  => $_GET['c'],
            'n'  => $tmp[0]['n'] + 1
        );
        $stmt = $pdo->prepare($sql);
        $stmt->execute($mod_value);
        $sql = "SELECT * FROM bracket WHERE id=:id";
        $mod_value = array('id' => $_GET['id']);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($mod_value);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(array('n' => $result[0]['n']));
        exit();
    default:
        echo json_encode(array('result' => 'failure'));
        exit();
}
