=== NXTBridge AE ===

Contributors: scor2k
License: GPLv2 or later.
Donate link: http://nxter.org/nxtbridge
Donate Nxt address: NXT-FRNZ-PDJF-2CQT-DQ4WQ
Tags: Nxt, assets, nxter, NXTBridge
Requires at least: 4.0
Tested up to: 4.7
Stable tag: trunk

Show Nxt assets information on yours Wordpress sites.

== Description ==

The plugin gets data from the Nxt Asset Exchange ([nxt.org](https://nxt.org), [Assets](http://nxter.org/assets)). Add an asset’s ID to the NxtBridge shortcode and the asset’s meta data will be shown in posts or pages.


== Installation ==

Unzip plugin files and upload them under your '/wp-content/plugins/' directory.

Resulted names will be:
  './wp-content/plugins/NXTBridge/*'

  Activate plugin at "Plugins" administration page.

== Howto Use ==

Just insert simple shortcode to your page with the asset ID you want to show. [NXTBridgeAssetInfo id=17290457900272383726] for example. 

You can use next shortcuts:
* [NXTBridgeAssetInfo id=<asset_id>] - It'll be show only information
* [NXTBridgeAssetStock id=<asset_id>] - It'll be show only StockChart info
* [NXTBridgeAssetPrice id=<asset_id>] - It'll be show only buy and sell info
* [NXTBridgeAssetCandlestick id=<asset_id>] - It'll be show candlesticks and volume information
* [NXTBridgeTop50] - It'll be showing most active assets for the last week.

You can customise output by changing styles (css) for the every class you needed.

== About this project ==

NxtBridge is a WordPress plugin series which can turn your Wordpress site into a Nxt Wallet. You can build and customise the wallet by pasting shortcodes into your posts and pages and choose which Nxt features you want to add.

Development priority list:

= NxtBridge AE =
* show asset description on a post or page. Done.
* show buy/sell orders and historic data. In development.

= NxtBridge Marketplace =
* show shop data on a post or page. Pending.

= NxtBridge Wallet =
* login to WordPress with Nxt account ID. Done
* view Nxt account balance in the Dashboard. Done
* create Nxt account for new users. Coded, needs UX
* send NXT tokens, place buy/sell orders.  Coded, needs UX
* local signing of transactions, passphrase is never sent to a server or node. Done

NXT donations to the developer can be sent to: NXT-FRNZ-PDJF-2CQT-DQ4WQ. Thanks!

== Frequently Asked Questions ==
* Pending

== Upgrade Notice ==
* If you do update plugin from version 0.3.1 or earlier all yours settings for NXTBridge plugin will be cleared.

== Screenshots ==
* Pending

== Changelog ==

= 1.0.5 = 
* ReBranding. NXTBridge AE

= 1.0.4 =
* Add some styles and bug fixes

= 1.0.3 =
* Add some styles again :)

= 1.0.2 =
* Fix grid bugs
* Add some styles 

= 1.0.1 =
* Fix bugs

= 1.0.0 =
* Remove MDL css files
* Using like bootstrap grid styles (with nb- prefix)

= 0.4.3 =
* Remove depricated method [NXTBridgeAuto]
* Replace prefix for the virtual page url from nxtbridge to ae (asset exchange).

= 0.4.2 =
* Bug fix :)

= 0.4.0 =
* Disabled shortcut NXTBridgeAuto. Not needed with fake pages. 
* Create new shortcut [NXTBridgeTip account=NXT-FRNZ-PDJF-2CQT-DQ4WQ]. It'll show button with input box on the page. 

= 0.3.6 =
* Bugfix for compatibility with WPML plugin

= 0.3.5 =
* Automatic generate fake page for every TOP50 asset. If page already exists it will be displaed.

= 0.3.4 =
* Bug fix with moment.min.js missing.

= 0.3.3 =
* Add checkbox on the global settings for show or hide Nxt logo on the site page

= 0.3.2 =
* Cleaning users settings (not site settings) for NXTBridge plugin, i.e. account name and agreement.

= 0.3.1 =
* Broadcast SIGNED transaction BYTES to the Nxt network
* Show Nxt ledger like NRS
* Set default user address

= 0.3.0 =
* NXTBridge Wallet beta version

= 0.2.4 =
* Fix bug with Candlestick graphics

= 0.2.3 =
* Fix bug with [NXTBridgeAuto].

= 0.2.2 =
* Added page with settings. You can set up prefix for every asset pages and select default URL to redirect when specific page for asset does not exists.
* If you want, you can add special shortcut [NXTBridgeAuto] to your "default page" to showing automatic information about any Nxt asset. It will be work only with default page or asset page which wasopening by clicking url on TOP50 page.

= 0.2.1 =
* Was added link in TOP50 asset to every items. 
* Administrator settings for plugin allowing to enter default prefix for assets pages.

= 0.2.0 =
* Add shortcut [NXTBridgeTop50] - It'll show top 50 of assets for last 7 days (week).
* Fix decimal's bug

= 0.1.8 =
* Replace API address. Now we can work on https (SSL) sites! 
* Remove shortcut [NXTBridgeAsset]

= 0.1.7 =
* Add new shortcut NXTBridgeAssetCandlestick id=<asset_id>] - It'll be show information about Open, High, Low and Close price and about Volume for this day.

= 0.1.6 =
* Add some styles only

= 0.1.5 =
* [NXTBridgeAssetInfo id=<asset_id>] - It'll be show only information
* [NXTBridgeAssetStock id=<asset_id>] - It'll be show only StockChart info 
* [NXTBridgeAssetPrice id=<asset_id>] - It'll be show only buy and sell info

= 0.1.4 =
* Add prices (ask and bid)

= 0.1.3 =
* Add bower.json
* Add highcharts JS http://www.highcharts.com/stock/demo/basic-line

= 0.1.2 =
* Add ask and bid info
* Update readme

= 0.1.1 =
* Bug Fix

= 0.1.0 =
* Initial release
