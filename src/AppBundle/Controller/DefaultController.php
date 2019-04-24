<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Client;
use AppBundle\Entity\Credit;
use AppBundle\Entity\Payment;
use AppBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    /**
     * @return Response
     *
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $parameters = array();

        $parameters['clients'] = count($this->getDoctrine()->getRepository(Client::class)->findAll());
        $parameters['credits'] = count($this->getDoctrine()->getRepository(Credit::class)->findAll());
        $parameters['payments'] = count($this->getDoctrine()->getRepository(Payment::class)->findAll());
        $parameters['products'] = count($this->getDoctrine()->getRepository(Product::class)->findAll());


        return $this->render('index.html.twig', $parameters);
    }

    /**
     * @return Response
     *
     * @Route("/construction/{section}", name="under_construction", defaults={"section": null})
     */
    public function constructionAction()
    {
        return $this->render('@App/base/under_construction.html.twig');
    }
}
