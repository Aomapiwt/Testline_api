<?php

/** FINANCIAL_MAX_ITERATIONS */
define('FINANCIAL_MAX_ITERATIONS', 128);

/** FINANCIAL_PRECISION */
define('FINANCIAL_PRECISION', 1.0e-08);

// function AuthenticateUser($cred) {
//     $base64 = base64_encode($cred['user'].":".$cred['pass']);
//     $client = new Client([
//         'base_uri' => 'http://auth-restpi-hostname',
//         'timeout' => 300,
//     'headers' => ['Content-Type' => 'application/json', "Accept" => "application/json", 'Authorization' => "Basic " . $base64],
//         'http_errors' => false,
//         'verify' => false
//     ]);
//     $client = $this->_client($this->praxisAPI, $cred);
//     try {
//         $response = $client->get("/user");
//         $data = json_decode($response->getBody()->getContents(), true);
//         $status = $response->getStatusCode();

//         if($status == 200) {
//             return true;
//         }
//         return false;

//     } catch (\Exception $ex) { 
//         Log::critical($ex);       
//         return Helper::jsonpError("Auth - Unable to get user account details", 400, 400);
//     }
// }
/////////////////////////////////////// AMC /////////////////////////////////
function checkAuthen($code_authen)
{
    try {
        header("content-type:text/javascript;charset=utf-8");
        $code_authen = str_replace("Basic ", "", $code_authen);
        $code_authen = base64_decode($code_authen);
        $arr1 = explode(":", $code_authen);
        $user = $arr1[0];
        $password = $arr1[1];

        if ($user == "amc_app" && $password == "3ebd7cad466bb71e7b6f6f81d119235a") { //@UAT_amc
            $result = "success";
            $response = [
                "status_code"       => 200,
                "status_Message"    => "Success",
                "status_desc"       => "แสดงข้อมูลสำเร็จ",
                'function'          => "checkAuthen",
                "Data"              => $result
            ];
        } else {
            $result = "error";
            $response = [
                "status_code"       => 100,
                "status_Message"    => "Error",
                "status_desc"       => "ข้อมูลไม่ถูกต้อง",
                'function'          => "checkAuthen",
                "Data"              => $result
            ];
        }
    } catch (Exception $e) {
        $response = [
            "status_code"       => $e->getCode(),
            "status_Message"    => "ข้อมูลไม่ถูกต้อง",
            "status_desc"       => $e->getMessage()
        ];
    } finally {
        return $response;
    }
}
function dateDifference($start, $end)
{
    $datediff = strtotime(dateform($end)) - strtotime(dateform($start));
    return floor($datediff / (60 * 60 * 24));
}
function dateform($date)
{
    $d = explode('/', $date);
    return $d[2] . '-' . $d[1] . '-' . $d[0];
}
function getQuarterBefore($month)
{
    $yy = date("Y");
    if ($month <= 3) {
        $yy = $yy - 1;
        $result = $yy . "-12-31";
    } else if ($month <= 6) {
        $result = $yy . "-03-31";
    } else if ($month <= 9) {
        $result = $yy . "-06-30";
    } else if ($month <= 12) {
        $result = $yy . "-09-30";
    }
    return $result;
}
function getDatesFromRange($start, $end, $format = 'Y-m-d')
{

    $array = array();
    // Variable that store the date interval
    // of period 1 day
    $interval = new DateInterval('P1D');

    $realEnd = new DateTime($end);
    $realEnd->add($interval);

    $period = new DatePeriod(new DateTime($start), $interval, $realEnd);

    // Use loop to store date into array
    foreach ($period as $date) {
        $array[] = $date->format($format);
    }

    // Return the array elements
    // Function call with passing the start date and end date
    // $Date = getDatesFromRange('2021-12-01', '2021-12-05');

    // var_dump($Date);

    return $array;
}

function PV($R, $n, $pmt, $m = 1)
{
    $FV = 1 / (1 + ($R / $m));
    return ($pmt * $FV * (1 - pow($FV, $n))) / (1 - $FV);
}

function PV2($investment = 0, $interest_rate = 0, $years)
{
    // get the data from the form
    // $investment = $_POST['investment'];
    // $interest_rate = $_POST['interest_rate'];
    // $years = $_POST['years'];

    // calculate the future value
    $future_value = $investment;
    for ($i = 1; $i <= $years; $i++) {
        $future_value = ($future_value + ($future_value * $interest_rate * .01));
    }
    // apply currency and percent formatting
    $investment_f = '$' . number_format($investment, 2);
    $yearly_rate_f = $interest_rate . '%';
    $future_value_f = '$' . number_format($future_value, 2);
    return array("investment" => $investment_f, "years" => $yearly_rate_f, "future_value" => $future_value_f);
}

function PV3($rate = 0, $nper = 0, $pmt = 0, $pv = 0, $type = 0)
{
    if ($type != 0 && $type != 1) {
        return False;
    }

    // Calculate
    if ($rate != 0.0) {
        return -$pv * pow(1 + $rate, $nper) - $pmt * (1 + $rate * $type) * (pow(1 + $rate, $nper) - 1) / $rate;
    } else {
        return -$pv - $pmt * $nper;
    }
}

function presentValue($rate, $nper, $pmt, $fv = 0, $type = 0)
{
    if ((int)$rate == 0 && (int)$nper == 0 && (int)$pmt == 0 && (int)$fv == 0 && (int)$type == 0) {
        $pv = 0;
    } else {
        if ($rate) {
            // $pv = (-$pmt * (1 + $rate * $type) * ((pow(1 + $rate, $nper) - 1) / $rate) - $fv) / pow(1 + $rate, $nper);
            $p = (pow(1 + $rate, $nper) - 1);
            if ($p == 0 && $rate == 0) $pr = 0;
            else $pr = $p / $rate;

            $pv1 = (-$pmt * (1 + $rate * $type) * $pr - $fv);
            $pv2 = pow(1 + $rate, $nper);
            if ($pv1 == 0 && $pv2 == 0) $pv = 0;
            else $pv = $pv1 / $pv2;
        } else {
            $pv = -$fv - $pmt * $nper;
        }
    }
    return abs($pv);
}

///////////for xirr calc////////////////
function DATEDIFF($datepart, $startdate, $enddate)
{
    switch (strtolower($datepart)) {
        case 'yy':
        case 'yyyy':
        case 'year':
            $di = getdate($startdate);
            $df = getdate($enddate);
            return $df['year'] - $di['year'];
            break;
        case 'q':
        case 'qq':
        case 'quarter':
            die("Unsupported operation");
            break;
        case 'n':
        case 'mi':
        case 'minute':
            return ceil(($enddate - $startdate) / 60);
            break;
        case 'hh':
        case 'hour':
            return ceil(($enddate - $startdate) / 3600);
            break;
        case 'd':
        case 'dd':
        case 'day':
            return ceil(($enddate - $startdate) / 86400);
            break;
        case 'wk':
        case 'ww':
        case 'week':
            return ceil(($enddate - $startdate) / 604800);
            break;
        case 'm':
        case 'mm':
        case 'month':
            $di = getdate($startdate);
            $df = getdate($enddate);
            return ($df['year'] - $di['year']) * 12 + ($df['mon'] - $di['mon']);
            break;
        default:
            die("Unsupported operation");
    }
}

// function XNPV($rate, $values, $dates)
// {
//     if ((!is_array($values)) || (!is_array($dates))) return null;
//     if (count($values) != count($dates)) return null;

//     $xnpv = 0.0;
//     for ($i = 0; $i < count($values); $i++)
//     {
//         $xnpv += $values[$i] / pow(1 + $rate, DATEDIFF('day', $dates[0], $dates[$i]) / 365);
//     }
//     return (is_finite($xnpv) ? $xnpv: null);
// }

function XNPV($rate, $values, $dates)
{

    $valCount = count($values);
    $xnpv = 0.0;
    for ($i = 0; $i < $valCount; ++$i) {

        $datediff = strtotime($dates[$i]) - strtotime($dates[0]);
        $datediff =  round($datediff / (60 * 60 * 24));
        $xnpv += $values[$i] / pow(1 + $rate, $datediff / 365);
    }
    return $xnpv;
}

function XIRR($values, $dates, $guess = 0.1)
{
    if ((!is_array($values)) && (!is_array($dates))) return null;
    if (count($values) != count($dates)) return null;

    // create an initial bracket, with a root somewhere between bot and top
    $x1 = 0.0;
    $x2 = $guess;
    $f1 = XNPV($x1, $values, $dates);
    $f2 = XNPV($x2, $values, $dates);
    for ($i = 0; $i < FINANCIAL_MAX_ITERATIONS; $i++)   //FINANCIAL_MAX_ITERATIONS
    {
        if (($f1 * $f2) < 0.0) break;
        if (abs($f1) < abs($f2)) {
            $f1 = XNPV($x1 += 1.6 * ($x1 - $x2), $values, $dates);
        } else {
            $f2 = XNPV($x2 += 1.6 * ($x2 - $x1), $values, $dates);
        }
    }
    if (($f1 * $f2) > 0.0) return null;

    $f = XNPV($x1, $values, $dates);
    if ($f < 0.0) {
        $rtb = $x1;
        $dx = $x2 - $x1;
    } else {
        $rtb = $x2;
        $dx = $x1 - $x2;
    }

    for ($i = 0; $i < FINANCIAL_MAX_ITERATIONS; $i++)  //FINANCIAL_MAX_ITERATIONS
    {
        $dx *= 0.5;
        $x_mid = $rtb + $dx;
        $f_mid = XNPV($x_mid, $values, $dates);
        if ($f_mid <= 0.0) $rtb = $x_mid;
        if ((abs($f_mid) < FINANCIAL_PRECISION) || (abs($dx) < FINANCIAL_PRECISION)) return $x_mid; //FINANCIAL_ACCURACY
    }
    return null;
}
/////////////////////////////////////////////////////////
