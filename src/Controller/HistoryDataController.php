<?php

namespace App\Controller;

use App\Form\HistoryDataFormType;
use App\Handler\HistoryRequestDataHandler;
use App\Model\HistoryRequestData;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HistoryDataController extends AbstractController
{
    #[Route('/', name: 'app_history_data')]
    public function index(Request $request, HistoryRequestDataHandler $handler): Response
    {
        $form = $this->createForm(HistoryDataFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var HistoryRequestData $historyRequestData */
            $historyRequestData = $form->getData();

            // TODO: handle form submit
            $historyData = $handler->handle($historyRequestData);
        }

        return $this->render('history_data/index.html.twig', [
            'mainForm' => $form->createView(),
            'historyData' => $historyData ?? null,
        ]);
    }
}
