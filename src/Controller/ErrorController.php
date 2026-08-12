<?php

namespace App\Controller;

class ErrorController extends AbstractController
{
    public function error404(): void
    {
        $this->renderError(404);
    }

    public function errorUser(): void
    {
        $this->render('error/user');
    }

    public function errorValidStart(): void
    {
        $this->render('error/valid-start');
    }

    public function errorValidEnd(): void
    {
        $this->render('error/valid-end');
    }

    public function errorQrcodeValidation(): void
    {
        $this->render('error/qrcode-validation');
    }
}