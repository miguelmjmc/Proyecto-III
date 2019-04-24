<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends Controller
{
    /**
     * @Route("/admin/user/", name="user")
     */
    public function userAction()
    {
        return $this->render('user/user.html.twig');
    }

    /**
     * @Route("/list/user", name="user_list")
     */
    public function userListAction()
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        $data = array(
            'data' => array(),
            'columns' => array(
                array('title' => 'Usuario'),
                array('title' => 'Email'),
                array('title' => 'Rol'),
                array('title' => 'Estado'),
                array('title' => 'Acciones'),
            )
        );

        /** @var User $user */
        foreach ($users as $user) {

            $parameters = array(
                'suffix' => 'usuario',
                'actions' => array('show', 'edit'),
                'path' => $this->generateUrl('user_modal', array('id' => $user->getId())),
            );

            $btn = $this->renderView('@App/base/table_btn.html.twig', $parameters);

            $data['data'][] = array(
                $user->getUsername(),
                $user->getEmail(),
                $user->hasRole('ROLE_ADMIN') ? 'Administrador' : 'Operador',
                $user->isEnabled() ? '<span class="label label-success" title="Habilitado">Habilitado</span>' : '<span class="label label-warning" title="Desabilitado">Desabilitado</span>',
                $btn,
            );
        }

        return new JsonResponse($data);
    }

    /**
     * @param Request $request
     * @param User $user
     * @param int $id
     *
     * @return Response
     *
     * @Route("/admin/user/modal/user/{id}}", name="user_modal", defaults={"id": "null"})
     */
    public function userModalAction(Request $request, User $user = null, $id = null)
    {
        $parameters = array('method' => $request->getMethod());

        if ('GET' === $request->getMethod() || 'DELETE' === $request->getMethod()) {
            $parameters['attr'] = array('readonly' => true);
        }

        $form = $this->createForm(UserType::class, $user, $parameters);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if ('POST' === $request->getMethod()) {
                $em->persist($form->getData());
            }

            $em->flush();

            $this->addFlash('success', 'Exito! Operación realizada satisfactoriamente');

            return new Response('success');
        }

        $parameters = array(
            'form' => $form->createView(),
            'suffix' => 'usuario',
            'action' => $this->generateUrl('user_modal', array('id' => $id)),
            'method' => $request->getMethod(),
        );

        return $this->render('@App/base/modal.html.twig', $parameters);
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Route("/user/profile", name="user_profile")
     */
    public function profileAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();

        if ($request->isXmlHttpRequest()) {

            $parameters = array('method' => $request->getMethod());

            $form = $this->createForm(UserType::class, $user, $parameters );

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $em->flush();

                $this->addFlash('success', 'Exito!. Operación realizada satisfactoriamente');

                return new Response('success');
            }

            $parameters = array(
                'form' => $form->createView(),
                'suffix' => 'perfil',
                'action' => $this->generateUrl('user_profile'),
                'method' => 'PUT',
            );

            return $this->render('@App/base/modal.html.twig', $parameters);
        }

        $parameters = array(
            'method' => 'GET',
            'attr' => array('readonly' => true),
        );

        $form = $this->createForm(UserType::class, $user, $parameters);

        return $this->render('user/profile.html.twig', array('form' => $form->createView()));
    }
}
