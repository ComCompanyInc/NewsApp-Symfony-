<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\News;
use App\Entity\User;
use App\Forms\CommentsForm;
use App\Repository\CommentsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class CommentsPageController extends AbstractController
{
    private CommentsRepository $commentsRepository;
    private EntityManagerInterface $entityManager;

    function __construct(EntityManagerInterface $entityManager, CommentsRepository $commentsRepository)
    {
        $this->entityManager = $entityManager;
        $this->commentsRepository = $commentsRepository;
    }

    /**
     * Контроллер для обработки страницы с комментариями
     * @param News $idNews
     * @param Request $request
     * @param CommentsRepository $commentsRepository
     * @return Response
     */
    #[Route('/comments/{idNews}', name: 'comments')]
    public function commentsPageAction(News $idNews, Request $request, CommentsRepository $commentsRepository): Response
    {
        $newsText = $idNews->getText();
        $dateText = $idNews->getDateCreating();
        $authorName = $this->entityManager->getRepository(User::class)->findOneBy(['id' =>
            $idNews->getAuthor()
        ])->getName();
        $authorSurname =  $this->entityManager->getRepository(User::class)->findOneBy(['id' =>
            $idNews->getAuthor()
        ])->getSurname();

        $comments = new Comments();

        $commentsForm = $this->createForm(CommentsForm::class, $comments);
        $commentsForm->handleRequest($request);

        if($commentsForm->isSubmitted() && $commentsForm->isValid())
        {

            $commentData = $commentsForm->getData();
            $this->commentsRepository->sendComment($commentData, date("Y-m-d H:i:s"), $this->getUser(), $idNews);
            $commentsForm = $this->createForm(CommentsForm::class);
        }

        $commentsSort = $commentsRepository->findBy(['newsId' => $idNews], ['dateCreating' => 'DESC']);

        return $this->render('signIn/checkIn/comments.html.twig', [
            'commentsForm' => $commentsForm,
            'comments' => $commentsSort,
            'newsText' => $newsText,
            'newsDateText' => $dateText,
            'newsAuthorName' => $authorName,
            'newsAuthorSurname' => $authorSurname,
            'idNews' => $idNews->getId(),
            'idUser' => $this->getUser(),
        ]);
    }

    #[Route('/comments/delete/{idComment}', name: 'deleteComments')]
    public function deleteCommentsAction($idComment): JsonResponse
    {
        $comment = $this->commentsRepository->find($idComment);

        $status = 200;
        if ($comment) {
            $this->entityManager->remove($comment);
            $this->entityManager->flush();
            $message = 'Comment deleted successfully';
        } else {
            $message = 'Comment not found';
            $status = 404;
        }

        return new JsonResponse(['message' => $message], $status);
    }
}