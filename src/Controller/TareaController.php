<?php

namespace App\Controller;

use App\Entity\Tarea;
use App\Form\TareaType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tareas')]
class TareaController extends AbstractController
{
    #[Route('/', name: 'tarea_index')]
    public function index(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        // Si el usuario tiene el rol TÉCNICO, mostrar solo sus tareas
        if (in_array('ROLE_TECNICO', $user->getRoles(), true)) {
            $tareas = $em->getRepository(Tarea::class)->findBy(['tecnico' => $user]);
        } else {
            // Si es admin, jefe u otro rol, mostrar todas
            $tareas = $em->getRepository(Tarea::class)->findAll();
        }

        return $this->render('tarea/index.html.twig', [
            'tareas' => $tareas,
        ]);
    }

    #[Route('/nueva', name: 'tarea_nueva')]
    public function nueva(Request $request, EntityManagerInterface $em): Response
    {
        $tarea = new Tarea();
        $form = $this->createForm(TareaType::class, $tarea);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();

            // Fecha y estado automáticos
            $tarea->setFechaCreacion(new \DateTime());
            $tarea->setEstado('pendiente');

            // Si no es administrador, se asigna como técnico
            if (!in_array('ROLE_ADMIN', $user->getRoles(), true)) {
                $tarea->setTecnico($user);
            }

            $em->persist($tarea);
            $em->flush();

            $this->addFlash('success', 'Tarea creada correctamente.');

            return $this->redirectToRoute('tarea_index');
        }

        return $this->render('tarea/nueva.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/editar', name: 'tarea_editar')]
    public function editar(Request $request, Tarea $tarea, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(TareaType::class, $tarea);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Tarea actualizada correctamente.');

            return $this->redirectToRoute('tarea_index');
        }

        return $this->render('tarea/editar.html.twig', [
            'form' => $form->createView(),
            'tarea' => $tarea
        ]);
    }

    #[Route('/{id}/eliminar', name: 'tarea_eliminar', methods: ['POST'])]
    public function eliminar(Request $request, Tarea $tarea, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('eliminar_tarea_' . $tarea->getId(), $request->request->get('_token'))) {
            $em->remove($tarea);
            $em->flush();
            $this->addFlash('success', 'Tarea eliminada correctamente.');
        }

        return $this->redirectToRoute('tarea_index');
    }

    #[Route('/{id}', name: 'tarea_ver', methods: ['GET'])]
    public function ver(Tarea $tarea): Response
    {
        return $this->render('tarea/ver.html.twig', [
            'tarea' => $tarea,
        ]);
    }
}
