<?php
$mysqli = mysqli_connect("localhost", "yura", "1q2w3e", "mydb");
if (!$mysqli) {
    echo "Error: Unable to connect to MySQL." . PHP_EOL;
    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
    echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
    exit;
}
echo "Success: A proper connection to MySQL was made!" . PHP_EOL;
printf("Initial character set: %s\n", mysqli_character_set_name($mysqli));
echo "Host information: " . mysqli_get_host_info($mysqli) . PHP_EOL;
if (!mysqli_set_charset($mysqli, "utf8")) {
    printf("Error loading character set utf8: %s\n", mysqli_error($mysqli));
    exit();
} else {
    printf("Current character set: %s\n", mysqli_character_set_name($mysqli));
}

for ($y = 1; $y <= 18; $y++){
    echo "Counting race $y...".PHP_EOL;
     $forecasts = getForecasts($mysqli, $y);
     $race_results = explode(":", getRaceResult($mysqli, $y));
     foreach ($forecasts as $predict){
         $forecast = explode(":",$predict['forecast']);
         //echo $predict['forecast_id'].PHP_EOL;
         $res_string = countPoints($race_results, $forecast);
         addForecastResult($mysqli, $predict['forecast_id'], $res_string);
     }
}

function getForecasts ($link, $race_id){
    $query = "SELECT forecast_id, user_id, forecast FROM forecasts WHERE race_id = '".$race_id."'";
    $res = mysqli_query($link, $query);
    $rows = mysqli_fetch_all($res,MYSQLI_ASSOC);
    return $rows;

}

function getRaceResult ($link, $race_id){
    $query = "SELECT positions FROM race_result WHERE race_id = '".$race_id."'";
    $res = mysqli_query($link, $query);
    $row = mysqli_fetch_assoc($res);
    return $row['positions'];
}

function addForecastResult($link, $f_id, $result){
        $query = "INSERT INTO fcast_result (forecast_id, result_string) VALUES ('$f_id', '$result')";
        $res = mysqli_query($link, $query);

    return $res;
}

function countPoints($place, $predict){
    $result = '';
    $points = 0;
    $total = 0;
    for($i = 0; $i <= 9; $i++){
        $prev = $i - 1;
        $next = $i + 1;
        if ($place[$i] == $predict[$i]){
            $points = 10;
        }
        elseif ($place[$next] == $predict[$i]){
            $points = 5;
        }
        elseif ($prev != -1 AND $place[$prev] == $predict[$i]){
            $points = 5;
        }
        else {
            $points = 0;
        }
        $total = $total + $points;
        $result .= $points.":";
        }
     return  $result = $result.$total;
}

mysqli_close($mysqli);
