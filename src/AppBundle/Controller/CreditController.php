<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Credit;
use AppBundle\Form\CreditType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/credit")
 */
class CreditController extends Controller
{
    /**
     * @Route("/", name="credit")
     */
    public function indexAction(Request $request)
    {
        return $this->render('credit/credit.html.twig');
    }

    /**
     * @Route("/list/credit", name="credit_list")
     */
    public function creditListAction()
    {
        $credits = $this->getDoctrine()->getRepository(Credit::class)->findAll();

        $data = array(
            'data' => array(),
            'columns' => array(
                array('title' => 'Fecha'),
                array('title' => 'Código'),
                array('title' => 'Cliente'),
                array('title' => 'Estado'),
                array('title' => 'Monto'),
                array('title' => 'Progreso'),
                array('title' => 'Acciones'),
            )
        );

        /** @var Credit $credit */
        foreach ($credits as $credit) {

            $parameters = array(
                'suffix' => 'credito',
                'actions' => array('show', 'manage'),
                'path' => $this->generateUrl('credit_modal', array('id' => $credit->getId())),
                'managePath' => $this->generateUrl('client_credit_manage', array('id' => $credit->getClient()->getId(), 'credit_id' => $credit->getId())),
            );

            $btn = $this->renderView('@App/base/table_btn.html.twig', $parameters);

            $data['data'][] = array(
                $credit->getDate()->format('Y/m/d'),
                $credit->getCode(),
                $credit->getClient()->getFullName().' (CI: '.$credit->getClient()->getCi().')',
                $credit->getStatus(),
                $credit->getAmountUnit(),
                $credit->getProgress(),
                $btn,
            );
        }

        return new JsonResponse($data);
    }

    /**
     * @param Request $request
     * @param Credit $credit
     * @param int $id
     *
     * @return Response
     *
     * @Route("/modal/credit/{id}}", name="credit_modal", defaults={"id": "null"})
     */
    public function creditModalAction(Request $request, Credit $credit = null, $id = null)
    {
        $parameters = array('method' => $request->getMethod());

        if ('GET' === $request->getMethod() || 'DELETE' === $request->getMethod()) {
            $parameters['attr'] = array('readonly' => true);
        }

        $form = $this->createForm(CreditType::class, $credit, $parameters);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if ('DELETE' === $request->getMethod()) {
                if (0 !== count($credit->getPayment())) {
                    $this->addFlash(
                        'danger',
                        'Oops! No se ha podido eliminar el credito porque existen pagos asociados al registro. Por favor elimine primero los pagos.'
                    );

                    return new Response('success');
                }

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
            'suffix' => 'credito',
            'action' => $this->generateUrl('credit_modal', array('id' => $id)),
            'method' => $request->getMethod(),
        );

        return $this->render('@App/base/modal.html.twig', $parameters);
    }
}
