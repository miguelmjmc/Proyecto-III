<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Client;
use AppBundle\Form\ClientType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/client")
 */
class ClientController extends Controller
{
    /**
     * @Route("/", name="client")
     */
    public function indexAction(Request $request)
    {
        return $this->render('client/client.html.twig');
    }

    /**
     * @Route("/list/client", name="client_list")
     */
    public function clientListAction()
    {
        $clients = $this->getDoctrine()->getRepository(Client::class)->findAll();

        $data = array(
            'data' => array(),
            'columns' => array(
                array('title' => 'Registro'),
                array('title' => 'Cedula'),
                array('title' => 'Nombre'),
                array('title' => 'Estado'),
                array('title' => 'Acciones'),
            )
        );

        /** @var Client $client */
        foreach ($clients as $client) {

            $parameters = array(
                'suffix' => 'cliente',
                'actions' => array('show', 'edit', 'delete'),
                'path' => $this->generateUrl('client_modal', array('id' => $client->getId())),
            );

            $btn = $this->renderView('@App/base/table_btn.html.twig', $parameters);

            $data['data'][] = array(
                $client->getCreatedAt()->format('Y/m/d'),
                $client->getCi(),
                $client->getFullName(),
                $client->getStatus(),
                $btn
            );
        }

        return new JsonResponse($data);
    }

    /**
     * @param Request $request
     * @param Client $client
     * @param int $id
     *
     * @return Response
     *
     * @Route("/modal/client/{id}}", name="client_modal", defaults={"id": "null"})
     */
    public function clientModalAction(Request $request, Client $client = null, $id = null)
    {
        $parameters = array('method' => $request->getMethod());

        if ('GET' === $request->getMethod() || 'DELETE' === $request->getMethod()) {
            $parameters['attr'] = array('readonly' => true);
        }

        $form = $this->createForm(ClientType::class, $client, $parameters);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if ('DELETE' === $request->getMethod()) {
                if (0 !== count($client->getCredit())) {
                    $this->addFlash(
                        'danger',
                        'Oops! No se ha podido eliminar el cliente porque existen creditos asociados al registro. Por favor elimine primero los creditos.'
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
            'suffix' => 'cliente',
            'action' => $this->generateUrl('client_modal', array('id' => $id)),
            'method' => $request->getMethod(),
        );

        return $this->render('@App/base/modal.html.twig', $parameters);
    }
}
