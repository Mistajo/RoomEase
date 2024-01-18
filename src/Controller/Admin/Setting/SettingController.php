<?php

namespace App\Controller\Admin\Setting;

use App\Entity\Setting;
use App\Form\SettingFormType;
use App\Repository\SettingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin')]
class SettingController extends AbstractController
{
    #[Route('/setting', name: 'admin.setting.index')]
    public function index(SettingRepository $settingRepository): Response
    {
        // on recupere toutes les données des parametres
        $data = $settingRepository->findAll();
        // on mets les données dans un tableau 
        $setting = $data[0];
        return $this->render('pages/admin/setting/index.html.twig', [
            'setting' => $setting,
        ]);
    }

    #[Route('/setting{id}/edit', name: 'admin.setting.edit', methods: ['GET', 'PUT'])]
    public function edit(Setting $setting, EntityManagerInterface $em, Request $request): Response
    {
        // on creer le formulaire, on precise la methode PUT
        $form = $this->createForm(SettingFormType::class, $setting, [
            'method' => 'PUT'
        ]);
        // Associons les données de la requete eu formulaire
        $form->handleRequest($request);
        // si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // on prepare la requette 
            $em->persist($setting);
            // on execute la requette
            $em->flush();
            // on affiche un message de succes
            $this->addFlash('success', 'Les paramètres du site ont bien été modifiés');
            // on redirige vers la page de liste des parametres
            return $this->redirectToRoute('admin.setting.index');
        }
        // on redireige vers la page de modification
        return $this->render('pages/admin/setting/edit.html.twig', [
            // on passe le formulaire dans la vue
            'form' => $form->createView()
        ]);
    }
}
