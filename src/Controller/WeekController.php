<?php

namespace App\Controller;

use App\Entity\Week;
use App\Form\Week\WeekType;
use App\Service\WeekManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class WeekController extends AbstractController
{
    /**
     * @Route("/mes-semaines-types", name="week")
     */
    public function home(ManagerRegistry $doctrine)
    {
        return $this->render('week/home.html.twig', [
            'weeks' => $doctrine->getRepository(Week::class)->findForUser($this->getUser()),
        ]);
    }

    /**
     * @Route("/mes-semaines-types/nouvelle", name="week_new")
     */
    public function new(Request $request, WeekManager $manager)
    {
        $week = (new Week())->setUser($this->getUser());
        $form = $this->createForm(WeekType::class, $week);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->save($form->getData());

            return $this->redirectToRoute('week');
        }

        return $this->render('week/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/mes-semaines-types/modifier/{id}", name="week_edit")
     */
    public function edit(Request $request, Week $week, WeekManager $manager)
    {
        $form = $this->createForm(WeekType::class, $week);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->save($form->getData());

            return $this->redirectToRoute('week');
        }

        return $this->render('week/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/mes-semaines-types/supprimer/{id}", name="week_del")
     */
    public function del(Week $week, WeekManager $manager)
    {
        $manager->del($week);

        return $this->redirectToRoute('week');
    }
}
