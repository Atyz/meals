<?php

namespace App\Controller;

use App\Entity\UserPassword;
use App\Form\Password\RequestType as PasswordRequestType;
use App\Form\Password\ResetType as PasswordResetType;
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
    public function requestSuccess(): Response
    {
        return $this->render('password/request-success.html.twig');
    }

    /**
     * @Route("/mot-de-passe/nouveau/succes", name="password_reset_success", methods={"GET"})
     */
    public function resetSuccess(): Response
    {
        return $this->render('password/reset-success.html.twig');
    }

    /**
     * @Route("/mot-de-passe/nouveau/{token}", name="password_reset", methods={"GET", "POST"})
     */
    public function reset(
        Request $request,
        PasswordManager $manager,
        ?UserPassword $userPwd = null
    ): Response {
        if (null === $userPwd) {
            return $this->render('password/reset-invalid.html.twig', ['code' => PasswordManager::RESET_ERROR_CODE_NOT_EXISTING]);
        }

        if (new \DateTime() > $userPwd->getExpireAt()) {
            return $this->render('password/reset-invalid.html.twig', ['code' => PasswordManager::RESET_ERROR_CODE_EXPIRED]);
        }

        $manager->secureRequest($userPwd);

        $form = $this->createForm(PasswordResetType::class, $userPwd->getUser());
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
