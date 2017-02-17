<?php
// Allow from any origin
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PATCH, PUT, DELETE");         
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    exit(0);
}

// Ticker
$ticker_service = 'https://www.mercadobitcoin.net/api/ticker/';
$ticker_json    = json_decode(file_get_contents($ticker_service));
$last_buy       = $ticker_json->ticker->buy;
$last_sell      = $ticker_json->ticker->sell;

// Getting the inverval (Default)
$period = 1;

if ( isset($_REQUEST['period']) ) {
    $period = (int) $_REQUEST['period'];
}

$initial_date = strtotime(sprintf("-%s days", ($period)));
$final_date   = strtotime("-0 days");

// Trade service
$trade_service = sprintf(
	"https://www.mercadobitcoin.net/api/trades/%s/%s/",
	$initial_date,
	$final_date
);

// Ask for server the trades
$trade_json = file_get_contents($trade_service);
$trade_data = json_decode($trade_json, TRUE);

// Buy trades
$buyprice  = [];

//Sell trades
$sellprice = [];

foreach($trade_data as $item) {
    if ( $item['type'] === 'buy') {
        $buyprice[$item['date']] = $item['price'];
    }
    if ( $item['type'] === 'sell') {
        $sellprice[$item['date']] = $item['price'];
    }    
}

// Averages
$buyavg = array_sum($buyprice) / count($buyprice);
$selavg = array_sum($sellprice) / count($sellprice);

// Suggestions
$suggestion = 'Please wait...';

// Suggestions
$buy_at  = min($buyprice)  + ( min($buyprice) * ((0.3)/100) );
$sell_at = max($sellprice) - ( max($sellprice) * ((0.3)/100) );

if ( $last_sell >= $sell_at  ) {
    $suggestion = 'PLEASE SELL NOW!';
}
if ( $last_buy <= $buy_at  ) {
    $suggestion = 'PLEASE BUY NOW!';
}

$result = [
    'buy' => [
        'min'  => min($buyprice),
        'max'  => max($buyprice),
        'last' => $last_buy,
        'avg'  => $buyavg,
    ],
    'sell' => [
        'min'  => min($sellprice),
        'max'  => max($sellprice),
        'last' => $last_sell,
        'avg'  => $selavg,
    ],
    'buy_at' => $buy_at,
    'sell_at' => $sell_at,
    'suggestion' => $suggestion,
    'period' => $period,
];

header('Content-Type: application/json');
echo json_encode($result);
?>
