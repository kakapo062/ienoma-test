<?php 
session_start();
// POSTされたトークンを取得
$token = isset($_POST["token"]) ? $_POST["token"] : "";
// セッション変数のトークンを取得
$session_token = isset($_SESSION["token"]) ? $_SESSION["token"] : "";
// セッション変数のトークンを削除
unset($_SESSION["token"]);
// POSTされたトークンとセッション変数のトークンの比較
if($token != "" && $token == $session_token) {
// フォーム送信実行
//エスケープ処理やデータチェックを行う関数のファイルの読み込み
require 'libs/functions.php';

//POSTされたデータがあれば変数に格納、なければ NULL（変数の初期化）
$name = isset( $_POST[ 'name' ] ) ? $_POST[ 'name' ] : NULL;
$email = isset( $_POST[ 'email' ] ) ? $_POST[ 'email' ] : NULL;
$phone = isset( $_POST[ 'phone' ] ) ? $_POST[ 'phone' ] : NULL;


//送信ボタンが押された場合の処理
if (isset($_POST['submitted'])) {
    //POSTされたデータに不正な値がないかを別途定義した checkInput() 関数で検証
    $_POST = checkInput( $_POST );
    //filter_var を使って値をフィルタリング
    if(isset($_POST['email'])) {
      //全ての改行文字を削除
      $email = str_replace(array("\r", "\n", "%0a", "%0d"), '', $_POST['email']);
      //E-mail の形式にフィルタ
      $email = filter_var($email, FILTER_VALIDATE_EMAIL);
    }
    if(isset($_POST['phone'])) {
      //数値の形式にフィルタ（数字、+ 、- 記号 以外を除去）
      $phone = filter_var($_POST['phone'], FILTER_SANITIZE_NUMBER_INT);
    }
    //POST でのリクエストの場合
    if ($_SERVER['REQUEST_METHOD']==='POST') {
      // //メールアドレス等を記述したファイルの読み込み
      // require 'libs/mailvars.php';
      //メール本文の組み立て。値は h() でエスケープ処理
      $mail_body = "イエノマLPから、" . "\n" . "以下の内容にてお申し込みがありました。" . "\n";
      $mail_body .= "*****************************" . "\n";
      $mail_body .= "【対象サイト】" . "\n" ;
      $mail_body .= "イエノマLP" . "\n" ;
      $mail_body .= "https://ienoma.com/ibaraki/" . "\n\n" ;
      $mail_body .=  "【氏名】\n" .h($name) . "\n\n";
      $mail_body .=  "【電話番号】\n" .h($phone) . "\n\n";
      $mail_body .=  "【メールアドレス】\n" .h($email) . "\n\n";
      $mail_body .= "*****************************" . "\n\n\n";
      $mail_body .= "▼お申し込み内容は以下に反映しています。" . "\n";
      $mail_body .= "イエノマLP｜お申し込み内容" . "\n";
      $mail_body .= "https://docs.google.com/spreadsheets/d/133Yefgmmok5ERkWfLplOAKgvh7a-IqwwqODEgF2WGXo/edit#gid=199559877";
  
      //-------- sendmail を使った自動通知メールの送信処理 ------------
      // 通知メールの送信先、自動返信メールの送付元のメアドを入力
      define('MAIL_TO', "ienoma@t-wind.co.jp");
      //メールの宛先（To）の名前
      define('MAIL_TO_NAME', "");
      //Return-Pathに指定するメールアドレス
      define('MAIL_RETURN_PATH', "ienoma@t-wind.co.jp");
      //自動返信の返信先名前（自動返信を設定する場合）
      define('AUTO_REPLY_NAME', "");

      //メールの宛先（名前<メールアドレス> の形式）。値は mailvars.php に記載
      // $mailTo = mb_encode_mimeheader(MAIL_TO_NAME) ."<" . MAIL_TO. ">";

      // 宛先
      $to = "ienoma@t-wind.co.jp";
      // 件名
      $subject = "【イエノマLP】お申し込みがありました";
      // 送信元
      $from = "イエノマ <k.kanazono@osen.co.jp>";
      // 送信元メールアドレス
      $from_mail = "k.kanazono@osen.co.jp";
      // 送信者名
      $from_name = "イエノマ";
      //mbstringの日本語設定
      mb_language( 'ja' );
      mb_internal_encoding( 'UTF-8' );
  
      // 送信者情報（From ヘッダー）の設定
      // $headers = "From: ienoma@t-wind.co.jp";
      // 送信者情報の設定
      $headers = '';
      $headers .= "Content-Type: text/plain \r\n";
      $headers .= "Return-Path: " . $from_mail . " \r\n";
      $headers .= "From: k.kanazono@osen.co.jp \r\n";
      $headers .= "Sender: " . $from ." \r\n";
      $headers .= "Reply-To: " . $from_mail . " \r\n";
      $headers .= "Organization: " . $from_name . " \r\n";
      $headers .= "X-Sender: " . $from_mail . " \r\n";
      $headers .= "X-Priority: 3 \r\n";
  
      //メールの送信結果を変数に代入
      // $result = mb_send_mail( $mailTo, $subject, $mail_body, $headers );
      $result = mb_send_mail( $to, $subject, $mail_body, $headers );
      //メールが送信された場合の処理
      if ( $result ) {
         
      //-------- 自動返信メールの処理 ------------
  //ヘッダー情報
  $ar_header = "MIME-Version: 1.0\n";
  //Return-Pathに指定するメールアドレス
  $returnMail = MAIL_RETURN_PATH; //
  // AUTO_REPLY_NAME や MAIL_TO は mailvars.php で定義
  $ar_header .= "From: " . "家づくりの窓口 イエノマ" . " <" . MAIL_TO . ">\n";
  $ar_header .= "Reply-To: " . mb_encode_mimeheader( AUTO_REPLY_NAME ) . " <" . MAIL_TO . ">\n";
  //件名
  $ar_subject = 'お申し込みを受け付けました | 家づくりの窓口イエノマ';
  //本文
  $ar_body = $name." 様\n\n";
  $ar_body .= "この度は、家づくりの窓口 イエノマにお申込いただき、" . "\n";
  $ar_body .= "誠にありがとうございました。" . "\n";
  $ar_body .= "お申込情報を確認しご連絡差し上げますので、" . "\n";
  $ar_body .= "ご対応のほど何卒よろしくお願いします。" . "\n\n";
  $ar_body .= "─ご送信内容の確認─────────────────" . "\n";
  $ar_body .= "【お名前】" . "\n";
  $ar_body .= $name . "\n\n";
  $ar_body .= "【お電話番号】" . "\n";
  $ar_body .= $phone . "\n\n";
  $ar_body .= "【メールアドレス】" . "\n";
  $ar_body .= $email . "\n\n";
  // $ar_body .= "お申し込み日時：" . date("Y年m月d日 D H時i分") . "\n";
  $ar_body .= "───" . "\n";
  $ar_body .= "家づくりの窓口 イエノマ" . "\n";
  $ar_body .= "──────────────────────────";
 //自動返信メールを送信（送信結果を変数 $result2 に代入）
 if ( ini_get( 'safe_mode' ) ) {
  $result2 = mb_send_mail( $email, $ar_subject, $ar_body , $ar_header  );
} else {
  $result2 = mb_send_mail( $email, $ar_subject, $ar_body , $ar_header , '-f' . $returnMail );
}
  //再読み込みによる二重送信の防止
  //自動返信の送信結果（$result2）をパラメータに追加
  $params = '?result='. $result .'&result2=' . $result2;
  //サーバー変数 $_SERVER['HTTPS'] が取得出来ない環境用
  if(isset($_SERVER['HTTP_X_FORWARDED_PROTO']) and $_SERVER['HTTP_X_FORWARDED_PROTO'] === "https") {
    $_SERVER['HTTPS'] = 'on';
  }
  $url = (empty($_SERVER['HTTPS']) ? 'http://' : 'https://').$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME']; 
  header('Location:' . $url . $params);
                          //----------googleスプレッドシート送信---------------------
  //composerでインストールしたライブラリを読み込む
// require_once __DIR__ . '../../../../../vendor/autoload.php';
require('../../../../vendor/autoload.php');
// サービスアカウント認証で作成したjsonファイル.
$key_file = __DIR__.'/../../../../key/ienoma-0f02b8344520.json';
  // 対象のスプレッドシートのIDを指定
$sheet_id = '133Yefgmmok5ERkWfLplOAKgvh7a-IqwwqODEgF2WGXo';
//アカウント認証インスタンスの生成
$client = new Google_Client();
//サービスキーをセット
$client->setAuthConfig($key_file);
$client->setApplicationName('Sheet Api Test');
//スコープを以下の内容でセット
$scopes = [Google_Service_Sheets::SPREADSHEETS];
$client->setScopes($scopes);

  // シートの書き込みインスタンスを生成
$sheet = new Google_Service_Sheets($client);

try {
  $range = 'フォーム申込';
  $values = array(
      array(
//連携したスプレッドシートの最終行に「A列（一番左）」から順に追記される
          date('Y-m-d H:i:s'),////問い合わせ日時 2019-12-31 23:59:59
          $name,//★フォームの入力値の取得
          $phone,
          $email,
      ),
  );

  $appendBody = new Google_Service_Sheets_ValueRange([
      'values' => $values
  ]);
// 書き込みの実行
  $response = $sheet->spreadsheets_values->append(
    $sheet_id,
    $range,
    $appendBody,
    ['valueInputOption' => 'USER_ENTERED']
  );
} catch (Google_Exception $e) {
  $errors = json_decode($e->getMessage(), true);
  $err = "code : " . $errors["error"]["code"] . "\r\n";
  $err .= "message : " . $errors["error"]["message"];
  echo "Google_Exception" . $err;
}
        //空の配列を代入し、すべてのPOST変数を消去
        $_POST = array();
  
        //変数の値も初期化
        $name = '';
        $email = '';
        $phone = '';
      }
    }
}
} else {
}
?>

<?php include ( dirname(__FILE__) . '/../header.php' ); ?>

<main class="thanks_main">
    <div class="thanks_wrap">
        <div class="thanks_inner">
            <h2 class="thanks_title">お申し込みが完了しました。</h2>
            <p class="thanks_para">家づくり相談のお申込みありがとうございました。</p>
            <p class="thanks_para">内容を確認の上、担当者からご連絡させていただきます。</p>
            <p class="thanks_para">なお、しばらくたっても連絡がない場合は、
                <br>お客様によりご入力いただいた情報に誤りがある場合がございます。
            </p>
            <p class="thanks_para">その際は、お手数ですが再度お問い合わせいただけますと幸いです。</p>
        </div>
    </div>
</main>

<?php include ( dirname(__FILE__) . '/../footer.php' ); ?>