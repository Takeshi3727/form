<?php
session_start();

require 'validation.php';

header('X-FRAME-OPTIONS:DENY');


//hにてサニタイズ（消毒） JavaScriptの行動を防ぐ
function h($str)
{
  return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

//$pageFlagを定数として、0であれば入力。1であれば確認。2であれば完了。 
// CSRF 偽物のinput.php ->悪意のあるページ 対策として$_SESSIONを使ったトークンを発行
$pageFlag = 0;
$errors = validation($_POST);

if(!empty($_POST['btn_confirm']) && empty($errors)) {
  $pageFlag = 1;
}

if(!empty($_POST['btn_submit'])) {
  $pageFlag = 2;
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>
<body>

<?php if($pageFlag === 0) : ?>
<?php 
  if(!isset($_SESSION['csrfToken'])) {
    $csrfToken = bin2hex(random_bytes(32));
    $_SESSION['csrfToken'] = $csrfToken;
  }
  $token = $_SESSION['csrfToken'];
?>
  <form action="input.php" method="post">
  氏名
  <input type="text" name="your_name"  value="<?php if(!empty($_POST['your_name'])) {echo h($_POST['your_name']) ;}?>">
  <br>
  メールアドレス
  <input type="email" name="email" value="<?php if(!empty($_POST['email'])) {echo h($_POST['email']) ;}?>">
  <br>
  ホームページ
  <input type="url" name="url" value="<?php if(!empty($_POST['url'])) {echo h($_POST['url']) ;}?>">
  <br>
  性別
  <input type="radio" name="gender" value="0" <?php if(!isset($_POST['gender']) && $_POST['gender'] === '0') {echo 'checked' ;}?>>
  男性
  <input type="radio" name="gender" value="1" <?php if(!isset($_POST['gender']) && $_POST['gender'] === '1') {echo 'checked' ;}?>>
  女性
  <br>
  年齢
  <select name="age">
    <option value="">選択して下さい</option>
    <option value="0" <?php if(!isset($_POST['age']) && $_POST === '0') {echo 'selected' ;}?>>〜19歳</option>
    <option value="1" <?php if(!isset($_POST['age']) && $_POST['age'] === '1') {echo 'selected' ;}?>>20〜29歳</option>
    <option value="2" <?php if(!isset($_POST['age']) && $_POST['age'] === '2') {echo 'selected' ;}?>>30〜39歳</option>
    <option value="3" <?php if(!isset($_POST['age']) && $_POST['age'] === '3') {echo 'selected' ;}?>>40〜49歳</option>
    <option value="4" <?php if(!isset($_POST['age']) && $_POST['age'] === '4') {echo 'selected' ;}?>>50〜59歳</option>
    <option value="5" <?php if(!isset($_POST['age']) && $_POST['age'] === '5') {echo 'selected' ;}?>>60歳〜</option>
  </select>
  <br>
  お問い合わせ内容
  <textarea name="contact" >
  <?php if(!empty($_POST['contact'])) {echo h($_POST['contact']) ;}?>
  </textarea>
  <br>
  <input type="checkbox" name="caution" value="1">注意事項にチェックする
  <br>

  <input type="submit" value="確認する" name="btn_confirm">
  <input type="hidden" name="csrf" value="<?php echo $token; ?>">
  </form>
<?php endif; ?>

<?php if($pageFlag === 1) : ?>
<?php if($_POST['csrf'] === $_SESSION['csrfToken']) :?>
  <form action="input.php" method="post">
  氏名
  <?php echo h($_POST['your_name']) ;?>
  <br>
  メールアドレス
  <?php echo h($_POST['email'] );?>
  <br>
  ホームページ
  <?php echo h($_POST['url'] );?>
  <br>
  性別
  <?php
    if($_POST['gender'] === '0') {echo '男性';}
    if($_POST['gender'] === '1') {echo '女性';}
  ?>
  <br>
  年齢
  <?php 
    if($_POST['age'] === 0) {echo '〜19歳';} 
    if($_POST['age'] === 1) {echo '20〜29歳';} 
    if($_POST['age'] === 2) {echo '30〜39歳';} 
    if($_POST['age'] === 3) {echo '40〜49歳';} 
    if($_POST['age'] === 4) {echo '50〜59歳';} 
    if($_POST['age'] === 5) {echo '60歳〜';} 
  ?>
  <br>
  お問い合わせ内容
  <?php echo h($_POST['contact'] );?>
  <br>
  <input type="submit" name="back" value="戻る">
  <input type="submit" name="btn_submit" value="送信する">
  <input type="hidden" name="your_name" value="<?php echo h($_POST['your_name'] );?>">
  <input type="hidden" name="email" value="<?php echo h($_POST['email']) ;?>">
  <input type="hidden" name="url" value="<?php echo h($_POST['url']) ;?>">
  <input type="hidden" name="gender" value="<?php echo h($_POST['gender']) ;?>">
  <input type="hidden" name="age" value="<?php echo h($_POST['age']) ;?>">
  <input type="hidden" name="contact" value="<?php echo h($_POST['contact']) ;?>">
  <input type="hidden" name="csrf" value="<?php echo h($_POST['csrf']) ;?>">
  </form>
<?php endif; ?>

<?php endif; ?>

<?php if($pageFlag === 2) : ?>
<?php if($_POST['csrf'] === $_SESSION['csrfToken']) :?>
送信が完了しました。

<?php endif; ?>

<?php endif; ?>
  
</body>
</html>