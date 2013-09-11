<?php

namespace Model;

class Time {

	/**
	 * [$days description]
	 * @var array
	 */
	private static $days = array("Söndag", "Måndag", "Tisdag", "Onsdag", "Torsdag", "Fredag", "Lördag");

	/**
	 * [$months description]
	 * @var array
	 */
	private static $months = array("Januari", "Februari", "Mars", "April", "Maj", "Juni", "Juli", "Augusti", "September", "Oktober", "November", "December");

	/**
	 * [getFullTimeString description]
	 * @return [type] [description]
	 */
	public function getFullTimeString() {
		$dateArray = getdate();
		$timeString = self::$days[$dateArray["wday"]];
		$timeString .= ", den " . $dateArray["mday"] . " " . self::$months[$dateArray["mon"] -1] . " ";
		$timeString .= "år " . $dateArray["year"] . ".";
		$timeString .= "Klockan är [" . $dateArray["hours"] . ":" . $dateArray["minutes"] .":" . $dateArray["seconds"] . "].";
		
		return $timeString;
	}
}