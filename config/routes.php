<?php

return [
    '/' => ['HomeController', 'index'],
    '/astronomie' => ['AstronomieController', 'index'],
    '/astronomie/{id}' => ['AstronomieController', 'show'],
    '/meteorologie' => ['MeteorologieController', 'index'],
    '/meteorologie/{id}' => ['MeteorologieController', 'show'],
    '/connexion/login' => ['UserController', 'login'],
    '/connexion/signup' => ['UserController', 'signup'],
    '/connexion/user-otp' => ['UserController', 'checkOtp'],
    '/connexion/forgot-password' => ['UserController', 'forgotPassword'],
    '/connexion/reset-code' => ['UserController', 'checkResetOtp'],
    '/connexion/new-password' => ['UserController', 'changePassword'],
    '/connexion/password-changed' => ['UserController', 'passwordChanged'],
    '/connexion/logout' => ['UserController', 'logout'],
    '/newsletter/welcome-newsletter' => ['NewsletterController', 'welcome'],
    '/user/update-profile' => ['UserController', 'updateProfile'],
    '/user/update-newsletter' => ['UserController', 'updateNewsletter'],
    '/cron/check-newsletter' => ['NewsletterCronController', 'checkNewsletter'],
];