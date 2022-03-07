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
require '../libs/functions.php';

//POSTされたデータがあれば変数に格納、なければ NULL（変数の初期化）
$name = isset( $_POST[ 'name' ] ) ? $_POST[ 'name' ] : NULL;
$email = isset( $_POST[ 'email' ] ) ? $_POST[ 'email' ] : NULL;
$phone = isset( $_POST[ 'phone' ] ) ? $_POST[ 'phone' ] : NULL;
$subject = "【イエノマLP】お問い合わせがありました";


//送信ボタンが押された場合の処理
if (isset($_POST['submitted'])) {
    //POSTされたデータに不正な値がないかを別途定義した checkInput() 関数で検証
    $_POST = checkInput( $_POST );
    //filter_var を使って値をフィルタリング
    if(isset($_POST['name'])) {
      //スクリプトタグがあれば除去
      $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    }
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
    if(isset($_POST['message'])) {
      $message = filter_var($_POST['message'], FILTER_SANITIZE_STRING);
    }
    //POST でのリクエストの場合
    if ($_SERVER['REQUEST_METHOD']==='POST') {
      //メールアドレス等を記述したファイルの読み込み
      require '../libs/mailvars.php';
      //メール本文の組み立て。値は h() でエスケープ処理
      $mail_body = "宅建Job【企業用LP】から、" . "\n" . "以下の内容にてお問い合わせがありました。" . "\n";
      $mail_body .= "*****************************" . "\n";
      $mail_body .= "【対象サイト】" . "\n" ;
      $mail_body .= "宅建Job【企業用LP】" . "\n" ;
      $mail_body .= "https://takken-job.com/s-client/" . "\n\n" ;
      $mail_body .=  "【会社名】\n" .h($company) . "\n\n";
      $mail_body .=  "【氏名】\n" .h($name) . "\n\n";
      $mail_body .=  "【電話番号】\n" .h($phone) . "\n\n";
      $mail_body .=  "【メールアドレス】\n" .h($email) . "\n\n";
      $mail_body .=  "【備考】\n" .h($message) . "\n\n";
      $mail_body .= "*****************************" . "\n\n\n";
      $mail_body .= "▼お問い合わせ内容は以下に反映しています。" . "\n";
      $mail_body .= "【採用企業向け】お問い合わせフォーム｜宅建Job（回答）" . "\n";
      $mail_body .= "https://docs.google.com/spreadsheets/d/19H5tliCmxmM3arq9yE3x7Ga2C8f6dNfTwTishQODHks/edit#gid=0";
  
      //-------- sendmail を使ったメールの送信処理 ------------
      //メールの宛先（名前<メールアドレス> の形式）。値は mailvars.php に記載
      $mailTo = mb_encode_mimeheader(MAIL_TO_NAME) ."<" . MAIL_TO. ">";
  
      //mbstringの日本語設定
      mb_language( 'ja' );
      mb_internal_encoding( 'UTF-8' );
  
      // 送信者情報（From ヘッダー）の設定
      $headers = "From: k.kanazono@hayfield.jp";
  
      //メールの送信結果を変数に代入
      $result = mb_send_mail( $mailTo, $subject, $mail_body, $headers );
      //メールが送信された場合の処理
      if ( $result ) {
                          //----------googleスプレッドシート送信---------------------
  //composerでインストールしたライブラリを読み込む
require_once __DIR__ . '/../../../../../vendor/autoload.php';
// サービスアカウント認証で作成したjsonファイル.
$key_file = __DIR__ . '/../../../../../key/s-client-lp-aa267a1a1685.json';
  // 対象のスプレッドシートのIDを指定
$sheet_id = '19H5tliCmxmM3arq9yE3x7Ga2C8f6dNfTwTishQODHks';
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
  $range = 'form-data';
  $values = array(
      array(
//連携したスプレッドシートの最終行に「A列（一番左）」から順に追記される
          date('Y-m-d H:i:s'),////問い合わせ日時 2019-12-31 23:59:59
          $company,//★フォームの入力値の取得
          $name,
          $phone,
          $email,
          $message
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
        $company = '';
        $name = '';
        $email = '';
        $phone = '';
        $message = '';


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