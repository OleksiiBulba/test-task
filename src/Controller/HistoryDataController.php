<?php

namespace App\Controller;

use App\Form\HistoryDataFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HistoryDataController extends AbstractController
{
    #[Route('/', name: 'app_history_data')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(HistoryDataFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

        }

        return $this->render('history_data/index.html.twig', [
            'mainForm' => $form->createView(),
            'historyData' => [],
        ]);
    }
}
