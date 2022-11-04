<?php
require_once 'vendor/autoload.php';
require_once './GoogleProfile.php';


$newInstance = new GoogleProfile();
$google_user_id = $newInstance->fetchGoogleUserId();

// 
echo "<pre>";
print_r($google_user_id);
echo '</pre>';
// DBのgoogle_user_idを検索
// 見つからなければ、Youtube Data APIで取得したデータと一緒にinsertする
// ログイン処理をする
