<?php

$app->get("/", function () {
    echo "Index";
});

$app->get("/login", Helper::auth($app, "/"), function () {
    echo "Login";
});

$app->get("/logout", function () use ($app) {
    // Unset the session
    session_unset();
    // Redirect to login
    $app->redirect("/login");
});

