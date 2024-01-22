<?php

namespace App\Controller\User\Profile;

use App\Form\EditProfileFormType;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\EditProfilePasswordFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/user')]
class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'user.profile.index')]
    public function index(): Response
    {
        return $this->render('pages/user/profile/index.html.twig');
    }

    #[Route('/profile/edit', name: 'user.profile.edit', methods: ['GET', 'PUT'])]
    public function edit(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(EditProfileFormType::class, $user, [
            'method' => 'PUT'
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', "Le profil a été modifié.");

            return $this->redirectToRoute('user.profile.index');
        }

        return $this->render("pages/user/profile/edit.html.twig", [
            'form' => $form->createView()
        ]);
    }


    #[Route('/user/profile/edit-password', name: 'user.profile.edit_password', methods: ['GET', 'PUT'])]
    public function editPassword(
        Request $request,
        UserPasswordHasherInterface $hasher,
        EntityManagerInterface $em
    ): Response {

        $user = $this->getUser();

        $form = $this->createForm(EditProfilePasswordFormType::class, null, [
            "method" => "PUT"
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $request->request->all();
            $password = $data['edit_profile_password_form']['password']['first'];

            $passwordHashed = $hasher->hashPassword($user, $password);

            $user->setPassword($passwordHashed);

            $em->persist($user);
            $em->flush();

            $this->addFlash("success", "Votre mot de passe a été modifié.");

            return $this->redirectToRoute('user.profile.index');
        }

        return $this->render("pages/user/profile/edit_password.html.twig", [
            'form' => $form->createView()
        ]);
    }


    #[Route('/user/profile/delete', name: 'user.profile.delete', methods: ['DELETE'])]
    public function delete(Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid("profile-delete", $request->request->get('csrf_token'))) {
            $user = $this->getUser();

            $reservations = $user->getReservations();

            foreach ($reservations as $reservation) {
                $reservation->setUser(null);
            }

            $em->remove($user);
            $em->flush();

            $this->container->get('security.token_storage')->setToken(null);

            $this->addFlash('success', "Votre compte a bien été supprimé. Vous allez nous manquer.");
        }

        return $this->redirectToRoute('visitor.authentication.login');
    }
}
