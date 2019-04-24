<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Payment;
use AppBundle\Form\PaymentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     * @Route("/list/payment", name="payment_list")
     */
    public function paymentListAction()
    {
        $payments = $this->getDoctrine()->getRepository(Payment::class)->findAll();

        $data = array(
            'data' => array(),
            'columns' => array(
                array('title' => 'Fecha'),
                array('title' => 'Cliente'),
                array('title' => 'Monto'),
                array('title' => 'Acciones'),
            )
        );

        /** @var Payment $payment */
        foreach ($payments as $payment) {

            $parameters = array(
                'suffix' => 'pago',
                'actions' => array('show'),
                'path' => $this->generateUrl('payment_modal', array('id' => $payment->getId())),
            );

            $btn = $this->renderView('@App/base/table_btn.html.twig', $parameters);

            $data['data'][] = array(
                $payment->getDate()->format('Y/m/d'),
                $payment->getCredit()->getClient()->getFullName().' (CI: '.$payment->getCredit()->getClient()->getCi().')',
                'Bs. '.number_format($payment->getAmount(), 2),
                $btn,
            );
        }

        return new JsonResponse($data);
    }

    /**
     * @param Request $request
     * @param Payment $payment
     * @param int $id
     *
     * @return Response
     *
     * @Route("/modal/payment/{id}}", name="payment_modal", defaults={"id": "null"})
     */
    public function paymentModalAction(Request $request, Payment $payment = null, $id = null)
    {
        $parameters = array('method' => $request->getMethod());

        if ('GET' === $request->getMethod() || 'DELETE' === $request->getMethod()) {
            $parameters['attr'] = array('readonly' => true);
        }

        $form = $this->createForm(PaymentType::class, $payment, $parameters);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if ('DELETE' === $request->getMethod()) {
                $em->remove($form->getData());
            }

            if ('POST' === $request->getMethod()) {
                $em->persist($form->getData());
            }

            $em->flush();

            $this->addFlash('success', 'Exito! Operación realizada satisfactoriamente');

            return new Response('success');
        }

        $parameters = array(
            'form' => $form->createView(),
            'suffix' => 'pago',
            'action' => $this->generateUrl('payment_modal', array('id' => $id)),
            'method' => $request->getMethod(),
        );

        return $this->render('@App/base/modal.html.twig', $parameters);
    }
}
