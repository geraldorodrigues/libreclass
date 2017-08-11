<?php

function debug($value)
{
  if ( env('APP_DEBUG', false) ) {
    \Log::debug($value);
  }
}

function maskPhone( $value ) {
  $value = preg_replace('/\D/', '', $value);
  if ( strlen($value) == 10 ) return vsprintf("(%s%s) %s%s%s%s-%s%s%s%s", str_split($value));
  if ( strlen($value) == 11 ) return vsprintf("(%s%s) %s%s%s%s%s-%s%s%s%s", str_split($value));
  return null;
}

function maskCep( $value ) {
  $value = preg_replace('/\D/', '', $value);
  if ( strlen($value) == 8 ) return vsprintf("%s%s%s%s%s-%s%s%s", str_split($value));
  return null;
}


function trimpp($str)
{
  return preg_replace('/\s+/', ' ', trim($str));;
}

function titleCase($string,
  $delimiters = [" ", "-", ".", "'", "O'", "Mc"],
  $exceptions = ['da', 'das', 'de', 'do', 'dos', 'e', 'o', "and", "to", "of", "ou", "no", "com", "em", "sem", "I", "II", "III", "IV", "V", "VI"])
{
    /*
     * Exceptions in lower case are words you don't want converted
     * Exceptions all in upper case are any words you don't want converted to title case
     *   but should be converted to upper case, e.g.:
     *   king henry viii or king henry Viii should be King Henry VIII
     */
    $string = mb_convert_case($string, MB_CASE_TITLE, "UTF-8");
    foreach ($delimiters as $dlnr => $delimiter) {
        $words = explode($delimiter, $string);
        $newwords = array();
        foreach ($words as $wordnr => $word) {
            if (in_array(mb_strtoupper($word, "UTF-8"), $exceptions)) {
                // check exceptions list for any words that should be in upper case
                $word = mb_strtoupper($word, "UTF-8");
            } elseif (in_array(mb_strtolower($word, "UTF-8"), $exceptions)) {
                // check exceptions list for any words that should be in upper case
                $word = mb_strtolower($word, "UTF-8");
            } elseif (!in_array($word, $exceptions)) {
                // convert to uppercase (non-utf8 only)
                $word = ucfirst($word);
            }
            array_push($newwords, $word);
        }
        $string = join($delimiter, $newwords);
   }//foreach
   return $string;
}

function first_upper( $str )
{
  return titleCase($str);
}

function url_no_cache($url)
{
  return "$url?_=". time();
}

function ptdate2isodate($date)
{
  $fDate = explode('/', $date);
  if (count($fDate) < 3)
    $fDate = $date;
  else{
    if (strlen($fDate[2])<4){
      if (intval($fDate[2])>30)
        $fDate[2]='19'.$fDate[2];
      else
        $fDate[2]='20'.$fDate[2];
    }
    $fDate = (new \DateTime($fDate[2] . '-' . $fDate[1] . '-' . $fDate[0]))->format('Y-m-d');
  }
  return $fDate;
}

function regex_accents($value)
{
  $value = mb_strtolower($value, 'UTF-8');
  // letras àáâãäåæ
  $value = str_replace(['a', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ'], 'X', $value);
  $value = str_replace('X', '[a|à|á|â|ã|ä|å|æ]', $value);

  // letras èéêëẽ
  $value = str_replace(['e', 'è', 'é', 'ê', 'ẽ'], 'X', $value);
  $value = str_replace('X', '[e|è|é|ê|ẽ]', $value);

  // letras ìíîïĩ
  $value = str_replace(['i', 'ì', 'í', 'î', 'ï', 'ĩ'], 'X', $value);
  $value = str_replace('X', '[i|ì|í|î|ï|ĩ]', $value);

  // letras ðòóôõöø
  $value = str_replace(['o', 'ð', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø'], 'X', $value);
  $value = str_replace('X', '[o|ð|ò|ó|ô|õ|ö|ø]', $value);

  // letras ùúûü
  $value = str_replace(['u', 'ù', 'ú', 'û', 'ü'], 'X', $value);
  $value = str_replace('X', '[u|ù|ú|û|ü]', $value);

  // letras ñ
  $value = str_replace(['n', 'ñ'], 'X', $value);
  $value = str_replace('X', '[n|ñ]', $value);

  // letras ç
  $value = str_replace(['c', 'ç'], 'X', $value);
  $value = str_replace('X', '[c|ç]', $value);

 // letras ýÿ
  $value = str_replace(['y', 'ý', 'ÿ'], 'X', $value);
  $value = str_replace('X', '[y|ý|ÿ]', $value);

  return $value;
}

function to_int($data)
{
	if ( is_array($data) ) $data = count($data) ? $data[0] : '';
  return (isset($data) && strlen(strval($data))) ? intval($data) : null;
}

function to_float($data)
{
	if ( is_array($data) ) $data = count($data) ? $data[0] : '';
  return (isset($data) && strlen(strval($data))) ? floatval(str_replace(',', '.', $data)) : null;
}

function to_time($d, $t)
{
  if ( strpos($d, '00') === 0 ) {
    $d = '19' . substr($d, 2);
  }
  $time = strlen($d)*strlen($t) ? strtotime("$d $t:00") : null;
  if ( $time > time() ) $time = time();
  else if ( $time < time()-130*365*24*60*60 ) $time = time()-129*365*24*60*60;

  return $time * 1000;
}

function to_array($r)
{
  return isset($r) ? (array)$r : null;
}

function to_array_int($arr)
{
  $arr = to_array($arr);
  if (!$arr) return null;
  foreach ($arr as $key => $value) {
    $arr[$key] = intval($value);
  }
  return $arr;
}

function to_data($d)
{
  if ( is_array($d) ) $d = count($d) ? $d[0] : null;
  $d = ((isset($d) && strlen((string) $d)) || $d === false) ? $d : null;
  $d = ($d === 'true' ? true : $d);
  $d = ($d === 'false' ? false : $d);
  return $d;
}

function to_bool($d)
{
  if ( $d === 'true' || $d === true ) return true;
  if ( $d === 'false' || $d === false ) return false;
  return null;
}

function format($mask,$string)
{
	return  vsprintf($mask, str_split($string));
}
