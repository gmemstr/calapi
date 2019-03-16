<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$currentMonth = new DateTime('first day of this month');
$previousMonth = new DateTime('first day of last month');

$cal = [
  'previous' => $previousMonth,
  'current' => $currentMonth,
];
$generatedCalendar = [];

foreach ($cal as $key => $month) {
  $m = $month->format('m');
  $range = $month->format('t');

  for ($day = 1; $day <= $range; $day++) {
    $d = $month->modify('+' . $day - 1 . ' days')->format('l');
    $t = $month->modify('+' . $day . ' days')->getTimestamp();

    $generatedCalendar[$m][$day] = [
      'day' => $d,
      'timestamp' => $t,
      'event' => randomEvent(),
    ];
  }
}

$json = json_encode($generatedCalendar);
echo $json;

/**
 * Returns "crypto-random" event from file.
 *
 * @return array
 *   Random event array, with long and short descriptions.
 *
 * @throws \Exception
 *   Throws exception if no good source of randomness is available.
 */
function randomEvent() {
  $eventsFile = fopen('events.txt', 'r');
  $eventsContents = fread($eventsFile, filesize("events.txt"));

  $events = explode("\n", $eventsContents);
  foreach ($events as &$event) {
    if ($event === '') {
      $event = [
        'short' => '',
        'long' => '',
      ];
      continue;
    }
    $exploded = explode('|', $event);
    $event = [
      'short' => $exploded[0],
      'long' => $exploded[1],
    ];
  }

  $shouldReturnEvent = random_int(0, 1);
  $pointer = random_int(0, count($events) - 1);

  $result = $events[$pointer];

  return $shouldReturnEvent === 1 ? $result : '';
}
