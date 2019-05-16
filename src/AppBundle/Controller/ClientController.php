<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Client;
use AppBundle\Entity\Credit;
use AppBundle\Entity\CreditProduct;
use AppBundle\Entity\Payment;
use AppBundle\Form\ClientType;
use AppBundle\Form\CreditProductType;
use AppBundle\Form\CreditType;
use AppBundle\Form\PaymentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
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
     * @Route("/list", name="client_list")
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
            ),
        );

        /** @var Client $client */
        foreach ($clients as $client) {

            $parameters = array(
                'suffix' => 'cliente',
                'actions' => array('show', 'edit', 'delete', 'manage'),
                'path' => $this->generateUrl('client_modal', array('id' => $client->getId())),
                'managePath' => $this->generateUrl('client_manage', array('id' => $client->getId())),
            );

            $btn = $this->renderView('@App/base/table_btn.html.twig', $parameters);

            $data['data'][] = array(
                $client->getCreatedAt()->format('Y/m/d'),
                $client->getCi(),
                $client->getFullName(),
                $client->getStatus(),
                $btn,
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
     * @Route("/modal/{id}", name="client_modal", defaults={"id": "null"})
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
                        'Oops! No se ha podido eliminar el cliente porque existen créditos asociados al registro. Por favor elimine primero los créditos.'
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

    /**
     * @return Response
     *
     * @Route("/list/modal", name="client_list_modal")
     */
    public function clientListModalAction()
    {
        return $this->render('client/client_list_modal.html.twig');
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Route("/list/data/modal", name="client_list_data_modal")
     */
    public function clientListDataModalAction(Request $request)
    {
        $clients = $this->getDoctrine()->getRepository(Client::class)->findAll();

        $data = array('data' => array());

        /** @var Client $client */
        foreach ($clients as $client) {

            $parameters = array(
                'suffix' => 'cliente',
                'actions' => array('manage'),
                'managePath' => $this->generateUrl('client_manage', array('id' => $client->getId())),
            );

            $btn = $this->renderView('@App/base/table_btn.html.twig', $parameters);

            $data['data'][] = array(
                $client->getCi(),
                $client->getFullName(),
                $client->getStatus(),
                $btn,
            );
        }

        return new JsonResponse($data);
    }

    /**
     * @param Client $client
     *
     * @return Response
     *
     * @Route("/{id}/manage", name="client_manage")
     */
    public function clientManageAction(Client $client)
    {
        return $this->render('client/client_manage.html.twig', array('client' => $client));
    }

    /**
     * @param Client $client
     *
     * @return Response
     *
     * @ParamConverter("client", options={"id" = "id"})
     *
     * @Route("/{id}/manage/list", name="client_manage_list")
     */
    public function clientManageListAction(Client $client)
    {
        $credits = $client->getCredit();

        $data = array(
            'data' => array(),
            'order' => array(0, 'DESC'),
            'columns' => array(
                array('title' => 'Fecha'),
                array('title' => 'Código'),
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
                'actions' => array('show', 'edit', 'delete', 'manage'),
                'path' => $this->generateUrl('client_manage_modal', array('id' => $credit->getClient()->getId(), 'credit_id' => $credit->getId())),
                'managePath' => $this->generateUrl('client_credit_manage', array('id' => $client->getId(), 'credit_id' => $credit->getId())),
            );

            $btn = $this->renderView('@App/base/table_btn.html.twig', $parameters);

            $data['data'][] = array(
                $credit->getDate()->format('Y/m/d'),
                $credit->getCode(),
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
     * @param Client $client
     * @param Credit $credit
     * @param int $credit_id
     *
     * @return Response
     *
     * @ParamConverter("client", options={"id" = "id"})
     * @ParamConverter("credit", options={"id" = "credit_id"})
     *
     * @Route("/{id}/manage/modal/{credit_id}", name="client_manage_modal", defaults={"credit_id": "null"})
     */
    public function clientManageModalAction(Request $request, Client $client, Credit $credit = null, $credit_id = null)
    {
        $parameters = array('method' => $request->getMethod());

        if ('GET' === $request->getMethod() || 'DELETE' === $request->getMethod()) {
            $parameters['attr'] = array('readonly' => true);
        }

        if (!$credit) {
            $credit = (new Credit())->setClient($client);
        }

        $form = $this->createForm(CreditType::class, $credit, $parameters);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if ('DELETE' === $request->getMethod()) {
                if (0 !== count($credit->getPayment())) {
                    $this->addFlash(
                        'danger',
                        'Oops! No se ha podido eliminar el crédito porque existen pagos asociados al registro. Por favor elimine primero los pagos.'
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

            if ('POST' === $request->getMethod()) {
                return new Response($this->generateUrl('client_credit_manage', array('id' => $client->getId(), 'credit_id' => $credit->getId())));
            }

            return new Response('success');
        }

        $parameters = array(
            'form' => $form->createView(),
            'suffix' => 'crédito',
            'action' => $this->generateUrl('client_manage_modal', array('id' => $client->getId(), 'credit_id' => $credit_id)),
            'method' => $request->getMethod(),
            'flash' => $client->getFlash(),
        );

        return $this->render('@App/base/modal.html.twig', $parameters);
    }

    /**
     * @param Credit $credit
     *
     * @return Response
     *
     * @ParamConverter("credit", options={"id" = "credit_id"})
     *
     * @Route("/{id}/manage/credit/{credit_id}", name="client_credit_manage")
     */
    public function clientCreditManageAction(Credit $credit)
    {
        return $this->render('client/client_credit_manage.html.twig', array('credit' => $credit));
    }

    /**
     * @param Credit $credit
     *
     * @return Response
     *
     * @ParamConverter("credit", options={"id" = "credit_id"})
     *
     * @ParamConverter("client", options={"id" = "id"})
     *
     * @Route("/{id}/manage/credit/{credit_id}/product/list", name="client_credit_product_list")
     */
    public function clientCreditProductListAction(Credit $credit)
    {
        $creditProducts = $credit->getCreditProduct();

        $data = array(
            'data' => array(),
            'columns' => array(
                array('title' => 'Código'),
                array('title' => 'Nombre'),
                array('title' => 'Marca',),
                array('title' => 'Cantidad'),
                array('title' => 'Precio'),
                array('title' => 'Total'),
                array('title' => 'Acciones'),
            )
        );

        /** @var CreditProduct $creditProduct */
        foreach ($creditProducts as $creditProduct) {

            $parameters = array(
                'suffix' => 'producto',
                'actions' => array('show', 'edit', 'delete'),
                'path' => $this->generateUrl('client_credit_product_modal', array('id' => $creditProduct->getCredit()->getClient()->getId(), 'credit_id' => $creditProduct->getCredit()->getId(), 'creditProduct_id' => $creditProduct->getId())),
            );

            $btn = $this->renderView('@App/base/table_btn.html.twig', $parameters);

            $data['data'][] = array(
                $creditProduct->getProduct()->getCode(),
                $creditProduct->getProduct()->getName(),
                $creditProduct->getProduct()->getProductBrand()->getName(),
                $creditProduct->getQuantityUnit(),
                $creditProduct->getAmountUnit(),
                $creditProduct->getTotalAmountUnit(),
                $btn,
            );
        }

        return new JsonResponse($data);
    }

    /**
     * @param Request $request
     * @param Credit $credit
     * @param CreditProduct $creditProduct
     * @param int $creditProduct_id
     *
     * @return Response
     *
     * @ParamConverter("credit", options={"id" = "credit_id"})
     * @ParamConverter("creditProduct", options={"id" = "creditProduct_id"})
     *
     * @Route("/{id}/manage/credit/{credit_id}/product/modal/{creditProduct_id}", name="client_credit_product_modal", defaults={"creditProduct_id": "null"})
     */
    public function clientCreditProductModalAction(Request $request, Credit $credit, CreditProduct $creditProduct = null, $creditProduct_id = null)
    {
        $parameters = array('method' => $request->getMethod());

        if ('GET' === $request->getMethod() || 'DELETE' === $request->getMethod()) {
            $parameters['attr'] = array('readonly' => true);
        }

        if (!$creditProduct) {
            $creditProduct = (new CreditProduct())->setCredit($credit);
        }

        $form = $this->createForm(CreditProductType::class, $creditProduct, $parameters);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if ('DELETE' === $request->getMethod()) {
                if ($credit->getTotalPaid() > ($credit->getAmount() - $creditProduct->getTotalAmount())) {
                    $this->addFlash(
                        'danger',
                        'Oops! No se ha podido eliminar el producto. La sumatoriria del costo total de los productos no puede ser menor al monto total pagado.'
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
            'suffix' => 'producto',
            'action' => $this->generateUrl('client_credit_product_modal', array('id' => $credit->getClient()->getId(), 'credit_id' => $credit->getId(), 'creditProduct_id' => $creditProduct_id)),
            'method' => $request->getMethod(),
        );

        return $this->render('@App/base/modal.html.twig', $parameters);
    }

    /**
     * @param Credit $credit
     *
     * @return Response
     *
     * @ParamConverter("credit", options={"id" = "credit_id"})
     *
     * @ParamConverter("client", options={"id" = "id"})
     *
     * @Route("/{id}/manage/credit/{credit_id}/payment/list", name="client_credit_payment_list")
     */
    public function clientCreditPaymentListAction(Credit $credit)
    {
        $payments = $credit->getPayment();

        $data = array(
            'data' => array(),
            'order' => array(0, 'DESC'),
            'columns' => array(
                array('title' => 'Fecha'),
                array('title' => 'Código'),
                array('title' => 'Monto'),
                array('title' => 'Acciones'),
            )
        );

        /** @var Payment $payment */
        foreach ($payments as $payment) {

            $parameters = array(
                'suffix' => 'pago',
                'actions' => array('show', 'edit', 'delete'),
                'path' => $this->generateUrl('client_credit_payment_modal', array('id' => $payment->getCredit()->getClient()->getId(), 'credit_id' => $payment->getCredit()->getId(), 'payment_id' => $payment->getId())),
            );

            $btn = $this->renderView('@App/base/table_btn.html.twig', $parameters);

            $data['data'][] = array(
                $payment->getDate()->format('Y/m/d'),
                $payment->getCode(),
                $payment->getAmountUnit(),
                $btn,
            );
        }

        return new JsonResponse($data);
    }

    /**
     * @param Request $request
     * @param Credit $credit
     * @param Payment $payment
     * @param int $payment_id
     *
     * @return Response
     *
     * @ParamConverter("credit", options={"id" = "credit_id"})
     * @ParamConverter("payment", options={"id" = "payment_id"})
     *
     * @Route("/{id}/manage/credit/{credit_id}/payment/modal/{payment_id}", name="client_credit_payment_modal", defaults={"payment_id": "null"})
     */
    public function clientCreditPaymentModalAction(Request $request, Credit $credit, Payment $payment = null, $payment_id = null)
    {
        $parameters = array('method' => $request->getMethod());

        if ('GET' === $request->getMethod() || 'DELETE' === $request->getMethod()) {
            $parameters['attr'] = array('readonly' => true);
        }

        if (!$payment) {
            $payment = (new Payment())->setCredit($credit);
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
            'action' => $this->generateUrl('client_credit_payment_modal', array('id' => $payment->getCredit()->getClient()->getId(), 'credit_id' => $payment->getCredit()->getId(), 'payment_id' => $payment_id)),
            'method' => $request->getMethod(),
        );

        return $this->render('@App/base/modal.html.twig', $parameters);
    }
}
