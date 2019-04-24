<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Entity\ProductBrand;
use AppBundle\Entity\ProductCategory;
use AppBundle\Form\ProductBrandType;
use AppBundle\Form\ProductCategoryType;
use AppBundle\Form\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/product")
 */
class ProductController extends Controller
{
    /**
     * @return Response
     *
     * @Route("/", name="product")
     */
    public function indexAction(Request $request)
    {
        return $this->render('product/product.html.twig');
    }

    /**
     * @return Response
     *
     * @Route("/list/product", name="product_list")
     */
    public function productListAction()
    {
        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();

        $data = array(
            'data' => array(),
            'columns' => array(
                array('title' => 'Nombre'),
                array('title' => 'Marca',),
                array('title' => 'Categoría'),
                array('title' => 'Acciones'),
            )
        );

        /** @var Product $product */
        foreach ($products as $product) {

            $parameters = array(
                'suffix' => 'producto',
                'actions' => array('show', 'edit', 'delete'),
                'path' => $this->generateUrl('product_modal', array('id' => $product->getId())),
            );

            $btn = $this->renderView('@App/base/table_btn.html.twig', $parameters);

            $data['data'][] = array(
                $product->getName(),
                $product->getProductBrand()->getName(),
                $product->getProductCategory()->getName(),
                $btn,
            );
        }

        return new JsonResponse($data);
    }

    /**
     * @return Response
     *
     * @Route("/list/brand", name="brand_list")
     */
    public function brandListAction()
    {
        $brands = $this->getDoctrine()->getRepository(ProductBrand::class)->findAll();

        $data = array(
            'data' => array(),
            'columns' => array(
                array('title' => 'Nombre'),
                array('title' => 'Acciones'),
            )
        );

        /** @var ProductBrand $brand */
        foreach ($brands as $brand) {

            $parameters = array(
                'suffix' => 'marca',
                'grammaticalGender' => 'f',
                'actions' => array('show', 'edit', 'delete'),
                'path' => $this->generateUrl('brand_modal', array('id' => $brand->getId())),
            );

            $btn = $this->renderView('@App/base/table_btn.html.twig', $parameters);

            $data['data'][] = array(
                $brand->getName(),
                $btn,
            );
        }

        return new JsonResponse($data);
    }

    /**
     * @return Response
     *
     * @Route("/list/category", name="category_list")
     */
    public function categoryListAction()
    {
        $categories = $this->getDoctrine()->getRepository(ProductCategory::class)->findAll();

        $data = array(
            'data' => array(),
            'columns' => array(
                array('title' => 'Nombre'),
                array('title' => 'Acciones'),
            )
        );

        /** @var ProductCategory $category */
        foreach ($categories as $category) {

            $parameters = array(
                'suffix' => 'categoria',
                'grammaticalGender' => 'f',
                'actions' => array('show', 'edit', 'delete'),
                'path' => $this->generateUrl('category_modal', array('id' => $category->getId())),
            );

            $btn = $this->renderView('@App/base/table_btn.html.twig', $parameters);

            $data['data'][] = array(
                $category->getName(),
                $btn,
            );
        }

        return new JsonResponse($data);
    }

    /**
     * @param Request $request
     * @param Product $product
     * @param int $id
     *
     * @return Response
     *
     * @Route("/modal/product/{id}}", name="product_modal", defaults={"id": "null"})
     */
    public function productModalAction(Request $request, Product $product = null, $id = null)
    {
        $parameters = array('method' => $request->getMethod());

        if ('GET' === $request->getMethod() || 'DELETE' === $request->getMethod()) {
            $parameters['attr'] = array('readonly' => true);
        }

        $form = $this->createForm(ProductType::class, $product, $parameters);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if ('DELETE' === $request->getMethod()) {
                if (0 !== count($product->getCreditProduct())) {
                    $this->addFlash(
                        'danger',
                        'Oops! No se ha podido eliminar el producto porque existen creditos asociados al registro. Por favor elimine primero los creditos.'
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
            'action' => $this->generateUrl('product_modal', array('id' => $id)),
            'method' => $request->getMethod(),
        );

        return $this->render('@App/base/modal.html.twig', $parameters);
    }

    /**
     * @param Request $request
     * @param ProductBrand $brand
     * @param int $id
     *
     * @return Response
     *
     * @Route("/modal/brand/{id}}", name="brand_modal", defaults={"id": "null"})
     */
    public function brandModalAction(Request $request, ProductBrand $brand = null, $id = null)
    {
        $parameters = array('method' => $request->getMethod());

        if ('GET' === $request->getMethod() || 'DELETE' === $request->getMethod()) {
            $parameters['attr'] = array('readonly' => true);
        }

        $form = $this->createForm(ProductBrandType::class, $brand, $parameters);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if ('DELETE' === $request->getMethod()) {
                if (0 !== count($brand->getProduct())) {
                    $this->addFlash(
                        'danger',
                        'Oops! No se ha podido eliminar la marca porque existen productos asociados al registro. Por favor elimine primero los productos.'
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
            'suffix' => 'marca',
            'grammaticalGender' => 'f',
            'action' => $this->generateUrl('brand_modal', array('id' => $id)),
            'method' => $request->getMethod(),
        );

        return $this->render('@App/base/modal.html.twig', $parameters);
    }

    /**
     * @param Request $request
     * @param ProductCategory $category
     * @param int $id
     *
     * @return Response
     *
     * @Route("/modal/category/{id}}", name="category_modal", defaults={"id": "null"})
     */
    public function categoryModalAction(Request $request, ProductCategory $category = null, $id = null)
    {
        $parameters = array('method' => $request->getMethod());

        if ('GET' === $request->getMethod() || 'DELETE' === $request->getMethod()) {
            $parameters['attr'] = array('readonly' => true);
        }

        $form = $this->createForm(ProductCategoryType::class, $category, $parameters);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if ('DELETE' === $request->getMethod()) {
                if (0 !== count($category->getProduct())) {
                    $this->addFlash(
                        'danger',
                        'Oops! No se ha podido eliminar la categoria porque existen productos asociados al registro. Por favor elimine primero los productos.'
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
            'suffix' => 'categoria',
            'grammaticalGender' => 'f',
            'action' => $this->generateUrl('category_modal', array('id' => $id)),
            'method' => $request->getMethod(),
        );

        return $this->render('@App/base/modal.html.twig', $parameters);
    }
}
