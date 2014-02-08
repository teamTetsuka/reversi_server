<?php
//    // 環境変数参照
//    $vcap_services = getenv('VCAP_SERVICES');
//    if($vcap_services === false){
//          exit();
//    }
//
//    // 環境変数からDB設定を取得
//    $vcap_services_json = json_decode($vcap_services);
//    //var_dump($vcap_services_json);
//
//    // DB接続
//    $pdo = null;
//    try {
//        $pdo = new PDO(
//            "mysql:host={$db->host};port={$db->port};dbname={$db->name}",
//            $db->username,
//            $db->password,
//            array(
//                PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET utf8"
//            )
//        );
//    } catch (Exception $e) {
//        //print_r( $e );
//        exit();
//    }

    $services_json = getenv('VCAP_SERVICES');
    $services = json_decode($services_json, true);
    $config = null;
    foreach ($services as $name => $service) {
        if (0 === stripos($name, 'mysql')) {
            $config = $service[0]['credentials'];
            break;
        }
    }
    is_null($config) && die('MySQL service information not found.');

    var_dump($config);

//    $db_hostname = $config["hostname"];
//    $db_hostport = $config["port"];
//    $db_username = $config["user"];
//    $db_password = $config["password"];
?>

<?php
    // json形式で出力
    //$json = array('session_id' => $session_id, 'player_id' => $player_id);

    //echo json_encode($json);
