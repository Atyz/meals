<?php

namespace App\Controller\Admin;

use App\Entity\Ingredient;
use App\Form\Ingredient\IngredientType;
use App\Repository\IngredientRepository;
use App\Service\IngredientManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IngredientController extends AbstractController
{
    /**
     * @Route("/admin/ingredient/liste/{offset}", name="admin_ingredient")
     */
    public function home(IngredientRepository $repo, int $offset = null): Response
    {
        $offset = max(0, $offset);
        $paginator = $repo->getPaginator($offset);
        $perPage = IngredientRepository::ADMIN_PAGINATOR_PER_PAGE;

        return $this->render('admin/ingredient/home.html.twig', [
            'ingredients' => $paginator,
            'prev' => $offset - $perPage,
            'next' => min(count($paginator), $offset + $perPage),
        ]);
    }

    /**
     * @Route("/admin/ingredient/nouveau", name="admin_ingredient_new")
     */
    public function new(Request $request, IngredientManager $manager): Response
    {
        $form = $this->createForm(IngredientType::class, new Ingredient());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->save($form->getData());

            return $this->redirectToRoute('admin_ingredient');
        }

        return $this->render('admin/ingredient/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/ingredient/modifier/{id}", name="admin_ingredient_edit")
     */
    public function edit(Request $request, Ingredient $ingredient, IngredientManager $manager): Response
    {
        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->save($form->getData());

            return $this->redirectToRoute('admin_ingredient');
        }

        return $this->render('admin/ingredient/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/ingredient/supprimer/{id}", name="admin_ingredient_del")
     */
    public function del(Ingredient $ingredient, IngredientManager $manager): Response
    {
        $manager->del($ingredient);

        return $this->redirectToRoute('admin_ingredient');
    }
}
