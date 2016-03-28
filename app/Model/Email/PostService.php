<?php
namespace Bulletpoint\Model\Email;

interface PostService {
    public function send(Message $message);
}