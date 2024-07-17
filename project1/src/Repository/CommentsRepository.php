<?php

namespace App\Repository;

use App\Entity\Comments;
use App\Entity\News;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comments>
 */
class CommentsRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Comments::class);
        $this->entityManager = $entityManager;
    }

    //    /**
    //     * @return Comments[] Returns an array of Comments objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Comments
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function sendComment(Comments $commentText, string $dateSending, User $idAuthor, News $idNews): void
    {
        $comments = new Comments();
        $comments->setText($commentText->getText());
        $comments->setDateCreating(new \DateTime($dateSending));
        $comments->setAuthorId($this->entityManager->getRepository(User::class)->findOneBy(['id' => $idAuthor]));
        $comments->setNewsId($this->entityManager->getRepository(News::class)->findOneBy(['id' => $idNews]));
        $this->entityManager->persist($comments);
        $this->entityManager->flush();
    }
}
