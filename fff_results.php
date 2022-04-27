<?php
	define("INTERVAL", 5 ); // 5 seconds
	date_default_timezone_set('Europe/Paris');
	echo "DERNIER ARTICLE : " . PHP_EOL;
	
	use GuzzleHttp\Exception\GuzzleException;
	use GuzzleHttp\Client as GuzzleClient;
	
	require 'vendor/autoload.php';

	function run() { // Your function to run every 5 seconds
		$chrono = date('d/m/Y h:i:s', time());

		$httpClient = new GuzzleClient();
		// url to parse. here it's the "Voir toutes les actualités" page of ARBITRES category
		$response = $httpClient->get('https://www.fff.fr/voir_plus/zd6r96un1_1608044686369.html', ['verify' => false]);
		$htmlString = (string) $response->getBody();
		// add this line to suppress any warnings
		libxml_use_internal_errors(true);
		$doc = new DOMDocument();
		$doc->loadHTML($htmlString);
		$xpath = new DOMXPath($doc);
		// Get the titles of each articles
		$titles = $xpath->evaluate('//div[@class="actualities-container"]//a//div[@class="item_toute_actu_cadre flex flex_jc_start flex_ai_start"]//figure[@class="flex article_zoom relative margin_b20 flex_ai_start"]//figcaption[@class="small_9 medium_12 large_12 font_14"]//h3');
		// Get the date of each articles
		$dates = $xpath->evaluate('//div[@class="actualities-container"]//a//div[@class="flex flex_column padding_r font_14 flex_ai_center bold heure_chrono"]');
		$lien = $xpath->evaluate('//div[@class="actualities-container"]//a/@href');
		foreach ($titles as $key => $title) {
			// First element only. The last article available.
			if ($key == 0){
				echo "Last check: " .$chrono . " : " . $dates[$key]->textContent . " => " . $title->textContent . PHP_EOL;
				if ($title->textContent != "Benoît Bastien en Ligue Europa"){
					
					echo PHP_EOL . "Nouvel article disponible ! Fin du traitement." . PHP_EOL;
					exit();
				}
			}
		}
	}

	function start() {
		$active = true;
		$nextTime   = microtime(true) + INTERVAL; // Set initial delay
	
		while($active) {	
			if (microtime(true) >= $nextTime) {
				run();
				$nextTime = microtime(true) + INTERVAL;
			}
		}
	}
	
	start();
?>