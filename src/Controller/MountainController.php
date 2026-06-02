<?php

namespace App\Controller;

use App\Entity\Mountain;
use App\Form\MountainType;
use App\Repository\MountainRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/mountain')]
final class MountainController extends AbstractController
{
    #[Route(name: 'app_mountain_index', methods: ['GET'])]
    public function index(MountainRepository $mountainRepository): Response
    {
        return $this->render('mountain/index.html.twig', [
            'mountains' => $mountainRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_mountain_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $mountain = new Mountain();
        $form = $this->createForm(MountainType::class, $mountain);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($mountain);
            $entityManager->flush();

            return $this->redirectToRoute('app_mountain_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('mountain/new.html.twig', [
            'mountain' => $mountain,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_mountain_show', methods: ['GET'])]
    public function show(Mountain $mountain): Response
    {
        return $this->render('mountain/show.html.twig', [
            'mountain' => $mountain,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_mountain_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Mountain $mountain, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MountainType::class, $mountain);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_mountain_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('mountain/edit.html.twig', [
            'mountain' => $mountain,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_mountain_delete', methods: ['POST'])]
    public function delete(Request $request, Mountain $mountain, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mountain->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($mountain);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_mountain_index', [], Response::HTTP_SEE_OTHER);
    }
}
