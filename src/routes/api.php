<?php
return [
  "GET" => [
    "/projects" => "projectController@index",
    "/projects/{id}" => "projectController@show",
  ],
  "POST" => [
    "/projects" => "projectController@store",
  ],
  "PUT" => [
    "/projects/{id}" => "projectController@update",
  ],
  "DELETE" => [
    "/projects/{id}" => "projectController@destroy",
  ],
];
