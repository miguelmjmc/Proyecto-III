<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Credit;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
            'order' => array(0, 'DESC'),
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
                'suffix' => 'crédito',
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
}
