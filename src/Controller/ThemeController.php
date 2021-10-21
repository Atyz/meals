<?php

namespace App\Controller;

use App\Entity\Theme;
use App\Form\Theme\ThemeType;
use App\Service\ThemeManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ThemeController extends AbstractController
{
    /**
     * @Route("/mes-themes", name="theme")
     */
    public function home(ManagerRegistry $doctrine)
    {
        return $this->render('theme/home.html.twig', [
            'themes' => $doctrine->getRepository(Theme::class)->findForUser($this->getUser()),
        ]);
    }

    /**
     * @Route("/mes-themes/nouveau", name="theme_new")
     */
    public function new(Request $request, ThemeManager $manager)
    {
        $theme = (new Theme())->setUser($this->getUser());
        $form = $this->createForm(ThemeType::class, $theme);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->save($form->getData());

            return $this->redirectToRoute('theme');
        }

        return $this->render('theme/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/mes-themes/modifier/{id}", name="theme_edit")
     */
    public function edit(Request $request, Theme $theme, ThemeManager $manager)
    {
        $form = $this->createForm(ThemeType::class, $theme);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->save($form->getData());

            return $this->redirectToRoute('theme');
        }

        return $this->render('theme/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/mes-themes/supprimer/{id}", name="theme_del")
     */
    public function del(Theme $theme, ThemeManager $manager)
    {
        $manager->del($theme);

        return $this->redirectToRoute('theme');
    }
}
