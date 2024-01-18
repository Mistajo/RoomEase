<?php

namespace App\Controller\Admin\Profile;

use App\Form\EditProfileFormType;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\EditProfilePasswordFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/admin')]
class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'admin.profile.index')]
    public function index(): Response
    {
        return $this->render('pages/admin/profile/index.html.twig');
    }

    #[Route('/profile/edit', name: 'admin.profile.edit', methods: ['GET', 'PUT'])]
    public function edit(Request $request, EntityManagerInterface $em): Response
    {
        // on recupere l'utilisateur connecté
        $user = $this->getUser();
        // on créer le formulaire pour modifier le profil en precisant la methode PUT
        $form = $this->createForm(EditProfileFormType::class, $user, ['method' => 'PUT']);
        // on prepare la requette 
        $form->handleRequest($request);
        // si le formulaire est soumis et valide 
        if ($form->isSubmitted() && $form->isValid()) {
            // le manager des entités prepare la requette
            $em->persist($user);
            // le manager des entités execute la requete
            $em->flush();
            // on affiche un message de succes
            $this->addFlash('success', 'Votre profil a bien été modifié');
            // on redirige vers la page de profil de l'admin
            return $this->redirectToRoute('admin.profile.index');
        }
        // on redirige vers la page de modification du profil de l'admin
        return $this->render('pages/admin/profile/edit.html.twig', [
            // on passe le formulaire dans la vue
            "form" => $form->createView()
        ]);
    }

    // Route de modification du mot de passe de l'admin
    #[Route('/profile/edit-password', name: 'admin.profile.edit_password', methods: ['GET', 'PUT'])]
    public function editPassword(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        //on recupère l'utilisateur connecté
        $user = $this->getUser();
        //On creer le formulaire, en utilisant la methode Put
        $form = $this->createForm(EditProfilePasswordFormType::class, null, [
            'method' => 'PUT'
        ]);
        //on recupere la requete
        $form->handleRequest($request);
        //si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            //on recupere les données du tableau
            $data = $request->request->all();
            //on recupere les données du mot de passe du formulaire associé à la  modificaion du profile
            $password = $data['edit_profile_password_form']['password']['first'];
            //on demande au hasher d'hasher le mot de passe
            $passwordHashed = $hasher->hashPassword($user, $password);
            //on modifie le mot de passe hasher
            $user->setPassword($passwordHashed);
            //l'entity manager prepare la requete
            $em->persist($user);
            //et l'execute
            $em->flush();
            //on affiche un message flash
            $this->addFlash('success', "Votre mot de passe a été modifié.");
            //on dirige vers la page d'acceuil du profil de l'admin
            return $this->redirectToRoute('admin.profile.index');
        }
        //dans le cas contraire on renvoit sur la page de modification du mot de passe 
        //on affiche le formulaire
        return $this->render("pages/admin/profile/edit_password.html.twig", [
            "form" => $form->createView()
        ]);
    }
}
