<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Entity\MenuDay;
use App\Form\Menu\MenuDayIngredientType;
use App\Service\MenuDayService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuDayController extends AbstractController
{
    /**
     * @Route("/repas/changer-un-plat/{day}", name="menu_day_change")
     */
    public function change(MenuDay $day, MenuDayService $service): Response
    {
        $service->changeMeal($day);

        return $this->redirectToRoute('home_date', [
            'date' => $day->getMenu()->getDate()->format('Y-m-d'),
        ]);
    }

    /**
     * @Route("/menu/enlever-un-plat/{day}", name="menu_day_remove")
     */
    public function remove(MenuDay $day, MenuDayService $service): Response
    {
        $service->removeMeal($day);

        return $this->redirectToRoute('home_date', [
            'date' => $day->getMenu()->getDate()->format('Y-m-d'),
        ]);
    }

    /**
     * @Route("/menu/ajouter-ingredient/{day}", name="menu_day_add")
     */
    public function add(
        Request $request,
        MenuDay $day,
        MenuDayService $service
    ): Response {
        $form = $this->createForm(MenuDayIngredientType::class, $day);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $service->save($form->getData());

            return new JsonResponse('ok');
        }

        return $this->render('menuDay/add.html.twig', [
            'day' => $day,
            'form' => $form->createView(),
        ]);
    }
}
