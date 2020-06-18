<?php

namespace App\Controller;

use App\Service\Guest\GuestService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     * @param Request $request
     * @param GuestService $guestService
     * @return Response
     */
    public function index(Request $request, GuestService $guestService): Response
    {
        $clientIp = $request->getClientIp();
        $guestService->checkVisit($clientIp);

        return $this->render('index/index.html.twig', [
            'clientIp' => $clientIp,
        ]);
    }

    /**
     * @Route("/statistics", name="statistics")
     * @param Request $request
     * @param GuestService $guestService
     * @return Response
     */
    public function statistics(Request $request, GuestService $guestService): Response
    {
        $dateWentIn = $request->get('date_went_in');
        $dateWentOut = $request->get('date_went_out');

        $response = null;
        $wentInDateTime = null;
        $wentOutDateTime = null;
        $isValidDates = (bool)strtotime($dateWentIn) && (bool)strtotime($dateWentOut);
        if ($isValidDates) {
            $wentInDateTime = new \DateTime($dateWentIn);
            $wentOutDateTime = new \DateTime($dateWentOut);
        } else {
            return $this->render('index/statistics.html.twig', [
                'errorMessage' => 'Dates is not valid!'
            ]);
        }

        $interval = $wentInDateTime->diff($wentOutDateTime);
        if ((int)$interval->format('%a') > 0) {
            return $this->render('index/statistics.html.twig', [
                'errorMessage' => 'Dates interval more when 1 day'
            ]);
        }

        return $this->render('index/statistics.html.twig', [
            'response' => [
                'count' => $guestService->getCountActiveGuests($wentInDateTime, $wentOutDateTime),
            ],
        ]);
    }


}
