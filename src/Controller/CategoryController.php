<?php

namespace App\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormError;

use App\Entity\Category;

use App\Form\CategoryType;

class CategoryController extends AbstractController
{
    /**
     * @Route("/category", name="category")
     */
    public function index()
    {
        return $this->redirectToRoute("all_categories");
    }

    /**
     * @Route("/category/list", name="all_categories")
     * @param Request $request
     * @return Response
     */
    public function viewAll(Request $request)
    {
        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findAll();

        return $this->render('category/view_all.html.twig',
            [
                'categories' => $categories
            ]);
    }

    /**
     * @Route("/category/add", name="add_category_form", methods={"GET"})
     *
     * @return Response
     */
    public function add()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $form = $this->createForm(CategoryType::class);

        return $this->render("category/add.html.twig", 
            [
                'categoryForm' => $form->createView()
            ]);
    }

    /**
     * @Route("/category/add", name="add_category_process", methods={"POST"})
     *
     * @param Request $request
     * @return Response
     */
    public function addProcess(Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $category = new Category();
        $form = $this->createForm(
            CategoryType::class,
            $category
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this
                ->getDoctrine()
                ->getManager();

            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash("info", "Kategoria \"" . $category->getName() . "\" dodana pomyślnie");

            return $this->redirectToRoute("all_categories");
        }

        return $this->render("category/add.html.twig",
            [
                'categoryForm' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/category/edit/{id}", name="edit_category_form", methods={"GET"})
     * @param Category $category
     * @return Response
     */
    public function edit(Category $category)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $form = $this->createForm(
            CategoryType::class,
            $category
        );

        return $this->render("category/edit.html.twig",
            ["categoryForm" => $form->createView()]);
    }

    /**
     * @Route("/category/edit/{id}", name="edit_category_process", methods={"POST"})
     * @param Category $category
     * @param Request $request
     * @return Response
     */
    public function editProcess(Category $category, Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $form = $this->createForm(
            CategoryType::class,
            $category
        );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this
                ->getDoctrine()
                ->getManager();

            $em->persist($category);
            $em->flush();

            $this->addFlash("info", "Kategoria \"" . $category->getName() . "\" edytowana pomyślnie");
            return $this->redirectToRoute("all_categories");
        }
        return $this->render("category/edit.html.twig",
            ["categoryForm" => $form->createView()]);
    }

    /**
     * @Route("/category/delete/{id}", name="delete_category_process", methods={"GET"})
     * @param Category $category
     * @return Response
     */
    public function deleteCategory(Category $category)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $em = $this->getDoctrine()->getManager();

        $em->remove($category);
        $em->flush();

        $this->addFlash("delete", "Pomyślnie usunięto kategorię");
        return $this->redirectToRoute("all_categories");
    }
}
