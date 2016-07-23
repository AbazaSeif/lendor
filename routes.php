<?php

$app->get("/", Helper::auth(["redirect" => "/login"]), function () {
    echo "Index";
});

$app->get("/login", function () {
    echo "Login";
});

$app->get("/logout", function () use ($app) {
    // Unset the session
    session_unset();
    // Redirect to login
    $app->redirect("/login");
});

