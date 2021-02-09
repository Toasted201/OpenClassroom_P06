<?php

namespace App\Service;

interface UserManagerInterface
{
    public function emailResetPassword($form);
    public function isTokenPerempted($user);
    public function emailValidation($user);
    public function newPassword($form, $user);
    public function uploadAvatar($form, $user);
    public function newUser($form, $user);
    public function sendEmail($to, $subjectEmail, $htmlTemplate, $context);
}