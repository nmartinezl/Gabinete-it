<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(): Response
    {
        $user = $this->getUser();

        // Secciones visibles para todos los usuarios autenticados
        $sections = [
            'Tareas'  => 'tarea_index',
            'Equipos' => 'app_equipo_index',
        ];

        // Agrega sección Usuarios si el usuario tiene el rol ROLE_ADMIN
        if ($this->isGranted('ROLE_ADMIN')) {
            $sections['Usuarios'] = 'app_usuario_index';
        }

        return $this->render('dashboard/index.html.twig', [
            'sections' => $sections,
        ]);
    }
}
