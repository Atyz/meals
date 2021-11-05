<?php

namespace App\Controller;

use App\Entity\Meal;
use App\Form\Meal\MealGenerateType;
use App\Form\Meal\MealType;
use App\Service\MealManager;
use App\Service\MealService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class MealController extends AbstractController
{
    /**
     * @Route("/mes-plats", name="meal")
     */
    public function home(ManagerRegistry $doctrine, SessionInterface $session): Response
    {
        $generateds = $session->get('generated.meals', null);
        $session->set('generated.meals', null);

        return $this->render('meal/home.html.twig', [
            'generateds' => $generateds,
            'meals' => $doctrine->getRepository(Meal::class)->findForUser($this->getUser()),
        ]);
    }

    /**
     * @Route("/mes-plats/nouveau", name="meal_new")
     */
    public function new(Request $request, MealManager $manager): Response
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
    public function edit(Request $request, Meal $meal, MealManager $manager): Response
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
    public function del(Meal $meal, MealManager $manager): Response
    {
        $manager->del($meal);

        return $this->redirectToRoute('meal');
    }

    /**
     * @Route("/mes-plats/generer", name="meal_generate")
     */
    public function generate(
        Request $request,
        MealService $service,
        SessionInterface $session
    ): Response {
        $form = $this->createForm(MealGenerateType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $session->set(
                'generated.meals',
                $service->generate(
                    $this->getUser(),
                    $form->getData()
                )
            );

            return $this->redirectToRoute('meal');
        }

        return $this->render('meal/generate.html.twig', [
            'form' => $form->createView(),
            'totalFields' => MealGenerateType::TOTAL_FIELDS,
        ]);
    }
}
