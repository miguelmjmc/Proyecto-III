<?php

namespace AppBundle\Controller;

use AppBundle\Entity\CompanyProfile;
use AppBundle\Form\CompanyProfileType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/settings")
 */
class SettingsController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Route("/company_profile", name="settings_company_profile")
     */
    public function companyProfileAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $companyProfile = $em->getRepository(CompanyProfile::class)->findOneBy(array());

        if ($request->isXmlHttpRequest()) {

            if (null === $companyProfile) {
                $method = 'POST';
            } else {
                $method = 'PUT';
            }

            $parameters = array('method' => $method);

            $form = $this->createForm(CompanyProfileType::class, $companyProfile, $parameters);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                if (null === $companyProfile) {
                    $em->persist($form->getData());
                }

                $em->flush();

                $this->addFlash('success', 'Exito!. Operación realizada satisfactoriamente');

                return new Response('success');
            }

            $parameters = array(
                'form' => $form->createView(),
                'suffix' => 'perfil de la compañia',
                'action' => $this->generateUrl('settings_company_profile'),
                'method' => $method,
            );

            return $this->render('@App/base/modal.html.twig', $parameters);
        }

        $parameters = array(
            'method' => 'GET',
            'attr' => array('readonly' => true),
        );

        $form = $this->createForm(CompanyProfileType::class, $companyProfile, $parameters);

        return $this->render('settings/company_profile.html.twig', array('form' => $form->createView()));
    }
}
