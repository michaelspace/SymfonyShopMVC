<?php

namespace App\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormError;

use App\Entity\Product;
use App\Entity\Category;

use App\Form\ProductType;

class ProductController extends AbstractController
{
    /**
     * @Route("/product", name="product")
     */
    public function index()
    {
        return $this->redirectToRoute("all_products");
    }

    /**
     * @Route("/product/list", name="all_products")
     * @param Request $request
     * @return Response
     */
    public function viewAll(Request $request)
    {
        $products = $this->getDoctrine()
            ->getRepository(Product::class)
            ->findAll();

        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findAll();

        return $this->render('product/view_all.html.twig',
            [
                'products' => $products,
                'categories' => $categories
            ]);
    }

    /**
     * @Route("/product/add", name="add_product_form", methods={"GET"})
     *
     * @return Response
     */
    public function add()
    {
        $form = $this->createForm(ProductType::class);

        return $this->render("product/add.html.twig", 
            [
                'productForm' => $form->createView()
            ]);
    }

    /**
     * @Route("/product/add", name="add_product_process", methods={"POST"})
     *
     * @param Request $request
     * @return Response
     */
    public function addProcess(Request $request)
    {
        $product = new Product();
        $form = $this->createForm(
            ProductType::class,
            $product
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this
                ->getDoctrine()
                ->getManager();

            $entityManager->persist($product);
            $entityManager->flush();

            $this->addFlash("info", "Produkt \"" . $product->getName() . "\" dodany pomyślnie");

            return $this->redirectToRoute("all_products");
        }

        return $this->render("products/add.html.twig",
            [
                'productForm' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/product/edit/{id}", name="edit_product_form", methods={"GET"})
     * @param Product $product
     * @return Response
     */
    public function edit(Product $product)
    {
        $form = $this->createForm(
            ProductType::class,
            $product
        );

        return $this->render("product/edit.html.twig",
            ["productForm" => $form->createView()]);
    }

    /**
     * @Route("/product/edit/{id}", name="edit_product_process", methods={"POST"})
     * @param Product $product
     * @param Request $request
     * @return Response
     */
    public function editProcess(Product $product, Request $request)
    {
        $form = $this->createForm(
            ProductType::class,
            $product
        );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this
                ->getDoctrine()
                ->getManager();

            $em->persist($product);
            $em->flush();

            $this->addFlash("info", "Produkt \"" . $product->getName() . "\" edytowano pomyślnie");
            return $this->redirectToRoute("all_products");
        }
        return $this->render("product/edit.html.twig",
            ["productForm" => $form->createView()]);
    }

    /**
     * @Route("/product/delete/{id}", name="delete_product_process", methods={"GET"})
     * @param Product $product
     * @return Response
     */
    public function deleteProduct(Product $product)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($product);
        $em->flush();

        $this->addFlash("delete", "Pomyślnie usunięto produkt");
        return $this->redirectToRoute("all_products");
    }
}
