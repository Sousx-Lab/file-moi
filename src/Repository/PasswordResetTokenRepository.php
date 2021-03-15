<?php

namespace App\Repository;

use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Auth\Password\PasswordResetToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method PasswordResetToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method PasswordResetToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method PasswordResetToken[]    findAll()
 * @method PasswordResetToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PasswordResetTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PasswordResetToken::class);
    }

    // /**
    //  * @return PasswordRecoveryToken[] Returns an array of PasswordRecoveryToken objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    
    public function findOneByToken(string $token): ?PasswordResetToken
    {

        return $this->findOneBy(['token' => $token]);
    }
    
}
