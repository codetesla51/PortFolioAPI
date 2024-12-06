<?php
return [
  "GET" => [
    "/projects" => "projectController@index",
    "/projects/" => "projectController@index",
    "/projects/{id}" => "projectController@show",
    "/skills" => "skillsController@index",
    "/skills/" => "skillsController@index",
    "/skills/{id}" => "skillsController@show",
    "/reviews" => "reviewsController@index",
    "/reviews/" => "reviewsController@index",
    "/reviews/{id}" => "reviewsController@show",
    "/experinece{id}" => "experienceController@show",
    "/experinece" => "experienceController@index",
    "/experinece/" => "experienceController@index",
  ],
  "POST" => [
    "/projects" => "projectController@store",
    "/skills" => "skillsController@store",
    "/reviews" => "reviewsController@store",
    "/experinece" => "experienceController@store",
  ],
  "PUT" => [
    "/projects/{id}" => "projectController@update",
    "/skills/{id}" => "skillsController@update",
    "/reviews/{id}" => "reviewsController@update",
    "/experinece/{id}" => "experienceController@update",
  ],
  "DELETE" => [
    "/projects/{id}" => "projectController@destroy",
    "/skills/{id}" => "skillsController@destroy",
    "/reviews{id}" => "reviewsController@destroy",
    "/experinece{id}" => "experienceController@destroy",
  ],
];
