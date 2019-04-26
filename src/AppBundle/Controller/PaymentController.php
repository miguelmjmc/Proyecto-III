<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Payment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/payment")
 */
class PaymentController extends Controller
{
    /**
     * @Route("/", name="payment")
     */
    public function indexAction(Request $request)
    {
        return $this->render('payment/payment.html.twig');
    }

    /**
     * @Route("/list", name="payment_list")
     */
    public function paymentListAction()
    {
        $payments = $this->getDoctrine()->getRepository(Payment::class)->findAll();

        $data = array(
            'data' => array(),
            'order' => array(0, 'DESC'),
            'columns' => array(
                array('title' => 'Fecha'),
                array('title' => 'Código'),
                array('title' => 'Cliente'),
                array('title' => 'Credito'),
                array('title' => 'Monto'),
                array('title' => 'Acciones'),
            )
        );

        /** @var Payment $payment */
        foreach ($payments as $payment) {

            $parameters = array(
                'suffix' => 'pago',
                'actions' => array('show', 'edit', 'delete', 'manage'),
                'path' => $this->generateUrl('client_credit_payment_modal', array('id' => $payment->getCredit()->getClient()->getId(), 'credit_id' => $payment->getCredit()->getId(), 'payment_id' => $payment->getId())),
                'managePath' => $this->generateUrl('client_credit_manage', array('id' => $payment->getCredit()->getClient()->getId(), 'credit_id' => $payment->getCredit()->getId())),
            );

            $btn = $this->renderView('@App/base/table_btn.html.twig', $parameters);

            $data['data'][] = array(
                $payment->getDate()->format('Y/m/d'),
                $payment->getCode(),
                $payment->getCredit()->getClient()->getFullName().' (CI: '.$payment->getCredit()->getClient()->getCi().')',
                $payment->getCredit()->getCode(),
                $payment->getAmountUnit(),
                $btn,
            );
        }

        return new JsonResponse($data);
    }
}
