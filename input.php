<?php
session_start();

require 'validation.php';

header('X-FRAME-OPTIONS:DENY');


//hにてサニタイズ（消毒） JavaScriptの行動を防ぐ
function h($str){
  return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

//$pageFlagを定数として、0であれば入力。1であれば確認。2であれば完了。 
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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
  <title>Document</title>
</head>
<body>

<?php if($pageFlag === 0) : ?>
<!-- CSRF 偽物のinput.php ->悪意のあるページ 対策として$_SESSIONを使ったトークンを発行 -->
<?php 
  if(!isset($_SESSION['csrfToken'])) {
    $csrfToken = bin2hex(random_bytes(32));
    $_SESSION['csrfToken'] = $csrfToken;
  }
  $token = $_SESSION['csrfToken'];
?>

<?php if(!empty($errors) && !empty($_POST['btn_confirm'])) :?>
  <?php echo '<ul>' ;?>
  <?php foreach($errors as $error) {
    echo '<li>' . $error . '</li>';
  }
  ?>

  <?php echo '</ul>' ;?>
<?php endif ;?>
<div class="container">
  <div class="row">
    <div class="col-md-6">
      <form action="input.php" method="post">
      <div class="form-group">
        <label for="your_name">氏名</label>
          <input type="text" class="form-control" id="your_name" name="your_name"  value="<?php if(!empty($_POST['your_name'])) {echo h($_POST['your_name']) ;}?>" required>
      </div>
      <div class="form-group">
        <label for="email">メールアドレス</label>
        <input type="email" class="form-control" id="email" name="email" value="<?php if(!empty($_POST['email'])) {echo h($_POST['email']) ;}?>" required>
      </div>
      <div class="form-group">
        <label for="url">ホームページ</label>
        <input type="url" class="form-control" id="url" name="url" value="<?php if(!empty($_POST['url'])) {echo h($_POST['url']) ;}?>" required>
      </div>
      <div class="form-group">
        <label for="gender">性別</label>
          <div class="form-check form-check-inline">
          <input type="radio" class="form-check-input" id="gender1" name="gender" value="0" <?php if(!isset($_POST['gender']) && $_POST['gender'] === '0') {echo 'checked' ;}?>>
          <label class="form-check-label" for="gender1">男性</label>
          </div>
          <div class="form-check form-check-inline">
          <input type="radio" class="form-check-input" id="gender2"  name="gender" value="1" <?php if(!isset($_POST['gender']) && $_POST['gender'] === '1') {echo 'checked' ;}?>>
          <label class="form-check-label" for="gender2">女性</label>
          </div>
    </div>
      <div class="form-group">
        <label for="age">年齢</label>
        <select class="form-control" id="age" name="age">
          <option value="">選択して下さい</option>
          <option value="0" >〜19歳</option>
          <option value="1" >20〜29歳</option>
          <option value="2" >30〜39歳</option>
          <option value="3" >40〜49歳</option>
          <option value="4" >50〜59歳</option>
          <option value="5" >60歳〜</option>
        </select>
      </div>
      <div class="form-group">
        <label for="contact">お問い合わせ内容</label>
        <textarea class="form-control" id="contact" name="contact" rows="3">
        <?php if(!empty($_POST['contact'])) {echo h($_POST['contact']) ;}?>
        </textarea> 
      </div>
      <div class="form-check">
      <input type="checkbox" class="form-check-input" id="caution" name="caution" value="1">
      <label class="form-check-label" for="caution">注意事項にチェックする</label>
      </div>
      <input type="submit" class="btn btn-info" value="確認する" name="btn_confirm">
      <input type="hidden" name="csrf" value="<?php echo $token; ?>">
      </form>
      </div>
    </div>  
  </div>
</div>
<?php endif; ?>

<?php if($pageFlag === 1) : ?>
<?php if($_POST['csrf'] === $_SESSION['csrfToken']) :?>
  <div class="container">
    <div class="row">
      <div class="col-md-6">
      <form action="input.php" method="post">
        <div class="form-group">
          <label for="your_name">名前</label>
          <?php echo h($_POST['your_name']) ;?>
        </div>
        <div class="form-group">
          <label for="email">メールアドレス</label>
          <?php echo h($_POST['email'] );?>
        </div>
        <div class="form-group">
          <label for="url">ホームページ</label>
          <?php echo h($_POST['url'] );?>
        </div>
        <div class="form-group">
          <label for="gender">性別</label>
          <?php
          if($_POST['gender'] === '0') {echo '男性';}
          if($_POST['gender'] === '1') {echo '女性';}
          ?>
        </div>
        <div class="form-group">
          <label for="age">年齢</label>
          <?php 
          if($_POST['age'] === '0') {echo '〜19歳';} 
          if($_POST['age'] === '1') {echo '20〜29歳';} 
          if($_POST['age'] === '2') {echo '30〜39歳';} 
          if($_POST['age'] === '3') {echo '40〜49歳';} 
          if($_POST['age'] === '4') {echo '50〜59歳';} 
          if($_POST['age'] === '5') {echo '60歳〜';} 
          ?>
        </div>
        <div class="form-group">
          <label for="contact">お問い合わせ内容</label>
          <?php echo h($_POST['contact'] );?>
        </div>
        <input type="submit" class="btn btn-info" name="back" value="戻る">
        <input type="submit" class="btn btn-info" name="btn_submit" value="送信する">
        <input type="hidden" name="your_name" value="<?php echo h($_POST['your_name'] );?>">
        <input type="hidden" name="email" value="<?php echo h($_POST['email']) ;?>">
        <input type="hidden" name="url" value="<?php echo h($_POST['url']) ;?>">
        <input type="hidden" name="gender" value="<?php echo h($_POST['gender']) ;?>">
        <input type="hidden" name="age" value="<?php echo h($_POST['age']) ;?>">
        <input type="hidden" name="contact" value="<?php echo h($_POST['contact']) ;?>">
        <input type="hidden" name="csrf" value="<?php echo h($_POST['csrf']) ;?>">
      </form>
      </div>
    </div>
  </div>
<?php endif; ?>

<?php endif; ?>

<?php if($pageFlag === 2) : ?>
<?php if($_POST['csrf'] === $_SESSION['csrfToken']) :?>
送信が完了しました。

<?php endif; ?>
<?php endif; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
</body>
</html>
