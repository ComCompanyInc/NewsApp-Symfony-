<?php

namespace App\Repository;

use App\Entity\News;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<News>
 */
class NewsRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $entityManager;
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, News::class);
        $this->entityManager=$entityManager;
    }

    //    /**
    //     * @return News[] Returns an array of News objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('n')
    //            ->andWhere('n.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('n.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?News
    //    {
    //        return $this->createQueryBuilder('n')
    //            ->andWhere('n.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function sendNews(News $textNews, string $creationDate, User $authorId): void
    {
        $news = new News();
        $news->setText($textNews->getText());
        $news->setDateCreating(new \DateTime($creationDate));
        $news->setAuthor($this->entityManager->getRepository(User::class)->findOneBy(['id' => $authorId]));
        $this->entityManager->persist($news);
        $this->entityManager->flush();
    }

    public function deleteNews(User $idUser, int $idNews): int
    {

        /*
        $comments = $this->entityManager->getRepository(Comments::class)->findBy([
            'newsId' => $idNews]);*/

        $news = $this->entityManager->getRepository(News::class)->findOneBy([
            'id' => $idNews,
            'author' => $idUser]);

        if($news) {

            /*foreach ($comments as $comment) { //Удаление всех комментариев к данной новости
                $this->entityManager->remove($comment);
            }*/

            $this->entityManager->remove($news); //удаление новости
            $this->entityManager->flush(); //сохранение состояния БД

            return 1;
        } else {
            return -100;
        }
    }
}
