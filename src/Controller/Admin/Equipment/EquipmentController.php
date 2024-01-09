<?php

namespace App\Controller\Admin\Equipment;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class EquipmentController extends AbstractController
{
    #[Route('/equipments/list', name: 'admin.equipments.index')]
    public function index(): Response
    {
        return $this->render('pages/admin/equipment/index.html.twig');
    }
}
