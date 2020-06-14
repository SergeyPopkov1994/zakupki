<!DOCTYPE html>
<html lang="en" class="full-height">
<head>
    <title>Поис недобросовестных сделок</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <script src="https://use.fontawesome.com/releases/v5.0.8/js/all.js"></script>
    <link  href="../static/css/main.css" th:href="@{/css/main.css}" rel="stylesheet"/>
    <style>
     body {
      background: url(./static/img/background.jpg); /* Цвет фона и путь к файлу */
     }
  </style>
</head>
<body>

<h2 class="text-center" style="color: white; margin-top: 100px;">Поиск недобросовестных сделок с сайта <a href="https://zakupki.gov.ru/">государственных закупок</a></h5>
<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-md-6" style="margin-top: 100px;">
      <form action="/action_page.php" method="POST">
        <div class="form-group">
          <h5 style="color: white">Введите регистрационный номер сделки</h2>
          <input type="text" class="form-control" id="regNum" name="regNumber" placeholder="0851200000620002605">
        </div>
        <button type="submit" name="submit" class="btn-lg btn-block btn btn-primary">Отправить</button>
      </form>
    </div>
  </div>
</div>
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</body>
</html>
