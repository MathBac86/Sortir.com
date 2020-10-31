<?php

namespace App\Controller;

use App\Form\FilterType;
use App\Repository\CampusRepository;
use App\Repository\InscriptionRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/Accueil", name="home")
     */
    public function index(SortieRepository $sortieRepo, ParticipantRepository $participantRepo,
                          Request $request, InscriptionRepository $inscriptionRepo)
    {
        $sortie = null;

        $form = $this->createForm(FilterType::class);
        $form -> handleRequest($request);

        $inscrit = null;
        $noInscrit = null;
        $beorganisator = null;

        if ($form->isSubmitted() && $form->isValid()) {

            $du = $form['dateDebut_du']->getData();
            $au = $form['dateDebut_au']->getData();

            $nom = $form['nom']->getData();

            $campus = $form['campus']->getData();

            $beorganisator = $form['organisateur']->getData();

            $inscrit = $form['BeInscrit']->getData();
            $noInscrit = $form['NotInscrit']->getData();

            $fini = $form['Finish']->getData();
            $inscription = $inscriptionRepo->findAll();
            $participant = $participantRepo->find($this->getUser()->getId());

            $sortie = $sortieRepo->findAllFilter($participant, $campus, $nom, $inscrit, $noInscrit,$beorganisator, $du, $au, $fini);
/*            $sortie = $sortieRepo->findAllFilter($participant, $campus, $nom, $inscrit,$beorganisator, $du, $au, $fini);*/
        } else {
            $inscription = $inscriptionRepo->findAll();
            $sortie = $sortieRepo->findAll();

        }


        return $this->render('home/home.html.twig', [
            //info sorties enregistrer
            'sorties' => $sortie,
            //info inscrit / non inscrita
            'part' => $inscription,
            'inscrit' => $inscrit,
            'noInscrit' => $noInscrit,
            //Ajout Campus
            'FilterForm' => $form->createView()
        ]);
    }
}
