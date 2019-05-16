<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Credit;
use AppBundle\Entity\Payment;
use AppBundle\Repository\CreditRepository;
use AppBundle\Repository\PaymentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/report")
 */
class ReportController extends Controller
{
    /**
     * @Route("/credit", name="credit_report")
     */
    public function creditAction()
    {
        /** @var CreditRepository $repository */
        $repository = $this->getDoctrine()->getRepository(Credit::class);

        $credits = $repository->findAll();

        $parameters = array(
            'totalCredits' => 0,
            'paidCredits' => 0,
            'pendingCredits' => 0,
            'expiredCredits' => 0,
            'dataChart1' => $repository->getDataChart1(),
            'dataChart2' => $repository->getDataChart2(),
        );

        $parameters['totalCredits'] = count($credits);

        /** @var Credit $credit */
        foreach ($credits as $credit) {
            if (1 === $credit->getStatusCode()) {
                $parameters['paidCredits']++;
            }

            if (2 === $credit->getStatusCode()) {
                $parameters['pendingCredits']++;
            }

            if (3 === $credit->getStatusCode()) {
                $parameters['expiredCredits']++;
            }
        }

        return $this->render('report/credit_report.html.twig', $parameters);
    }

    /**
     * @Route("/payment", name="payment_report")
     */
    public function reportAction()
    {
        $em = $this->getDoctrine()->getManager();

        /** @var PaymentRepository $repository */
        $repository = $em->getRepository(Payment::class);

        $parameters = array(
            'dataChart1' => $repository->getDataChart1(),
            'dataChart2' => $repository->getDataChart2(),
        );

        return $this->render('report/payment_report.html.twig', $parameters);
    }
}
