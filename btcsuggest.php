<?php
// Ticker
$ticker_service = 'https://www.mercadobitcoin.net/api/ticker/';
$ticker_json    = json_decode(file_get_contents($ticker_service));
$last_buy       = $ticker_json->ticker->buy;
$last_sell      = $ticker_json->ticker->sell;

// Getting the inverval (Default)
$period = 1;

if ( isset($argv[1]) ) {
    $period = (int) $argv[1];
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

echo PHP_EOL;
echo sprintf('====== [ %s ] ======', $suggestion);
echo PHP_EOL;
echo PHP_EOL;
echo '-------------------[ BUY ]-----------------------';
echo PHP_EOL;
echo sprintf(
    "Min.: %s - Max: %s - Avg: %s - Current: %s", 
    min($buyprice), 
    max($buyprice),
    $buyavg,
    $last_buy
);
echo PHP_EOL;
echo PHP_EOL;
echo '-------------------[ SELL ]-----------------------';
echo PHP_EOL;
echo sprintf(
    "Min.: %s - Max: %s - Avg: %s - Current: %s", 
    min($sellprice), 
    max($sellprice),
    $selavg,
    $last_sell
);

echo PHP_EOL;
echo PHP_EOL;
echo sprintf('---- Buy At: %s - Sell At: %s --------', $buy_at, $sell_at);

echo PHP_EOL;
echo PHP_EOL;
echo sprintf('-------------[ period: %s days ]-----------------', $period);
?>s