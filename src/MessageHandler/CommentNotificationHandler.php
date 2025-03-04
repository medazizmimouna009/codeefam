<?php

namespace App\MessageHandler;

use App\Message\CommentNotification;
use App\Repository\CommentaireRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Notification\Notification;

class CommentNotificationHandler implements MessageHandlerInterface
{
    private CommentaireRepository $commentaireRepository;
    private NotifierInterface $notifier;

    public function __construct(CommentaireRepository $commentaireRepository, NotifierInterface $notifier)
    {
        $this->commentaireRepository = $commentaireRepository;
        $this->notifier = $notifier;
    }

    public function __invoke(CommentNotification $notification)
    {
        $commentaire = $this->commentaireRepository->find($notification->getCommentId());

        if (!$commentaire) {
            return;
        }

        $notification = (new Notification('New Comment', ['email']))
            ->content(sprintf('A new comment was posted: %s', $commentaire->getContenu()));

        $this->notifier->send($notification);
    }
}
