<?php

namespace App\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormError;

use App\Entity\KeyWord;

use App\Form\KeyWordType;

class KeyWordController extends AbstractController
{
    /**
     * @Route("/keyword", name="keyword")
     */
    public function index()
    {
        return $this->redirectToRoute("all_keywords");
    }

    /**
     * @Route("/keyword/list", name="all_keywords")
     * @param Request $request
     * @return Response
     */
    public function viewAll(Request $request)
    {
        $keywords = $this->getDoctrine()
            ->getRepository(KeyWord::class)
            ->findAll();

        return $this->render('keyword/view_all.html.twig',
            [
                'keywords' => $keywords
            ]);
    }

    /**
     * @Route("/keyword/add", name="add_keyword_form", methods={"GET"})
     *
     * @return Response
     */
    public function add()
    {
        $form = $this->createForm(KeyWordType::class);

        return $this->render("keyword/add.html.twig", 
            [
                'keywordForm' => $form->createView()
            ]);
    }

    /**
     * @Route("/keyword/add", name="add_keyword_process", methods={"POST"})
     *
     * @param Request $request
     * @return Response
     */
    public function addProcess(Request $request)
    {
        $keyword = new KeyWord();
        $form = $this->createForm(
            KeyWordType::class,
            $keyword
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this
                ->getDoctrine()
                ->getManager();

            $entityManager->persist($keyword);
            $entityManager->flush();

            $this->addFlash("info", "Słowo \"" . $keyword->getWord() . "\" dodane pomyślnie");

            return $this->redirectToRoute("all_keywords");
        }

        return $this->render("keyword/add.html.twig",
            [
                'keywordForm' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/keyword/edit/{id}", name="edit_keyword_form", methods={"GET"})
     * @param KeyWord $keyword
     * @return Response
     */
    public function edit(KeyWord $keyword)
    {
        $form = $this->createForm(
            KeyWordType::class,
            $keyword
        );

        return $this->render("keyword/edit.html.twig",
            ["keywordForm" => $form->createView()]);
    }

    /**
     * @Route("/keyword/edit/{id}", name="edit_keyword_process", methods={"POST"})
     * @param KeyWord $keyword
     * @param Request $request
     * @return Response
     */
    public function editProcess(KeyWord $keyword, Request $request)
    {
        $form = $this->createForm(
            KeyWordType::class,
            $keyword
        );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this
                ->getDoctrine()
                ->getManager();

            $em->persist($keyword);
            $em->flush();

            $this->addFlash("info", "Słowo \"" . $keyword->getWord() . "\" edytowane pomyślnie");
            return $this->redirectToRoute("all_keywords");
        }
        return $this->render("keyword/edit.html.twig",
            ["keywordForm" => $form->createView()]);
    }

    /**
     * @Route("/keyword/delete/{id}", name="delete_keyword_process", methods={"GET"})
     * @param KeyWord $keyword
     * @return Response
     */
    public function deleteKeyWord(KeyWord $keyword)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($keyword);
        $em->flush();

        $this->addFlash("delete", "Pomyślnie usunięto słowo");
        return $this->redirectToRoute("all_keywords");
    }
}
