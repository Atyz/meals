<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Form\Menu\MenuType;
use App\Service\MenuNavigator;
use App\Service\MenuService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @Route("/semaine-{date}", name="home_date")
     */
    public function home(
        MenuService $service,
        MenuNavigator $navigator,
        ?\DateTime $date = null
    ): Response {
        $navigator->setBaseDate($date);

        if (!$navigator->isValid()) {
            return $this->redirectToRoute('home');
        }

        return $this->render('menu/home.html.twig', [
            'navigator' => $navigator,
            'menu' => $service->findOn(
                $this->getUser(),
                $navigator->getFrom()
            ),
        ]);
    }

    /**
     * @Route("/menu/generer-un-menu/{date}", name="menu_generate")
     */
    public function generate(
        Request $request,
        MenuService $service,
        MenuNavigator $navigator
    ): Response {
        $menu = (new Menu())->setUser($this->getUser());
        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $menu = $service->generate($form->getData());
            $navigator->setBaseDate($menu->getDate());

            return $this->redirectToRoute('home_date', [
                'date' => $navigator->getFrom()->format('Y-m-d'),
            ]);
        }

        return $this->render('menu/generation.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
