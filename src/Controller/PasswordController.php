<?php

namespace App\Controller;

use App\Entity\UserPassword;
use App\Form\PasswordRequestType;
use App\Form\PasswordResetType;
use App\Service\PasswordManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PasswordController extends AbstractController
{
    /**
     * @Route("/mot-de-passe/demande", name="password_request", methods={"GET", "POST"})
     */
    public function request(Request $request, PasswordManager $manager): Response
    {
        $manager->cleanRequests();

        $form = $this->createForm(PasswordRequestType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $manager->createRequest($data['email']);

            return $this->redirectToRoute('password_request_success');
        }

        return $this->render('password/request.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/mot-de-passe/demande/succes", name="password_request_success", methods={"GET"})
     */
    public function requestSuccess()
    {
        return $this->render('password/request-success.html.twig');
    }

    /**
     * @Route("/mot-de-passe/recuperation/succes", name="password_reset_success", methods={"GET"})
     */
    public function resetSuccess()
    {
        return $this->render('password/reset-success.html.twig');
    }

    /**
     * @Route("/mot-de-passe/recuperation/{token}", name="password_reset", methods={"GET", "POST"})
     */
    public function reset(Request $request, UserPassword $userPwd, PasswordManager $manager)
    {
        if (null === $userPwd) {
            return $this->redirectToRoute('invalid-token', ['code' => 1]);
        }

        if (new \DateTime() > $userPwd->getExpireAt()) {
            return $this->redirectToRoute('invalid-token', ['code' => 2]);
        }

        $manager->secureRequest($userPwd);

        $user = $userPwd->getUser();

        $form = $this->createForm(PasswordResetType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->reset($userPwd);

            return $this->redirectToRoute('password_reset_success');
        }

        return $this->render('password/reset.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
