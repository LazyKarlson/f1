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

for ($i = 1; $i <= 18; $i++) {
    $filepath = "./tmp/".$i.".csv";
    if (($handle = fopen($filepath, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $user = getUserID($mysqli, $data[1]);
            $forecast = '';
            for ($y = 2; $y <= 11; $y++){
                $forecast .= getPilotID($mysqli, $data[$y]).':';
            }
            $forecast = rtrim($forecast, ":");
            $query = "SELECT * FROM forecasts WHERE user_id = '".$user."' AND race_id = ".$i;
            $result = mysqli_query($mysqli, $query);
            if (mysqli_num_rows($result) > 0) {
                updateForecast($mysqli, $i, $user, $forecast, $data[0]);
                printf("Updated %s forecast.\n", $data[1]);
            }
            else {
                addForecast($mysqli, $i, $user, $forecast, $data[0]);
                printf("Inserted %s forecast.\n", $data[1]);
            }

        }
        fclose($handle);
    }
}


function getUserID ($link, $username){
    $query = "SELECT user_id FROM users WHERE username = '".$username."'";
    $res = mysqli_query($link, $query);
    $row = mysqli_fetch_assoc($res);
    return $row['user_id'];
}

function getPilotID ($link, $pilotname){
    $query = "SELECT pilot_id FROM pilots WHERE pilotname = '".$pilotname."'";
    $res = mysqli_query($link, $query);
    $row = mysqli_fetch_assoc($res);
    return $row['pilot_id'];
}

function addForecast($link, $race, $user, $forecast, $timest){
    for ($i = $race; $i <= 20; $i++) {
        $query = "INSERT INTO forecasts (race_id, user_id, forecast, foretime) VALUES ('$i', '$user', '$forecast', '$timest')";
        $res = mysqli_query($link, $query);
    }
    return $res;
}

function updateForecast($link, $race, $user, $forecast, $timest){
    for ($i = $race; $i <= 20; $i++) {
        $query = "UPDATE forecasts SET forecast = '$forecast', foretime = '$timest' WHERE race_id = '$i' AND user_id = '$user'";
        $res = mysqli_query($link, $query);
    }
    return $res;
}

mysqli_close($mysqli);
