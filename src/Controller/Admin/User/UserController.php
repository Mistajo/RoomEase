<?php

namespace App\Controller\Admin\User;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Form\EditUserRolesFormType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin')]
class UserController extends AbstractController
{
    #[Route('/user/list', name: 'admin.user.index')]
    public function index(UserRepository $userRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $search = $request->query->get('search');

        // Si un terme de recherche est saisi, filtrez la liste des utilisateurs
        if ($search) {
            $users = $userRepository->findBySearchTerm($search, $request->query->getInt('page', 1));
        } else {
            $users = $userRepository->findBy([], ['createdAt' => 'DESC']);
            $users = $paginator->paginate($users, $request->query->getInt('page', 1), 6);
        }

        return $this->render('pages/admin/user/index.html.twig', [
            "users" => $users,
        ]);
    }

    #[Route('/admin/user/{id}/edit_roles', name: 'admin.user.edit_roles', methods: ["GET", 'PUT'])]
    public function editRoles(User $user, Request $request, EntityManagerInterface $em): Response
    {
        // on récupère le formulaire, on renseigne l'user et la methode
        $form = $this->createForm(EditUserRolesFormType::class, $user, [
            'method' => 'PUT'
        ]);
        // on prepare la requete
        $form->handleRequest($request);
        // on valide le formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            // l'entity manager prepare la requete
            $em->persist($user);
            // L'entity manager envoie la requete
            $em->flush();
            // on affiche un message de succès
            $this->addFlash('success', "Les Roles de " . $user->getFirstName() . " " . $user->getLastName() . " " . "ont bien été modifiés");
            // on redirige vers la page d'accueil des utilisateurs
            return $this->redirectToRoute('admin.user.index');
        }
        // on redirige l'utilisateur vers la page d'édition des roles
        return $this->render("pages/admin/user/edit_roles.html.twig", [
            // on associe la categorie à la vue
            "user" => $user,
            // on associe le formulaire à la vue
            "form" => $form->createView()
        ]);
    }
    //Route pour la suppression d'un utilisateur
    #[Route('/admin/user/{id}/delete', name: 'admin.user.delete', methods: ['DELETE'])]
    public function delete(User $user, Request $request, EntityManagerInterface $em): Response
    {
        // si le token est valide
        if ($this->isCsrfTokenValid('delete_user_' . $user->getId(), $request->request->get('csrf_token'))) {
            // on le supprime
            $em->remove($user);
            // on mets à jour la base de données
            $em->flush();
            // on affiche un message de succès
            $this->addFlash('success', "L'utilisateur " . $user->getFirstName() . " " . $user->getLastName() . " a bien été supprimé");
        }
        // on redirige l'utilisateur vers la page d'accueil des utilisateurs
        return $this->redirectToRoute('admin.user.index');
    }
}
