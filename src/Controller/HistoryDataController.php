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
    public function __construct(private readonly CompanyProviderInterface $companyProvider)
    {
    }

    #[Route('/', name: 'app_history_data')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(HistoryDataFormType::class, null, ['symbol_choices' => $this->getSymbolChoices()]);
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

    /**
     * @return array<string, string>
     */
    public function getSymbolChoices(): array
    {
        $companies = $this->companyProvider->getAllCompanies();
        $symbolChoices = [];

        foreach ($companies as $company) {
            $symbolChoices[$company->getSymbol()] = (string) $company;
        }

        return $symbolChoices;
    }
}
