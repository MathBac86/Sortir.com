<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Form\CampusType;
use App\Repository\CampusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CampusController extends AbstractController
{
    /**
     * @Route("/campus", name="campus")
     */
    public function list(CampusRepository $campusRepo, Request $request, EntityManagerInterface $emi)
    {
        //ajout campus
        $camp = new Campus();
        $form = $this->createForm(CampusType::class, $camp);
        $form -> handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $emi->persist($camp);
            $emi->flush();

            $this->addFlash('success', 'Le campus a été créé');
            return $this->redirectToRoute('campus');
        }

        return $this->render('campus/index.html.twig', [
            //info campus enregistrer
            'campus' => $campusRepo->findAll(),
            //Ajout Campus
            'CampusForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/campus/delete/{id}", name="delete_campus" , requirements={"id"="\d+"})
     */
    public function delete(int $id, EntityManagerInterface $emi, Campus $campus)
    {
        $campus = $emi->getRepository(Campus::class)->find($id);

        $emi->remove($campus);
        $emi->flush();

        $this->addFlash('success', 'Le campus a été supprimé');
        return $this->redirectToRoute('campus');
    }

    /**
     * @Route("/campus/update/{id}", name="update_campus" , requirements={"id"="\d+"}, methods={"GET","POST"})
     */
    public function update(Campus $campus,Request $request)
    {
        $form = $this->createForm(CampusType::class, $campus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Le lieu a été modifié');
            return $this->redirectToRoute('campus');
        }

        return $this->render('campus/index.html.twig', [
            'campus' => $campus,
            'CampusForm' => $form->createView(),
            'button' => 'Modifier'
        ]);
    }
}
