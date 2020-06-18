<?php

namespace App\Service\Guest;

use App\Entity\Guest;
use App\Entity\Status;
use App\Repository\GuestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class GuestService
{
    /**
     * @var AdapterInterface
     */
    protected $cache;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @param AdapterInterface $cache
     * @param LoggerInterface $logger
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        AdapterInterface $cache,
        LoggerInterface $logger,
        EntityManagerInterface $entityManager
    ) {
        $this->cache = $cache;
        $this->logger = $logger;
        $this->entityManager = $entityManager;
    }

    /**
     * @param string|null $clientIp
     */
    public function checkVisit(?string $clientIp): void
    {
        if (!$clientIp) {
            $this->logger->warning('Client IP is not defined');
            return;
        }

        $guestCacheItem = $this->cache->getItem($clientIp);
        $guestCacheItem->expiresAfter(new \DateInterval('PT10M'));
        $this->cache->save($guestCacheItem);

        /** @var Status $statusWaitIn */
        $statusWaitIn = $this->entityManager->getRepository(Status::class)
            ->findOneBy(['name' => Status::WENT_IN_STATUS]);

        $guestEntity = new Guest($statusWaitIn, $clientIp);
        $this->entityManager->persist($guestEntity);
        $this->entityManager->flush();
    }

    /**
     * @param \DateTime $dateFrom
     * @param \DateTime $dateTo
     * @return int
     */
    public function getCountActiveGuests(\DateTime $dateFrom, \DateTime $dateTo): int
    {
        /** @var GuestRepository $guestRepository */
        $guestRepository = $this->entityManager->getRepository(Guest::class);
        return $guestRepository->getGuestsCountyDateRange($dateFrom, $dateTo);
    }
}
