<?php

namespace App\Notification;

use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\RecipientInterface;

class CommentNotification extends Notification
{
    private string $commentContent;

    public function __construct(string $commentContent)
    {
        $this->commentContent = $commentContent;
        parent::__construct('New Comment');
    }

    public function getChannels(RecipientInterface $recipient): array
    {
        return ['browser'];
    }

    public function getContent(): string
    {
        return sprintf('A new comment was posted: %s', $this->commentContent);
    }
}
