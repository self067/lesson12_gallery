<?php

//var_dump($_FILES);
$connection = new PDO('mysql:host=jktu.ru; dbname=selto149_php; charset=utf8', 'selto149_php', 'AcademyPHP2@');

if( isset($_POST['submit'])){
  for($i=0, $cnt=count($_FILES['file']['name']); $i < $cnt; $i++){

    $fileTmpName = $_FILES['file']['tmp_name'][$i];
    $fileType = $_FILES['file']['type'][$i];
    $fileError = $_FILES['file']['error'][$i];
    $fileSize = $_FILES['file']['size'][$i];

    $fn = strtolower($_FILES['file']['name'][$i]);

    $lp = strrpos($fn, '.');
    $fileExt = substr($fn, $lp+1);
    $fileName = substr($fn, 0, $lp);
    $fileName = preg_replace('/[0-9]/', '_', $fileName);

    $allowedExt = ['jpg','jpeg','png'];
    if( in_array($fileExt, $allowedExt)) {
      if( $fileSize < 5000000) {
        if( $fileError === 0 ){
          $connection->query("insert into images (`name`, `ext`) values ('$fileName','$fileExt');");
          $lastID = $connection->query("select max(id) from images;");
          $lastID = $lastID->fetchAll();
          $lastID = $lastID[0][0];
          $fileNameNew = $lastID . $fileName . '.' .$fileExt;
          $fileDestination = 'uploads/' . $fileNameNew;
          move_uploaded_file($fileTmpName, $fileDestination);
          echo "Файл $fn успешно загружен в $fileDestination";
        } else { echo "Ошибка $fileError загрузки файла $fn"; }
      } else { echo "Слишком большой размер файла $fn"; }
    } else { echo "Неверный тип файла $fn"; }
  }

}

$data = $connection->query("select * from images;");
echo "<div style='display: flex; align-items: flex-end; flex-wrap: wrap;'>";
foreach ($data as $img) {
  $image = "uploads/".$img['id'].$img['name'].'.'.$img['ext'];
  if( file_exists( $image)) {
    echo "<div>";
    echo "<img width='150' height='150' src=$image>";
    echo "<form method='POST'><button name='delete".$img['id']."' style='display: block; margin: auto;'>
Удалить</form></form></div>";

  }
}

//$login = $connection->query('select * from `login`;');


?>
<style>
    body {
        margin: 50px 100px;
        font-size: 20px;
    }
    input, button {
        outline: none;
        font-size: 20px;
    }
</style>

<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Document</title>
</head>
<body>

<div>Используй ctrl или shift для выбора нескольких файлов</div>
<form method="POST" enctype="multipart/form-data">

  <input type="file" name="file[]" multiple required  title="Используй ctrl или shift для выбора нескольких файлов">

  <button name="submit">Отправить</button>
</form>


</body>
</html>
