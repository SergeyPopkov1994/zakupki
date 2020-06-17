<?php
  include_once('./lib/curl_query.php');
  include_once('./lib/simple_html_dom.php');
  include_once('./lib/SimpleXLSX.php');
  require './lib/sql_connect.php';

  set_time_limit(0);
  libxml_use_internal_errors(true);
  $dbc = mysqli_connect('localhost', 'mysql', 'mysql', 'db_zakupki');


  if(isset($_POST['submit'])) {


//--------------------------------------------------Рабочий код---------------------------------------------------
    $regNumber = filter_var(trim($_POST['regNumber']),FILTER_SANITIZE_STRING);
    //echo $regNumber;
    $checkData = "SELECT `regNumber`, `mnn`, `dosage`, `priceZakupkiGov` FROM `zakupki` WHERE regNumber = '$regNumber'";
    $dataZakupki = mysqli_query($dbc, $checkData);
    //echo mysqli_num_rows($dataZakupki);
    $resultTable =
    '<div class="container-fluid">
      <div class="row justify-content-center">
        <div class="col-md-11" style="margin-top: 100px;">
          <table class="table table-striped table-dark">
            <thead>
              <tr>
               <th scope="col">#</th>
               <th scope="col">МНН</th>
               <th scope="col">ЛЕК. ФОРМА, ДОЗИРОВКА И ЕД. ИЗМЕРЕНИЯ</th>
               <th scope="col">Кол-во в упаковке (ГРЛС)</th>
               <th scope="col">ЦЕНА, ₽ (ГРЛС)</th>
               <th scope="col">ЦЕНА ЗА ЕД., ₽ (Закупки)</th>
             </tr>
            </thead>
            <tbody>';
    $rowTable = 1;
    if(mysqli_num_rows($dataZakupki) < 1) {
      $url = 'https://zakupki.gov.ru/epz/order/notice/ea44/view/common-info.html?regNumber='. $regNumber;
      $refererUrl = 'https://www.google.com';
      $dataFromPage = curl_get($url, $refererUrl);

      if ($dataFromPage['code'] == 200){
        $results = array();
        $str = 1;

        do {
          $mnn = getDataXPath($dataFromPage, "//*[@id='medTable']/table/tbody/tr[$str]/td[2]/text()");
          $dosage = getDataXPath($dataFromPage, "//*[@id='medTable']/table/tbody/tr[$str]/td[3]/text()");
          $priceZakupkiGov = getDataXPath($dataFromPage, "//*[@id='medTable']/table/tbody/tr[$str]/td[5]/text()");
          //*[@id="medTable"]/table/tbody/tr[10]/td[2]/text()
          //*[@id="medTable"]/table/tbody/tr[14]/td[2]/text()
          //*[@id="medTable"]/table/tbody/tr[18]/td[2]/text()
          //*[@id="medTable"]/table/tbody/tr[1]/td[2]/text()
          //*[@id="medTable"]/table/tbody/tr[1]/td[2]/text()
          //https://zakupki.gov.ru/epz/order/notice/ea44/view/common-info.html?regNumber=0378200003020000111
          //https://zakupki.gov.ru/epz/order/notice/ea44/view/common-info.html?regNumber=0378200003020000111
          if(empty($mnn)) {
            break;
          }
          $insDataZakupki = "INSERT INTO zakupki (regNumber, mnn, dosage, priceZakupkiGov)
                             VALUES ('$regNumber', '$mnn', '$dosage', '$priceZakupkiGov')";
          $result = $dbc->query($insDataZakupki);
          if ($result == false) {
              print("Произошла ошибка при выполнении запроса");
              echo mysqli_error($dbc);
          }
          if($str == 1) {
            $str = $str + 5;
          } else {
            $str = $str + 4;
          }

          //echo $mnn;
          echo $str;
        } while(!empty($mnn));

        $checkData = "SELECT `regNumber`, `mnn`, `dosage`, `priceZakupkiGov` FROM `zakupki` WHERE regNumber = '$regNumber'";
        $dataZakupki = mysqli_query($dbc, $checkData);
        //echo mysqli_num_rows($dataZakupki);
        while($rowZakupki = mysqli_fetch_assoc($dataZakupki)) {
            $descr = explode(",", $rowZakupki['dosage']);
            $form = filter_var(trim($descr[0]),FILTER_SANITIZE_STRING);
            $dosage = filter_var(trim($descr[1]),FILTER_SANITIZE_STRING);
            $unit = filter_var(trim($descr[2]),FILTER_SANITIZE_STRING);
            //echo "Форма: $form; Дозировка: $dosage; Единица измерения: $unit<br />\n";
            $mnnZakupki = filter_var(trim($rowZakupki['mnn']),FILTER_SANITIZE_STRING);
            $priceZakupki = filter_var(trim($rowZakupki['priceZakupkiGov']),FILTER_SANITIZE_STRING);
            // $selectGrls = "SELECT `mnn`, `dosage`, `price` FROM `grls`
            //                WHERE mnn = '$mnnZakupki' AND
            //                count_in_pack = 1 AND
            //                UPPER(dosage) like UPPER('%$form%') AND
            //                UPPER(dosage) like UPPER('%$dosage%');

            $selectGrls = "SELECT `mnn`, `dosage`, `price`, `count_in_pack` FROM `grls`
                           WHERE mnn = '$mnnZakupki' AND
                           dosage like ('%$form%') AND
                           dosage like ('%$dosage%')";

             $dataGrls = mysqli_query($dbc, $selectGrls);
             if ($dataGrls == false) {
                 print("Произошла ошибка при выполнении запроса");
                 echo mysqli_error($dbc);
             }
             //echo mysqli_num_rows($dataGrls);
             while($rowGrls = mysqli_fetch_assoc($dataGrls)) {
               $resultTable = $resultTable.'<tr><th scope="row">'.$rowTable++.'</th>'.'<td>'.$rowGrls['mnn'].'</td>';
               $resultTable = $resultTable.'<td>'.$rowGrls['dosage'].'</td>'.'<td>'.$rowGrls['count_in_pack'].'</td>'.'<td>'.$rowGrls['price'].'</td>'.'<td>'.$priceZakupki.'</td>';
             }
        }

        mysqli_close($dbc);
      }
    } else {
      //echo "Данные есть в БД";
      //echo mysqli_num_rows($dataZakupki);
      while($rowZakupki = mysqli_fetch_assoc($dataZakupki)) {
          $descr = explode(",", $rowZakupki['dosage']);
          $form = filter_var(trim($descr[0]),FILTER_SANITIZE_STRING);
          $dosage = filter_var(trim($descr[1]),FILTER_SANITIZE_STRING);
          $unit = filter_var(trim($descr[2]),FILTER_SANITIZE_STRING);
          //echo "Форма: $form; Дозировка: $dosage; Единица измерения: $unit<br />\n";
          $mnnZakupki = filter_var(trim($rowZakupki['mnn']),FILTER_SANITIZE_STRING);
          $priceZakupki = filter_var(trim($rowZakupki['priceZakupkiGov']),FILTER_SANITIZE_STRING);
          // $selectGrls = "SELECT `mnn`, `dosage`, `price` FROM `grls`
          //                WHERE mnn = '$mnnZakupki' AND
          //                count_in_pack = 1 AND
          //                UPPER(dosage) like UPPER('%$form%') AND
          //                UPPER(dosage) like UPPER('%$dosage%');

          $selectGrls = "SELECT `mnn`, `dosage`, `price`, `count_in_pack` FROM `grls`
                         WHERE mnn = '$mnnZakupki' AND                         
                         count_in_pack = 1 AND
                         dosage like ('%$form%') AND
                         dosage like ('%$dosage%')";

           $dataGrls = mysqli_query($dbc, $selectGrls);
           if ($dataGrls == false) {
               print("Произошла ошибка при выполнении запроса");
               echo mysqli_error($dbc);
           }
           //echo mysqli_num_rows($dataGrls);
           while($rowGrls = mysqli_fetch_assoc($dataGrls)) {
             $resultTable = $resultTable.'<tr><th scope="row">'.$rowTable++.'</th>'.'<td>'.$rowGrls['mnn'].'</td>';
             $resultTable = $resultTable.'<td>'.$rowGrls['dosage'].'</td>'.'<td>'.$rowGrls['count_in_pack'].'</td>'.'<td>'.$rowGrls['price'].'</td>'.'<td>'.$priceZakupki.'</td>';
           }
      }
    }
//--------------------------------------------------Рабочий код---------------------------------------------------
  }

  function getDataXPath($dataFromPage,$path){
    $doc = new DOMDocument();
    $doc->loadHTML($dataFromPage['data']);
    $xpath = new DOMXpath($doc);
    $data="";
    $nodelist = $xpath->query($path);
    $node_counts = $nodelist->length;
    if ($node_counts) {
      foreach ($nodelist as $element) {
            $data= $data.$element->nodeValue;
      }
    }
    return $data;
  }
?>

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
  <?php echo $resultTable; ?>
  <p><a href="/">Вернуться на главную</a></p>
  <!-- <div class="container-fluid">
    <div class="row justify-content-center">
      <div class="col-md-10" style="margin-top: 100px;">
    <table class="table table-striped table-dark">
    <thead>
      <tr>
        <th scope="col">#</th>
        <th scope="col">Имя</th>
        <th scope="col">Фамилия</th>
        <th scope="col">Username</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <th scope="row">1</th>
        <td>Mark</td>
        <td>Otto</td>
        <td>@mdo</td>
      </tr>
      <tr>
        <th scope="row">2</th>
        <td>Jacob</td>
        <td>Thornton</td>
        <td>@fat</td>
      </tr>
      <tr>
        <th scope="row">3</th>
        <td>Larry</td>
        <td>the Bird</td>
        <td>@twitter</td>
      </tr>
    </tbody>
  </table>
</div>
</div>
</div> -->



 <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
 <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
 <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
 </body>
 </html>
