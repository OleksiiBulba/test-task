<?php

namespace App\Controller;

use App\Company\Provider\CompanyProviderInterface;
use App\Form\HistoryDataFormType;
use App\Model\HistoryDataRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HistoryDataController extends AbstractController
{
    #[Route('/', name: 'app_history_data')]
    public function index(Request $request, CompanyProviderInterface $companyProvider): Response
    {
        $form = $this->createForm(HistoryDataFormType::class, null, ['symbol_choices' => $companyProvider->getAllCompanies()]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var HistoryDataRequest $historyRequestFormData */
            $historyRequestFormData = $form->getData();

            // TODO: handle form submit
            $historyData = [];
        }

        return $this->render('history_data/index.html.twig', [
            'mainForm' => $form->createView(),
            'historyData' => $historyData ?? [],
        ]);
    }
}
