<?php

namespace App\MessageHandler;

use App\Message\CommentNotification as CommentNotificationMessage;
use App\Notification\CommentNotification;
use App\Repository\CommentaireRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Notifier\NotifierInterface;

class CommentNotificationHandler implements MessageHandlerInterface
{
    private CommentaireRepository $commentaireRepository;
    private NotifierInterface $notifier;

    public function __construct(CommentaireRepository $commentaireRepository, NotifierInterface $notifier)
    {
        $this->commentaireRepository = $commentaireRepository;
        $this->notifier = $notifier;
    }

    public function __invoke(CommentNotificationMessage $notification)
    {
        $commentaire = $this->commentaireRepository->find($notification->getCommentId());

        if (!$commentaire) {
            return;
        }

        $notification = new CommentNotification($commentaire->getContenu());
        $this->notifier->send($notification);
    }
}
