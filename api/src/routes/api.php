<?php
return [
  "GET" => [
    "/projects" => "projectController@index",
    "/projects/" => "projectController@index",
    "/projects/{id}" => "projectController@show",
    "/skills" => "skillsController@index",
    "/skills/" => "skillsController@index",
    "/skills/{id}" => "skillsController@show",
  ],
  "POST" => [
    "/projects" => "projectController@store",
    "/skills" => "skillsController@store",
  ],
  "PUT" => [
    "/projects/{id}" => "projectController@update",
    "/skills/{id}" => "skillsController@update",
  ],
  "DELETE" => [
    "/projects/{id}" => "projectController@destroy",
    "/skills/{id}" => "skillsController@destroy",
  ],
];
