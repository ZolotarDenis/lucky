<?php


namespace App\Repository;

use App\Entity\Guest;
use App\Entity\Status;
use Doctrine\ORM\EntityRepository;

class GuestRepository extends EntityRepository
{
    /**
     * @return Guest[]
     */
    public function getGuestsForLogoutProcess(): array
    {
        $qb = $this->createQueryBuilder('guest');
        $qb
            ->innerJoin('guest.status', 'status')
            ->where('guest.created > :date')
            ->setParameter('date', new \DateTime('-11 minute'))
            ->andWhere('status.name = :statusName')
            ->setParameter('statusName', Status::WENT_IN_STATUS);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param \DateTime $dateWentInFrom
     * @param \DateTime $dateWentInTo
     * @return int
     */
    public function getGuestsCountyDateRange(\DateTime $dateWentInFrom, \DateTime $dateWentInTo): int
    {
        $qb = $this->createQueryBuilder('guest');
        $qb
            ->select('count(DISTINCT guest.clientIp)')
            ->innerJoin('guest.status', 'status')
            ->where('guest.created BETWEEN :dateWentInFrom AND :dateWentInTo')
            ->setParameter('dateWentInFrom', $dateWentInFrom)
            ->setParameter('dateWentInTo', $dateWentInTo)
            ->andWhere('status.name = :statusName')
            ->setParameter('statusName', Status::WENT_IN_STATUS);

        return $qb->getQuery()->getSingleScalarResult();
    }
}
