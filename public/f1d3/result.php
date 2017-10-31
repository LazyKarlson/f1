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

    $filepath = "./res/results.csv";
    if (($handle = fopen($filepath, "r")) !== FALSE) {

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $i = $data[0];
            $result = '';
            for ($y = 1; $y <= 11; $y++){
                $result .= getPilotID($mysqli, $data[$y]).':';
            }
            $result = rtrim($result, ":");
            addResult($mysqli, $i, $result);
            printf("Inserted %s result.\n", $i);
        }
        fclose($handle);
    }



function getPilotID ($link, $pilotname){
    $query = "SELECT pilot_id FROM pilots WHERE pilotname = '".$pilotname."'";
    $res = mysqli_query($link, $query);
    $row = mysqli_fetch_assoc($res);
    //echo $pilotname;
    return $row['pilot_id'];
}

function addResult($link, $race, $result){
        $query = "INSERT INTO race_result (race_id, positions) VALUES ('$race', '$result')";
        $res = mysqli_query($link, $query);

    return $res;
}

function addResult2($link, $race, $result){
    $query = "INSERT INTO race_result (positions) VALUES ('$result') WHERE race_id = '$race'";
    $res = mysqli_query($link, $query);

return $res;
}

mysqli_close($mysqli);
