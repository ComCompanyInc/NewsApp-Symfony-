<?php

namespace App\Controller;

use App\Entity\News;
use App\Forms\NewsForm;
use App\Repository\NewsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class NewsController extends AbstractController
{
    private NewsRepository $newsRepository;

    function __construct(NewsRepository $newsRepository)
    {
        $this->newsRepository = $newsRepository;
    }

    #[Route('news/addNews', name: 'addNews')]
    function addNewsAction(Request $request): Response
    {
        $state = null;
        $news = new News();

        $addNewsForm = $this->createForm(NewsForm::class, $news);
        $addNewsForm->handleRequest($request);

        if ($addNewsForm->isSubmitted() && $addNewsForm->isValid()) {
            $newsData = $addNewsForm->getData();

            $this->newsRepository->sendNews($newsData, date("Y-m-d H:i:s"), $this->getUser());
            $state = "Новость успешно отправлена!";

            $addNewsForm = $this->createForm(NewsForm::class);
        }

        return $this->render('signIn/checkIn/addNews.html.twig', [
            'addNewsForm' => $addNewsForm,
            'state' => $state
        ]);
    }

    /**
     * контроллер для обработки страницы с новостями
     * @param NewsRepository $newsRepository
     * @return Response
     */

    #[\Symfony\Component\Routing\Annotation\Route('/news', name: 'news')]
    public function newsPageAction(NewsRepository $newsRepository): Response
    {
        $news = $newsRepository->findBy([], ['dateCreating' => 'DESC']);

        return $this->render('signIn/checkIn/news.html.twig', [
            'news' => $news,
            'idUser' => $this->getUser()->getId()
        ]);
    }

    /**
     * Контроллер для удаления новостей
     * @param $idNews
     * @return Response
     */
    #[\Symfony\Component\Routing\Annotation\Route('/news/deleteNews/{idNews}', name: 'deleteNews')]
    public function deleteNewsPageAction($idNews): Response
    {
        $result = $this->newsRepository->deleteNews($this->getUser(), $idNews);
        if($result) {
            return $this->redirectToRoute('news');
        } else {
            return new Response("<h1> ОШИБКА: вы не можете удалить данную новость так как она не ваша!</h1>");
        }
    }
}