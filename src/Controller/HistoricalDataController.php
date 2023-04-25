<?php

namespace App\Controller;

use App\Form\HistoryDataFormType;
use App\Handler\HistoryRequestDataHandler;
use App\Model\HistoricalDataRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HistoricalDataController extends AbstractController
{
    #[Route('/', name: 'app_history_data')]
    public function index(
        Request $request,
        HistoryRequestDataHandler $handler
    ): Response {
        $form = $this->createForm(HistoryDataFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var HistoricalDataRequest $historicalRequestData */
            $historicalRequestData = $form->getData();
            try {
                $historyData = $handler->handle($historicalRequestData);
            } catch (\Throwable $e) {
                $this->addFlash('danger', $e->getMessage());
            }
        }

        return $this->render('history_data/index.html.twig', [
            'mainForm' => $form->createView(),
            'historyData' => $historyData ?? null,
        ]);
    }
}
