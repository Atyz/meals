<?php

namespace App\Controller;

use App\Entity\Meal;
use App\Form\Meal\MealType;
use App\Service\MealManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MealsController extends AbstractController
{
    /**
     * @Route("/mes-plats", name="meal")
     */
    public function home(ManagerRegistry $doctrine)
    {
        return $this->render('meal/home.html.twig', [
            'meals' => $doctrine->getRepository(Meal::class)->findForUser($this->getUser()),
        ]);
    }

    /**
     * @Route("/mes-plats/nouveau", name="meal_new")
     */
    public function new(Request $request, MealManager $manager)
    {
        $meal = (new Meal())->setUser($this->getUser());
        $form = $this->createForm(MealType::class, $meal);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->save($form->getData());

            return $this->redirectToRoute('meal');
        }

        return $this->render('meal/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/mes-plats/modifier/{id}", name="meal_edit")
     */
    public function edit(Request $request, Meal $meal, MealManager $manager)
    {
        $form = $this->createForm(MealType::class, $meal);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->save($form->getData());

            return $this->redirectToRoute('meal');
        }

        return $this->render('meal/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/mes-plats/supprimer/{id}", name="meal_del")
     */
    public function del(Request $request, Meal $meal, MealManager $manager)
    {
        $manager->del($meal);

        return $this->redirectToRoute('meal');
    }
}
