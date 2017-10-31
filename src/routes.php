<?php
// Routes

$app->get('/users', function ($request, $response, $args) {
    $sth = $this->db->prepare("SELECT user_id, username FROM users ORDER BY username");
   $sth->execute();
   $users = $sth->fetchAll();
   return $this->response->withJson($users);
});

$app->get('/races', function ($request, $response, $args) {
    $sth = $this->db->prepare("SELECT race_id, racename FROM races ORDER BY race_id");
   $sth->execute();
   $races = $sth->fetchAll();
   return $this->response->withJson($races);
});

$app->get('/users/[{id}]', function ($request, $response, $args) {
    $sth = $this->db->prepare("SELECT user_id, username FROM users WHERE user_id=:id");
   $sth->bindParam("id", $args['id']);
   $sth->execute();
   $users = $sth->fetchObject();
   return $this->response->withJson($users);
});

$app->get('/races/[{id}]', function ($request, $response, $args) {
    $sth = $this->db->prepare("SELECT race_id, racename FROM races WHERE race_id=:id");
   $sth->bindParam("id", $args['id']);
   $sth->execute();
   $races = $sth->fetchObject();
   return $this->response->withJson($races);
});

$app->get('/forecast/user/[{id}]', function ($request, $response, $args) {
    $sth = $this->db->prepare("SELECT * FROM forecasts WHERE user_id=:id");
   $sth->bindParam("id", $args['id']);
   $sth->execute();
   $forecasts = $sth->fetchAll();
   return $this->response->withJson($forecasts);
});

$app->get('/forecast/race/[{id}]', function ($request, $response, $args) {
    $sth = $this->db->prepare("SELECT * FROM forecasts INNER JOIN users ON forecasts.user_id=users.user_id INNER JOIN fcast_result ON forecasts.forecast_id=fcast_result.forecast_id INNER JOIN race_result ON forecasts.race_id=race_result.race_id WHERE forecasts.race_id=:id");
   $sth->bindParam("id", $args['id']);
   $sth->execute();
   $forecasts = $sth->fetchAll();
   return $this->response->withJson($forecasts);
});
