<?php

namespace App\Services\User;

use App\Entity\Chat;
use App\Entity\EmailConfirmation;
use App\Entity\Feedback;
use App\Entity\Message;
use App\Entity\User;
use App\Repository\EmailConfirmationRepository;
use App\Services\RandomizeServices;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use App\Repository\FilesRepository;
use App\Repository\ChatRepository;
use App\Repository\FeedbackRepository;
use App\Repository\MessageRepository;

class FeedbackServices
{
    public function __construct(
        EntityManagerInterface $em,
        EmailConfirmationRepository $emailConfirmationRepository,
        FilesRepository $filesRepository,
        ChatRepository $chatRepository,
        FeedbackRepository $feedbackRepository,
        MessageRepository $messageRepository
    ) {
        $this->em = $em;
        $this->emailConfirmationRepository = $emailConfirmationRepository;
        $this->filesRepository = $filesRepository;
        $this->chatRepository = $chatRepository;
        $this->feedbackRepository = $feedbackRepository;
        $this->messageRepository = $messageRepository;
    }

    /**
     * Сохраняем / Апдейтим запрос
     *
     * @param User $user
     * 
     * @return EmailConfirmation|null
     */
    public function saveFeedback(User $user, FormInterface $form): ?Feedback
    {
        $title = $form->get('title')->getData();
        $message_feedback = $form->get('message')->getData();
        $files = $form->get('files')->getData();
        $chat_id = $form->get('chat_id')->getData();
        if (!empty($chat_id)) {
            $chat = $this->chatRepository->find($chat_id);
            $feedback = $chat->getFeedback();
            $feedback->setUpdatedAt(time());
        } else {
            //Save feedback
            $feedback = new Feedback();
            $feedback->setTitle($title)->setStatus(false)->setCreatedAt(time())->setUser($user);
            $this->em->persist($feedback);
            $this->em->flush();

            // Save chat
            $chat = new Chat();
            $chat->setFeedback($feedback);
            $this->em->persist($chat);
            $this->em->flush();
        }

        // Save message
        $message = new Message();
        $message->setChat($chat)->setUser($user)->setComment($message_feedback)->setCreateTime(time())->setUpdateTime(time());
        $this->em->persist($message);
        $this->em->flush();

        if (!empty($files)) {
            foreach ($files as $file) {
                $find_file = $this->filesRepository->find($file);
                if ($find_file) {
                    $find_file->setMessage($message);
                    $this->em->persist($find_file);
                    $this->em->flush();
                }
            }
        }

        return $feedback;
    }

    /**
     * Получаем обращение
     *
     * @param User $user
     * @param mixed $feedback_id
     * @param bool $last_message
     * 
     * @return [type]
     */
    public function getFeedback(User $user, $feedback_id, $last_message = false)
    {
        $result = [];

        $feedback = $this->feedbackRepository->find($feedback_id);
        if (empty($feedback)) {
            return $result;
        }
        $chat = $this->chatRepository->findOneBy(['feedback' => $feedback->getId()]);
        if (empty($chat)) {
            return $result;
        }
        if (!$last_message) {
            $messages = $this->messageRepository->findBy(['chat' => $chat->getId(), 'user' => [$user]], ['id' => 'DESC']);
        } else {
            $messages = $this->messageRepository->findBy(['chat' => $chat->getId(), 'user' => [$user]], ['id' => 'DESC'], 1);
        }
        $result['feedbackInfo'] = [
            'chatId' => $chat->getId(),
            'feedbackId' => $feedback->getId(),
            'title' => $feedback->getTitle(),
            'status' => $feedback->getStatus(),
            'createdAt' => date("Y-m-d H:i:s", $feedback->getCreatedAt()),
            'messageCount' => 0,
        ];

        if ($messages) {
            $result['feedbackInfo']['messageCount'] = count($messages);
            foreach ($messages as $key => $message) {
                $files = $this->filesRepository->findBy(['message' => $message]);
                $result['messages'][$key] = [
                    'userId' => $user->getId(),
                    'firstName' => $user->getFirstName(),
                    'lastName' => $user->getLastName(),
                    'messageId' => $message->getId(),
                    'message' => $message->getComment(),
                    'created_at' => date("Y-m-d H:i:s", $message->getCreateTime()),
                    'files' => null,
                ];
                if ($files) {
                    foreach ($files as $file) {
                        $result['messages'][$key]['files'] = [
                            'id' => $file->getId(),
                            'filepath' => $file->getFilePath(),
                            'type' => $file->getFiletype(),
                            'is_image' => $file->getIsImage(),
                        ];
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @param User $user
     * @param int $closed
     * @param int $page
     * 
     * @return [type]
     */
    public function getFeedbackRequests(User $user, int $closed, int $page)
    {
        $offset = ($page - 1) * $this->feedbackRepository::PAGE_OFFSET;
        $limit = $this->feedbackRepository::PAGE_OFFSET;

        $result_total_count = $this->feedbackRepository->count(['user' => $user->getId(), 'status' => $closed]);
        $requests = $this->feedbackRepository->findBy(['user' => $user->getId(), 'status' => $closed], ['id' => 'DESC'], $limit, $offset);

        $result['result'] = [];
        $result['result_total_count'] = $result_total_count;
        foreach ($requests as $key => $request) {
            $result['result'][$key] = $this->getFeedback($user, $request->getId(), true);
        }
        return $result;
    }
}
