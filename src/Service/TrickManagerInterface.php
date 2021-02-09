<?php

namespace App\Service;

interface TrickManagerInterface
{
    public function newTrick($form, $trick, $user);
    public function newComment($form, $trick, $user);
    public function editTrick($form,$trick);
}