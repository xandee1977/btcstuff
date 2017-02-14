Number.prototype.formatMoney = function(c, d, t){
var n = this, 
    c = isNaN(c = Math.abs(c)) ? 2 : c, 
    d = d == undefined ? "." : d, 
    t = t == undefined ? "," : t, 
    s = n < 0 ? "-" : "", 
    i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))), 
    j = (j = i.length) > 3 ? j % 3 : 0;
   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
 };

function loadData(callback) {
  // Server configs
  var server    = 'http://ec2-54-207-20-63.sa-east-1.compute.amazonaws.com/util/btcstuff';
  var filename  = 'btcsuggest_json.php';
  var config =  {'period' : 1};
  var url = server + '/' + filename + jsonToQueryString(config);

  var xhr = new XMLHttpRequest();
  xhr.open('get', url, true);
  xhr.send(null);

  xhr.onreadystatechange = function() 
  { 
     var content = document.getElementById('content');
     if( xhr.readyState === 4 ) {
          if( xhr.status === 200 ) {
               var data = JSON.parse(xhr.responseText);
               callback(data);
          }
      }
  }

}

function jsonToQueryString(json) {
    return '?' + 
        Object.keys(json).map(function(key) {
            return encodeURIComponent(key) + '=' +
                encodeURIComponent(json[key]);
        }).join('&');
}

loadData(onData);

function onData(data) {
  var loader = document.getElementById('cnt-loader');
  loader.setAttribute("style", "display:none");

  var container = document.getElementById('cnt-mbtc');
  container.setAttribute("style", "display:block");

  var containers = [
    { 'container' : 'buy-min',  'value' : data.buy.min.formatMoney(2, ',', '.') },
    { 'container' : 'buy-max',  'value' : data.buy.max.formatMoney(2, ',', '.') },
    { 'container' : 'buy-curr', 'value' : data.buy.last.formatMoney(2, ',', '.') },
    { 'container' : 'buy-avg',  'value' : data.buy.avg.formatMoney(2, ',', '.') },
    { 'container' : 'sell-min', 'value' : data.sell.min.formatMoney(2, ',', '.') },
    { 'container' : 'sell-max', 'value' : data.sell.max.formatMoney(2, ',', '.') },
    { 'container' : 'sell-curr','value' : data.sell.last.formatMoney(2, ',', '.') },
    { 'container' : 'sell-avg', 'value' : data.sell.avg.formatMoney(2, ',', '.') }    
  ];

  var mbtcTitle = document.getElementById('mbtc-suggestion');
  if (data.suggestion.indexOf("BUY") !=-1) {
    mbtcTitle.className = "buy-color-text";
    mbtcTitle.innerHTML = data.suggestion;
  }  

  if (data.suggestion.indexOf("SELL") !=-1) {
    mbtcTitle.className = "sell-color-text";
    mbtcTitle.innerHTML = data.suggestion;
  }
  
  for(var i=0; i< containers.length; i++) {
    document.getElementById(containers[i].container).innerHTML = containers[i].value;
  }
}
