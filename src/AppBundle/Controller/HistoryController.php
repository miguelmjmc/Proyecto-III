<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AccessHistory;
use AppBundle\Entity\OperationHistory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/history")
 */
class HistoryController extends Controller
{
    /**
     * @Route("/access", name="access_history")
     */
    public function accessHistoryAction()
    {
        return $this->render('history/access_history.html.twig');
    }

    /**
     * @Route("/operations", name="operations_history")
     */
    public function operationsHistoryAction()
    {
        return $this->render('history/operations_history.html.twig');
    }

    /**
     * @Route("/list/access", name="access_history_list")
     */
    public function accessHistoryListAction()
    {
        $accessHistories = $this->getDoctrine()->getRepository(AccessHistory::class)->findAll();

        $data = array(
            'data' => array(),
            'order' => array(0, 'DESC'),
            'columns' => array(
                array('title' => 'Fecha'),
                array('title' => 'Usuario'),
                array('title' => 'Ip'),
            ),
        );

        /** @var AccessHistory $accessHistory */
        foreach ($accessHistories as $accessHistory) {
            $data['data'][] = array(
                $accessHistory->getDate()->format('Y/m/d h:i:s'),
                $accessHistory->getUser()->getUsername(),
                $accessHistory->getIp(),
            );
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/list/operations", name="operations_history_list")
     */
    public function operationsHistoryListAction()
    {
        $operationHistories = $this->getDoctrine()->getRepository(OperationHistory::class)->findAll();

        $data = array(
            'data' => array(),
            'order' => array(0, 'DESC'),
            'columns' => array(
                array('title' => 'Fecha'),
                array('title' => 'Usuario'),
                array('title' => 'Tipo de Operación'),
                array('title' => 'Entidad Objetivo'),
            ),
        );

        /** @var OperationHistory $operationHistory */
        foreach ($operationHistories as $operationHistory) {
            $data['data'][] = array(
                $operationHistory->getDate()->format('Y/m/d h:i:s'),
                $operationHistory->getUser()->getUsername(),
                $operationHistory->getDecodedOperationType(),
                $operationHistory->getDecodedTargetEntity(),
            );
        }

        return new JsonResponse($data);
    }
}
