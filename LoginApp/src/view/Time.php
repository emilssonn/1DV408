<?php

namespace View;

class Time {

	/**
	 * Array of days in swedish
	 * @var array
	 */
	private static $daysSwedish = array("Söndag", "Måndag", "Tisdag", "Onsdag", 
								"Torsdag", "Fredag", "Lördag");

	/**
	 * Array of months in swedish
	 * @var array
	 */
	private static $monthsSwedish = array("Januari", "Februari", "Mars", "April", 
									"Maj", "Juni", "Juli", "Augusti", "September", 
									"Oktober", "November", "December");

	/**
	 * Ex: Måndag, den 16 September år 2013. Klockan är [17:48:13].
	 * @return String
	 */
	public function getFullTimeString() {
		$dateArray = getdate();

		$timeString = $this->getDateString($dateArray);

		$timeString .= $this->getTimeString($dateArray);
		
		return $timeString;
	}

	/**
	 * Ex: Måndag, den 16 September år 2013. 
	 * @param  array $dateArray, array from getdate()
	 * @return string
	 */
	private function getDateString($dateArray) {
		$dateString = 	self::$daysSwedish[$dateArray["wday"]] .
					 	", den " . $dateArray["mday"] . " " . self::$monthsSwedish[$dateArray["mon"] -1] .
						" år " . $dateArray["year"] . ". ";
		
		return $dateString;
	}

	/**
	 * Ex: Klockan är [17:48:13].
	 * @param  array $dateArray, array from getdate()
	 * @return string
	 */
	private function getTimeString($dateArray) {
		$timeString = 	"Klockan är [" . sprintf("%02s", $dateArray["hours"]) . ":" . 
						sprintf("%02s", $dateArray["minutes"]) .":" . 
						sprintf("%02s", $dateArray["seconds"]) . "].";
		
		return $timeString;
	}
}