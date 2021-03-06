<?php
namespace Lang\Tankerking;

class ApiClient {
	const SORT_PRICE = 'price';
	const SORT_DIST = 'dist';

	const TYPE_E10 = 'e10';
	const TYPE_E5 = 'e5';
	const TYPE_DIESEL = 'diesel';

	private $apiKey;


	public function __construct(string $apiKey) {
		$this->apiKey = $apiKey;
	}


	public function search(float $lat, float $lng, string $type = self::TYPE_DIESEL, int $radius = 5, string $sort = self::SORT_DIST): array {
		$json = file_get_contents("https://creativecommons.tankerkoenig.de/json/list.php?lat={$lat}&lng={$lng}&rad={$radius}&sort={$sort}&type={$type}&apikey={$this->apiKey}");

		if ($json === false) {
			throw new \Exception("FEHLER - Die Tankerkoenig-API konnte nicht abgefragt werden!");
		}

		$data = json_decode($json);

		// Daten der Tankstellen in Array speichern
		$apiResult = $data->stations;

		// Daten der Tankstellen aus Array auslesen und HTML-Code generieren
		$result = [];
		
		foreach ($apiResult as $station) {
			//map to an reasonable layout
			$result[$station->id] = [
				'name' => ($station->name),
				'brand' => ($station->brand),
				'dist' => (float)($station->dist),
				'price' => (float)($station->price),
				'street' => ($station->street),
				'houseNumber' => ($station->houseNumber),
				'postCode' => ($station->postCode),
				'place' => ($station->place),
			];
		}

		return $result;
	}


	public function detail(string $gasStationId) : GasStation {
		$json = file_get_contents("https://creativecommons.tankerkoenig.de/json/detail.php?id={$gasStationId}&apikey={$this->apiKey}");

		if ($json === false) {
			throw new \Exception("FEHLER - Die Tankerkoenig-API konnte nicht abgefragt werden!");
		}

		$data = json_decode($json, true);
		//TODO: check for OK status code
		return GasStation::fromApiArray($data['station']);
	}
}