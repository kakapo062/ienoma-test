<?php
//メールの宛先（To）のメールアドレス
// 本番反映時には、s.takken@hayfield.jpと入力する
define('MAIL_TO', "k.kanazono@hayfield.jp");
//メールの宛先（To）の名前
define('MAIL_TO_NAME', "");
//Return-Pathに指定するメールアドレス
define('MAIL_RETURN_PATH', '$email');
//自動返信の返信先名前（自動返信を設定する場合）
define('AUTO_REPLY_NAME', 'お申し込み企業様');