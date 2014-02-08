<?php
    // 環境変数参照
    $vcap_services = getenv('VCAP_SERVICES');
    if($vcap_services === false){
          exit();
    }

    // 環境変数からDB設定を取得
    $vcap_services_json = json_decode($vcap_services);
    var_dump($vcap_services_json);
?>

<?php
    // json形式で出力
    //$json = array('session_id' => $session_id, 'player_id' => $player_id);

    //echo json_encode($json);
