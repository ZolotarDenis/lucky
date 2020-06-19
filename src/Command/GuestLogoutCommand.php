<?php

namespace App\Command;

use App\Entity\Guest;
use App\Entity\Status;
use App\Repository\GuestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class GuestLogoutCommand
 * @package App\Command
 */
class GuestLogoutCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'app:guest:logout';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var AdapterInterface
     */
    private $cache;

    public function __construct(EntityManagerInterface $entityManager, AdapterInterface $cache)
    {
        $this->entityManager = $entityManager;
        $this->cache = $cache;

        parent::__construct();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var GuestRepository $guestRepository */
        $guestRepository = $this->entityManager->getRepository(Guest::class);
        $guests = $guestRepository->getGuestsForLogoutProcess();
        $statusRepository = $this->entityManager->getRepository(Status::class);
        /** @var Status $statusWentOut */
        $statusWentOut = $statusRepository->findOneBy(['name' => Status::WENT_OUT_STATUS]);

        foreach ($guests as $guest) {
            $clientIp = $guest->getClientIp();
            $guestCacheItem = $this->cache->getItem($clientIp);
            if (!$guestCacheItem->isHit()) {
                $guest = new Guest($statusWentOut, $clientIp);
                $this->entityManager->persist($guest);
                $output->write('Logout guest with IP:' . $clientIp);
            }
        }

        $this->entityManager->flush();

        return Command::SUCCESS;
    }
}
