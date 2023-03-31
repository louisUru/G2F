<?php

namespace App\Repository;

use App\Entity\Inscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Inscription>
 *
 * @method Inscription|null find($id, $lockMode = null, $lockVersion = null)
 * @method Inscription|null findOneBy(array $criteria, array $orderBy = null)
 * @method Inscription[]    findAll()
 * @method Inscription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Inscription::class);
    }

    public function save(Inscription $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Inscription $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function findByVoirInscEmploye($value): array
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.lEmploye = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    public function getInscEnAtt($idForm): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.laFormation = :form and e.statut = :statut ')
            ->setParameter('form', $idForm)
            ->setParameter('statut', "Attente")
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
    ;
    }

    public function getInscStatut($statut): array //fonction permettant de choisir le statut (question2)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.statut = :statut ')
            ->setParameter('statut', $statut)
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
    ;
    }


    public function trouverParIdFormation($id): array
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.laFormation = :id')
            ->setParameter('id', $id)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
        ;
    }
    public function trouverParIdFormationEmpl($id,$idEmp): array
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.laFormation = :id and i.lEmploye= :emp')
            ->setParameter('id', $id)
            ->setParameter('emp',$idEmp)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
        ;
    }

//    /**
//     * @return Inscription[] Returns an array of Inscription objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Inscription
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
