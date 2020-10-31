<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\VilleType;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class VilleController extends AbstractController
{
    /**
     * @Route("/ville", name="ville")
     */
    public function list(VilleRepository $villeRepo, Request $request, EntityManagerInterface $emi)
    {
        //ajout campus
        $city = new Ville();
        $form = $this->createForm(VilleType::class, $city);
        $form -> handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $emi->persist($city);
            $emi->flush();
            return $this->redirectToRoute('ville');
        }

        return $this->render('ville/index.html.twig', [
            //info campus enregistrer
            'ville' => $villeRepo->findAll(),
            //Ajout Campus
            'VilleForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/ville/delete/{id}", name="delete_ville" , requirements={"id"="\d+"})
     */
    public function delete(int $id, EntityManagerInterface $emi, Ville $ville)
    {
        $ville = $emi->getRepository(Ville::class)->find($id);

        $emi->remove($ville);
        $emi->flush();

        return $this->redirectToRoute('ville');
    }

    /**
     * @Route("/ville/update/{id}", name="update_ville" , requirements={"id"="\d+"}, methods={"GET","POST"})
     */
    public function update(Ville $ville,Request $request)
    {
        $form = $this->createForm(VilleType::class, $ville);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('ville');
        }

        return $this->render('ville/index.html.twig', [
            'ville' => $ville,
            'VilleForm' => $form->createView(),
            'button' => 'Modifier'
        ]);
    }
}
