<?php
$routes = [
  "GET" => [
    "/projects" => "projectController@index",
    "/projects/{$id}" => "projectController@show",
  ],
  "POST" => [
    "/projects" => "projectController@store",
  ],
  "UPDATE" => [
    "/projects" => "projectController@update",
  ],
  "DELETE" => [
    "/projects" => "projectController@destroy",
  ],
];
