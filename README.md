Great BTC Stuff
============================
Stuff to help you in your BTC Investment decisions.

## BTC Suggest
Get data from Mercado Bitcoin API and suggest to buy or sell your bitcoins.

* Usage:

```bash

php -f btcsuggest.php [[period]]

```

[[period]] = Integer that represents the number of days to get data.

* Output (Sample):

```bash

php -f btcsuggest.php 4         

====== [ PLEASE BUY NOW! ] ======

-------------------[ BUY ]-----------------------
Min.: 2992.99998 - Max: 3049 - Avg: 3023.7985612375 - Current: 2980

-------------------[ SELL ]-----------------------
Min.: 2970 - Max: 3044.9999 - Avg: 2999.9068055605 - Current: 2998.99

---- Buy At: 3001.97897994 - Sell At: 3035.8649003 --------

-------------[ period: 4 days ]-----------------s%

```
