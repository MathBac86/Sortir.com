<?php

namespace App\Controller;

use App\Entity\Lieux;
use App\Form\LieuxType;
use App\Repository\LieuxRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LieuxController extends AbstractController
{
    /**
     * @Route("/lieux", name="lieux")
     */
    public function list(LieuxRepository $lieuxRepo, Request $request, EntityManagerInterface $emi)
    {
        //ajout campus
        $lieux = new Lieux();
        $form = $this->createForm(LieuxType::class, $lieux);
        $form -> handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $emi->persist($lieux);
            $emi->flush();

            $this->addFlash('success', 'Le lieu a été créé');
            return $this->redirectToRoute('lieux');
        }

        return $this->render('lieux/index.html.twig', [
            //info campus enregistrer
            'lieux' => $lieuxRepo->findAll(),
            //Ajout Campus
            'LieuxForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/lieux/delete/{id}", name="delete_lieu" , requirements={"id"="\d+"})
     */
    public function delete(int $id, EntityManagerInterface $emi, Lieux $lieux)
    {
        $lieux = $emi->getRepository(Lieux::class)->find($id);

        $emi->remove($lieux);
        $emi->flush();

        $this->addFlash('success', 'Le lieu a été supprimé');
        return $this->redirectToRoute('lieux');
    }

    /**
     * @Route("/lieux/update/{id}", name="update_lieu" , requirements={"id"="\d+"}, methods={"GET","POST"})
     */
    public function update(Lieux $lieux,Request $request)
    {
        $form = $this->createForm(LieuxType::class, $lieux);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Le lieu a été modifié');
            return $this->redirectToRoute('lieux');
        }

        return $this->render('lieux/index.html.twig', [
            'lieux' => $lieux,
            'LieuxForm' => $form->createView(),
            'button' => 'Modifier'
        ]);
    }
}
