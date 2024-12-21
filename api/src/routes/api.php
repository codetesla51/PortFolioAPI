<?php
return [
  "GET" => [
    "/projects" => "projectController@index",
    "/projects/{id}" => "projectController@show",
    "/skills" => "skillsController@index",
    "/skills/{id}" => "skillsController@show",
    "/reviews" => "reviewsController@index",
    "/reviews/{id}" => "reviewsController@show",
    "/experinece{id}" => "experienceController@show",
    "/experinece" => "experienceController@index",
    "/count" => "adminController@Count",
  ],
  "POST" => [
    "/projects" => "projectController@store",
    "/skills" => "skillsController@store",
    "/reviews" => "reviewsController@store",
    "/experinece" => "experienceController@store",
    "/email" => "emailController@handleRequest",
    "/register" => "userController@register",
    "/login" => "userController@login",
  ],
  "PUT" => [
    "/projects/{id}" => "projectController@update",
    "/skills/{id}" => "skillsController@update",
    "/reviews/{id}" => "reviewsController@update",
    "/experinece/{id}" => "experienceController@update",
    "/reset" => "resetController@resetUserLimits",
    "/resetReq" => "resetController@deleteAllRecords",
    "/updateAPIKEY" => "userController@regenerateAPIKey",
  ],
  "DELETE" => [
    "/projects/{id}" => "projectController@destroy",
    "/skills/{id}" => "skillsController@destroy",
    "/reviews{id}" => "reviewsController@destroy",
    "/experinece{id}" => "experienceController@destroy",
  ],
];
