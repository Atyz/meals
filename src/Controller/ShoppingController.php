<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Entity\Shopping;
use App\Service\MenuNavigator;
use App\Service\ShoppingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShoppingController extends AbstractController
{
    /**
     * @Route("/ma-liste-de-course/{menu}", name="shopping")
     */
    public function home(
        Menu $menu,
        ShoppingService $service,
        MenuNavigator $navigator
    ): Response {
        $service->build($menu);
        $navigator->setBaseDate($menu->getDate());

        return $this->render('shopping/home.html.twig', [
            'shpCategories' => $service->findForMenu($menu),
            'navigator' => $navigator,
        ]);
    }

    /**
     * @Route("/ma-liste-de-course/prendre/{shopping}", name="shopping_take")
     */
    public function take(Shopping $shopping, ShoppingService $service): Response
    {
        $service->take($shopping);

        return $this->redirectToRoute('shopping', ['menu' => $shopping->getMenu()->getId()]);
    }

    /**
     * @Route("/ma-liste-de-course/deposer/{shopping}", name="shopping_untake")
     */
    public function untake(Shopping $shopping, ShoppingService $service): Response
    {
        $service->untake($shopping);

        return $this->redirectToRoute('shopping', ['menu' => $shopping->getMenu()->getId()]);
    }
}
