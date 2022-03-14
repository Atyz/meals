<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Entity\Shopping;
use App\Form\Shopping\FreeShoppingType;
use App\Service\MenuNavigator;
use App\Service\ShoppingBuilder;
use App\Service\ShoppingManager;
use App\Service\ShoppingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
        ShoppingBuilder $builder,
        MenuNavigator $navigator
    ): Response {
        $builder->setMenu($menu);
        $builder->build();

        $navigator->setBaseDate($menu->getDate());

        return $this->render('shopping/home.html.twig', [
            'shpCategories' => $service->findForMenu($menu),
            'navigator' => $navigator,
            'menu' => $menu,
        ]);
    }

    /**
     * @Route("/ma-liste-de-course/prendre/{shopping}", name="shopping_take")
     */
    public function take(Shopping $shopping, ShoppingManager $manager): Response
    {
        $manager->take($shopping);

        return $this->redirectToRoute('shopping', ['menu' => $shopping->getMenu()->getId()]);
    }

    /**
     * @Route("/ma-liste-de-course/deposer/{shopping}", name="shopping_untake")
     */
    public function untake(Shopping $shopping, ShoppingManager $manager): Response
    {
        $manager->untake($shopping);

        return $this->redirectToRoute('shopping', ['menu' => $shopping->getMenu()->getId()]);
    }

    /**
     * @Route("/ma-liste-de-course/manuel/ajouter/{menu}", name="shopping_new")
     */
    public function new(Menu $menu, Request $request, ShoppingManager $manager): Response
    {
        $shopping = (new Shopping())->setMenu($menu);
        $form = $this->createForm(FreeShoppingType::class, $shopping);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->save($form->getData());

            return $this->redirectToRoute('shopping', ['menu' => $menu->getId()]);
        }

        return $this->render('shopping/new.html.twig', [
            'form' => $form->createView(),
            'menu' => $menu,
        ]);
    }

    /**
     * @Route("/ma-liste-de-course/manuel/modifier/{shopping}", name="shopping_edit")
     */
    public function edit(Shopping $shopping, Request $request, ShoppingManager $manager): Response
    {
        $menu = $shopping->getMenu();
        $form = $this->createForm(FreeShoppingType::class, $shopping);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->save($form->getData());

            return $this->redirectToRoute('shopping', ['menu' => $menu->getId()]);
        }

        return $this->render('shopping/edit.html.twig', [
            'form' => $form->createView(),
            'menu' => $menu,
        ]);
    }

    /**
     * @Route("/ma-liste-de-course/manuel/supprimer/{shopping}", name="shopping_del")
     */
    public function del(Shopping $shopping, ShoppingManager $manager): Response
    {
        $manager->delete($shopping);

        return $this->redirectToRoute('shopping', ['menu' => $shopping->getMenu()->getId()]);
    }
}
