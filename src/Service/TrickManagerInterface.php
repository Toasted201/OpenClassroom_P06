<?php

namespace App\Service;

interface TrickManagerInterface
{
    public function newTrick($trick, $user);
    public function newComment($comment, $trick, $user);
    public function editTrick($trick);
}