<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Form\Menu\MenuType;
use App\Service\MenuService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MenuController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home(MenuService $service)
    {
        return $this->render('menu/home.html.twig', [
            'menu' => $service->findCurrent($this->getUser()),
        ]);
    }

    /**
     * @Route("/menu/generer-un-menu", name="menu_generate")
     */
    public function generate(Request $request, MenuService $service)
    {
        $menu = (new Menu())->setUser($this->getUser());
        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $service->generate($form->getData());

            return $this->redirectToRoute('home');
        }

        return $this->render('menu/generation.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
