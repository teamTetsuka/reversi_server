<?php
    // 環境変数参照
    $vcap_services = getenv('VCAP_SERVICES');
    if($vcap_services === false){
          exit();
    }

    // 環境変数からDB設定を取得
    $vcap_services_json = json_decode($vcap_services);
    //var_dump($vcap_services_json);

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
        //print_r( $e );
        exit();
    }

    $sql = "SELECT * FROM fixtures WHERE quantity=1";
    $params = array();
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php
    // json形式で出力
    //$json = array('session_id' => $session_id, 'player_id' => $player_id);

    //echo json_encode($json);
