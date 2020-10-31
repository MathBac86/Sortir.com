<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Inscription;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\AnnulSortieType;
use App\Form\SortieType;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    /**
     * @Route("/sortie", name="sortie")
     */
    public function index()
    {
        return $this->render('sortie/detail.html.twig', [
            'controller_name' => 'SortieController',
        ]);
    }

    /**
     * @Route("/AddSortie", name="add_sortie")
     */
    public function register(Request $request,
                             EntityManagerInterface $emi,
                             CampusRepository $campRepo,
                             EtatRepository $etatRepo,
                             ParticipantRepository $Partrepo)
    {
        $sortie = new Sortie();

        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $sortie->setArchivage(0);

            $organisor = $Partrepo->find($this->getUser()->getId());
            $sortie->setOrganisateur($organisor);

            $campus = $campRepo->findOneBy(['nomCampus' => $this->getUser()->getCampus()->getNomCampus()]);
            $sortie->setCampus($campus);

            $etat = $etatRepo->findOneBy(['libelleEtat'=>'A venir']);
            $sortie->setEtat($etat);

            $emi->persist($sortie);
            $emi->flush();

            $inscription = new Inscription();
            $inscription-> setParticipant($organisor);
            $inscription-> setSortie($sortie);
            $inscription-> setDateInscription(new \DateTime('now'));
            $emi->persist($inscription);
            $emi->flush();

            $this->addFlash('success', 'La sortie a été créée');
            return $this->redirectToRoute('home');
        }

        return $this->render('sortie/add.html.twig', [
            'sortieForm' => $sortieForm->createView()
        ]);
    }

    /**
     * @Route("/sortie/update/{id}",
     *     name="Modif_sortie",
     *     requirements={"id": "\d+"})
     */
    public function update(Request $request, Sortie $sortie)
    {
        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'La sortie a été modifiée');
            return $this->redirectToRoute('home');
        }

        return $this->render('sortie/update.html.twig', [
            'sortie' => $sortie,
            'sortieForm' => $sortieForm->createView()
        ]);
    }


    /**
     * @Route("/sortie/{id}",
     *     name="sortie_detail",
     *     requirements={"id": "\d+"})
     */
    public function detail(int $id, EntityManagerInterface $emi)
    {
        $sortieRepo = $emi->getRepository(Sortie::class);
        $sortie = $sortieRepo->find($id);

        $inscriRepo = $emi->getRepository(Inscription::class);
        $inscrit = $inscriRepo->findBy(array('Sortie' => $id));

        return $this->render("sortie/detail.html.twig", [
            'sortie' => $sortie,
            'inscrits' => $inscrit
        ]);
    }

    /**
     * @Route("/sorties/annuler/{id}",
     *     name="annul_sortie",
     *     requirements={"id": "\d+"})
     */
    public function annuler_sortie(Request $request, EntityManagerInterface $em, Sortie $sortie){

        $participant = $this->getUser();

        $form = $this->createForm(AnnulSortieType::class, $sortie);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $sortie->setMotifAnnulation($form['MotifAnnulation']->getData());
            $etat = $em->getRepository(Etat::class)->findOneBy(['libelleEtat'=>'Annulée']);
            $sortie->setEtat($etat);

            $em->flush();

            $this->addFlash('success', 'La sortie a été annulée');
            return $this->redirectToRoute('home');

        }

        return $this->render('sortie/annuler.html.twig', [
            'sortie' => $sortie,
            'participants' => $participant,
            'AnnulSortForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/sorties/inscription/{id}", name="inscription_sortie")
     */
    public function add(EntityManagerInterface $emi, Sortie $sortie)
    {
        $participant = $emi->getRepository(Participant::class)->find($this->getUser()->getId());
        $inscription = new Inscription();
        $inscription->setDateInscription(new \DateTime());
        $inscription->setSortie($sortie);
        $inscription->setParticipant($participant);

        $emi->persist($inscription);
        $emi->flush();

        $this->addFlash('success', 'Votre inscription à la sortie a été enregistrée');
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/sorties/removeInscription/{id}", name="delete_inscription_sortie")
     */
    public function remove_participant(EntityManagerInterface $em, Request $request)
    {
        $participant = $this->getUser();

        $sortie = $em->getRepository(Sortie::class)->find($request->get('id'));

        $inscription = $em->getRepository(Inscription::class)->findBy(['Sortie'=>$sortie->getId(), 'Participant'=>$participant->getId()]);
        $em->remove($inscription[0]);
        $em->flush();

        $this->addFlash('success', 'Votre désinscription à la sortie a été enregistrée');
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/sortie/participant/{id}",
     *     name="participant_sortie",
     *     requirements={"id": "\d+"})
     */
    public function partSort(int $id, EntityManagerInterface $emi)
    {
        $partRepo = $emi->getRepository(Participant::class);
        $participant = $partRepo->find($id);

        return $this->render("participant/detail.html.twig", [
            'participant' => $participant,
        ]);
    }
}
