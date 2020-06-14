<?php
  function curl_get($pageUrl, $baseUrl, $pauseTime = 4, $retry = true) {
    $errors = [];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $pageUrl);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.61 Mobile Safari/537.36');
    curl_setopt($ch, CURLOPT_REFERER, $baseUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER , false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST , false);

    $response['data'] = curl_exec($ch);
    $ci = curl_getinfo($ch);
    if($ci['http_code'] != 200 && $ci['http_code'] != 404) {
      $errors[] = [1, $pageUrl, $ci['http_code']];
      if($retry) {
        sleep($pauseTime);
        $response['data'] = curl_exec($ch);
        $ci = curl_getinfo($ch);
        if($ci['http_code'] != 200 && $ci['http_code'] != 404){
          $errors[] = [2, $pageUrl, $ci['http_code']];
        }
      }
    }
    $response['code'] = $ci['http_code'];
    $response['errors'] = $errors;

    curl_close($ch);
    return $response;
  }

  function parseContent(DOMXpath $xPath, $query, $compare = '', $lenCut = 0)
{
  $result = [];
  $i = 0;
  //echo $query;
  $q = $xPath->query($query);
  if (empty($compare)){
    foreach ($q as $k => $item) {
      $result[] = mb_substr($item->textContent, $lenCut);
    }
  } else {
    foreach ($q as $k => $item) {
      if (strpos($item->textContent, $compare) !== false){
        $result[$i++] = mb_substr(str_replace("\n", ' ', $item->textContent), $lenCut);
      }
    }
  }
  return $result;
}
?>
