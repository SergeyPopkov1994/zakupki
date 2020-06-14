<?php
  include_once('./lib/curl_query.php');
  include_once('./lib/simple_html_dom.php');
  include_once('./lib/SimpleXLSX.php');

  set_time_limit(0);
  libxml_use_internal_errors(true);

  if(isset($_POST['submit'])) {

//--------------------------------------------------Рабочий код---------------------------------------------------
    // $regNumber = filter_var(trim($_POST['regNumber']),FILTER_SANITIZE_STRING);
    // $url = 'https://zakupki.gov.ru/epz/order/notice/ea44/view/common-info.html?regNumber='. $regNumber;
    // $refererUrl = 'https://www.google.com';
    // $dataFromPage = curl_get($url, $refererUrl);
    //
    // if ($dataFromPage['code'] == 200){
    //   $results = array();
    //   $str = 1;
    //   do {
    //     $results['mnn'] = getDataXPath($dataFromPage, "//*[@id='medTable']/table/tbody/tr[$str]/td[2]/text()");
    //     $results['dosage'] = getDataXPath($dataFromPage, "//*[@id='medTable']/table/tbody/tr[$str]/td[3]/text()");
    //     $results['priceZakupkiGov'] = getDataXPath($dataFromPage, "//*[@id='medTable']/table/tbody/tr[$str]/td[5]/text()");
    //     echo $results['mnn'];
    //     echo $results['dosage'];
    //     echo $results['priceZakupkiGov'];
    //     $str = $str + 5;
    //   } while(!empty($results['mnn']));
    // }
//--------------------------------------------------Рабочий код---------------------------------------------------
    // if ( $xlsx = SimpleXLSX::parse('./lib/lp2020-06-11-1.xlsx') ) {
    // 	print_r( $xlsx->rows() );
    // } else {
    // 	echo SimpleXLSX::parseError();
    // }

    // if ( $xlsx = SimpleXLSX::parse('./lib/lp2020-06-11-1.xlsx') ) {
    //   echo '<h1>Parsing Result</h1>';
    //   echo '<table border="1" cellpadding="3" style="border-collapse: collapse">';
    //   list($cols,) = $xlsx->dimension();
    // 	foreach( $xlsx->rows() as $k => $r ) {
    //     if ($k == 0) continue;
    //     for( $i = 0; $i < $cols; $i++)
    //     {
    //       echo '<td>'.( (isset($r[$i])) ? $r[$i] : '&nbsp;' ).'</td>';
    //     }
    // 		//echo '<tr><td>'.implode('</td><td>', $k ).'</td></tr>';
    //     echo '</tr>';
    // 	}
    // 	echo '</table>';
    // 	// or $xlsx->toHTML();
    // } else {
    // 	echo SimpleXLSX::parseError();
    // }

    $xlsx = SimpleXLSX::parse('./lib/lp2020-06-11-1.xlsx');
    //echo 'Sheet Name 2 = '.$xlsx->sheetName(0);

    echo $xlsx->getCell(0,'A4'); // reads D12 cell from second sheet
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
            $data= $data.$element->nodeValue . "\n";
      }
    }
    return $data;
  }
?>
