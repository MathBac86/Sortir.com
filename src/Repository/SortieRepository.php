<?php

namespace App\Repository;

use App\Entity\Campus;
use App\Entity\Inscription;
use App\Entity\Participant;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Mapping\OrderBy;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    /**
     * @return Sortie[]
     */
    public function findAllFilter(
        Participant $loguser,
        Campus $campus,
        string $nom = null,
        bool $inscrit,
        bool $noInscrit,
        bool $beorganisator = false ,
        string $du = null,
        string $au = null,
        bool $fini = false)
    {

        $qb = $this ->createQueryBuilder('s')
            ->addSelect('i')
            ->leftJoin('s.inscriptions', 'i');


        if ($campus != null && !$beorganisator){
            $qb ->andWhere('s.Campus = :campus')
                ->setParameter('campus', $campus);
        }

        if ($nom != null) {
            $qb ->andWhere('s.nom LIKE :nom')
                ->setParameter('nom', '%'.$nom.'%');
        }

        if ($du != null){
            $starttime = strtotime($du);
            $startnewformat = date('Y-m-d',$starttime);
            $qb ->andWhere('s.dateDebut >= :datedebut')
                ->setParameter('datedebut', $startnewformat);
        }

        if ($au != null){
            $stoptime = strtotime($au);
            $stopnewformat = date('Y-m-d',$stoptime);
            $qb ->andWhere('s.dateDebut <= :datecloture')
                ->setParameter('datecloture', $stopnewformat);
        }

        if ($fini){
            $qb ->andWhere('s.dateDebut <= :passed')
                ->setParameter('passed', date('Y-m-d H:i:s') );
        }

        if ($beorganisator){
            $qb ->andWhere('s.organisateur = :organisateur')
                ->setParameter('organisateur', $loguser->getId());
        }

        if ($inscrit ) {
            $qb ->orWhere('i.Participant = :participant')
                ->setParameter('participant', $loguser->getId());
        }


        if ($noInscrit) {

            $qb ->andWhere($qb->expr()->notIn('s.id', $qb2 = $this->createQueryBuilder('s1')
                                                            ->select('DISTINCT (s1.id)')
                                                            ->innerJoin('s1.inscriptions', 'p1')
                                                            ->andWhere('p1.Participant = :participant')
                                                            ->getDQL()
                                            ))
                ->setParameter('participant', $loguser->getId());
        }

        $qb = $qb->orderBy('s.dateDebut', 'ASC');
        $qb = $qb->getQuery();

        return $qb->execute();
    }


    /**
     * @return Sortie[] Returns an array of Sortie objects
     * @param $participant
     */
    public function findRegistered($participant)
    {
        $query = $this
            ->createQueryBuilder('s')
            ->select('s', 'i', 'e')
            ->join('s.inscriptions', 'i' )
            ->andWhere('i.Participant = (:user)')
            ->setParameter('user', $participant)
            ->join('s.Etat', 'e')
            ->andWhere('e.libelleEtat = (:Avenir) OR e.libelleEtat = (:encours)')
            ->setParameter('Avenir', 'A venir')
            ->setParameter('encours', 'En-cours')
            ->getQuery()
            ->getResult()
        ;

        return $query;
    }

}
