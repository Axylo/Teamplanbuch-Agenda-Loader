<?php
	/**
	* Helper class for Agenda module
	*
	* @package    Agenda Loader
	* @subpackage Modules
	* @license        GNU/GPL, see LICENSE.php
	* 
	*/

	class Mod_Agenda_Helper {

		public static function getEvents($params) {
			$icsFile 			= htmlentities($params->get('icsFile',''), ENT_COMPAT, 'UTF-8');
			$categories			= htmlentities($params->get('categories',''), ENT_COMPAT, 'UTF-8');
			$timeZone			= htmlentities($params->get('timeZone',''), ENT_COMPAT, 'UTF-8');
			$timeFormat			= htmlentities($params->get('timeFormat',''), ENT_COMPAT, 'UTF-8');

			$monatsnamen = array(1=>"Januar",2=>"Februar",3=>"März",4=>"April",5=>"Mai",6=>"Juni",7=>"Juli",8=>"August",9=>"September",10=>"Oktober",11=>"November",12=>"Dezember");
			$now = date('Y-m-d H:i:s');//current date and time

			// https://daveismyname.blog/reading-events-from-an-ical-calendar-using-php
			function iCalDecoder($file, $categoriesToUse) {
				$ical = file_get_contents($file);
				
				//Die : bei den Kategorien ersetzen mit ;
				foreach(explode(";", $categoriesToUse) as $category){
					$ical = str_replace($category . ":", $category . ";", $ical);
				}
				// Linebreaks correction
				$ical = str_replace('\,', ',', $ical);				

				preg_match_all('/(BEGIN:VEVENT.*?END:VEVENT)/si', $ical, $result, PREG_PATTERN_ORDER);
				for ($i = 0; $i < count($result[0]); $i++) {
					$tmpbyline = explode("\r\n", $result[0][$i]);

					foreach ($tmpbyline as $item) {
						$tmpholderarray = explode(":",$item);
						if (count($tmpholderarray) >1) {
							$majorarray[$tmpholderarray[0]] = $tmpholderarray[1];
						}
					}

					if (preg_match('/DESCRIPTION:(.*)END:VEVENT/si', $result[0][$i], $regs)) {
						$majorarray['DESCRIPTION'] = str_replace("  ", " ", str_replace("\r\n", "", $regs[1]));
					}
					$icalarray[] = $majorarray;
					unset($majorarray);

				}
				return $icalarray;
			}

			// load the ICAL-File
			$events = iCalDecoder($icsFile, $categories);
			
			// Sort entries chronologically
			usort($events, function($a, $b) {return $a['DTSTART'] <=> $b['DTSTART'];}); //https://stackoverflow.com/questions/2699086/how-to-sort-multi-dimensional-array-by-value
			$eventsAgenda = array();

			// do formatting
			foreach($events as $event){
				$startDate 	= $event['DTSTART'];//get date from ical
				$endDate 	= $event['DTEND'];//get date from ical
				
				$startDate 			= str_replace('T', '', $startDate);//remove T
				$startDate 			= str_replace('Z', '', $startDate);//remove Z
				$startAsTimeStamp 	= strtotime($startDate);
				
				// Convert TimeZone: https://stackoverflow.com/questions/19074552/converting-utc-to-a-different-time-zone-in-php
				$startAsDateTime 	= new DateTime(date('Y-m-d G:i:s', $startAsTimeStamp), new DateTimeZone('GMT'));
				$startAsDateTime->setTimezone(new DateTimeZone('Europe/Zurich'));
				
				$dS					= $startAsDateTime->format('d');//get date day
				$mS					= $startAsDateTime->format('n');//get date month
				$yS					= $startAsDateTime->format('Y');//get date year
				$tS					= $startAsDateTime->format('G:i');//get end time
				$eventStartDate		= $startAsDateTime->format('Y-m-d G:i:s');//user friendly date
				
				
				// Do the same for enddate
				$endDate 			= str_replace('T', '', $endDate);//remove T
				$endDate 			= str_replace('Z', '', $endDate);//remove Z
				$endAsTimeStamp 	= strtotime($endDate);
				
				// Convert TimeZone: https://stackoverflow.com/questions/19074552/converting-utc-to-a-different-time-zone-in-php
				$endAsDateTime 	= new DateTime(date($timeFormat, $endAsTimeStamp), new DateTimeZone('GMT'));
				$endAsDateTime->setTimezone(new DateTimeZone($timeZone));
				
				$dE					= $endAsDateTime->format('d');//get date day
				$mE					= $endAsDateTime->format('n');//get date month
				$yE					= $endAsDateTime->format('Y');//get date year
				$tE					= $endAsDateTime->format('G:i');//get end time
				$eventEndDate		= $endAsDateTime->format($timeFormat);//user friendly date
				
				// takes only events which are in future
				if($eventStartDate > $now){
					// takes only categories selected in module settings
					foreach(explode(";", $categories) as $category){
						if (strpos($event['SUMMARY'], $category . ";") !== false){
							$eventsAgenda[] = array(
								'Monatsname' 	=> $monatsnamen[$mS],
								'Datum' 		=> $dS,
								'Jahr'			=> $yS,
								'Titel'			=> substr( $event['SUMMARY'], (strpos( $event['SUMMARY'], ';' ) + 1 )),
								'Zeit_Von_Bis'	=> $tS . ' - ' . $tE,
								'Ort'			=> $event['LOCATION']
							);
						}
					}					
				}
			}
			return $eventsAgenda;
		}
	}
?>