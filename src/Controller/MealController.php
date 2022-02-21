<?php

namespace App\Controller;

use App\Entity\Meal;
use App\Form\Meal\MealGenerateType;
use App\Form\Meal\MealType;
use App\Form\SearchSimpleType;
use App\Repository\MealRepository;
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

    /**
     * @Route("/placard-communautaire/{page}/{search}", name="meal_closet")
     */
    public function closet(Request $request, MealRepository $repo, int $page = 1, string $search = null): Response
    {
        $perPage = MealRepository::CLOSET_PAGINATOR_PER_PAGE;
        $offset = ($page - 1) * $perPage;
        $paginator = $repo->getPaginator($offset, null, $search);
        $lastPage = (int) ($paginator->count() / $perPage) + 1;

        $form = $this->createForm(SearchSimpleType::class, ['search' => $search]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->redirectToRoute('meal_closet', [
                'search' => $form->get('search')->getData(),
                'page' => 1,
            ]);
        }

        return $this->render('meal/closet.html.twig', [
            'meals' => $paginator,
            'prev' => $page > 1 ? $page - 1 : null,
            'next' => $page < $lastPage ? $page + 1 : null,
            'form' => $form->createView(),
            'search' => $search,
        ]);
    }

    /**
     * @Route("/mes-plats/ajouter-depuis-placard-communautaire/{meal}", name="meal_add_from_closet")
     */
    public function addFromCloset(Request $request, Meal $meal, MealManager $manager): Response
    {
        dump($meal->getThemes());

        $myMeal = clone $meal;
        $myMeal->setUser($this->getUser());

        $form = $this->createForm(MealType::class, $myMeal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->save($form->getData());

            return $this->redirectToRoute('meal');
        }

        return $this->render('meal/closet-add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
