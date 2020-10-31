<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\CSVType;
use App\Form\ModifParticipantType;
use App\Form\ModifPasswordType;
use App\Form\OubliPasswordType;
use App\Repository\SortieRepository;
use League\Csv\Reader;
use App\Form\RegisterType;
use App\Repository\CampusRepository;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ParticipantController extends AbstractController
{
    /**
     * @Route("/participant", name="participant")
     */
    public function index()
    {
        return $this->render('participant/index.html.twig', [
            'controller_name' => 'ParticipantController',
        ]);
    }

    /**
     * @Route("/Inscription", name="inscription")
     */
    public function register(Request $request,
                             EntityManagerInterface $emi,
                             UserPasswordEncoderInterface $encoder)
    {
        $user = new Participant();
        $registerForm = $this->createForm(RegisterType::class, $user);

        $registerForm->handleRequest($request);
        if ($registerForm->isSubmitted() && $registerForm->isValid()) {
            $user->setRoles(['ROLE_USER']);
            $user->setActif(1);
            $user->setUpdatedAt(new \DateTime('now'));
            $hashed = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hashed);
            $emi->persist($user);
            $emi->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('participant/register.html.twig', [
            'registerForm' => $registerForm->createView()
        ]);
    }

    /**
     * @Route("/participant/{id}",
     *     name="participant_modif",
     *     requirements={"id": "\d+"})
     */
    public function edit(Request $request, Participant $participant): Response
    {
        $form = $this->createForm(ModifParticipantType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('participant');
        }

        return $this->render('participant/update.html.twig', [
            'participant' => $participant,
            'Registerform' => $form->createView(),
        ]);
    }

    /**
     * @Route("/participant/Password/{id}",
     *     name="password_modif",
     *     requirements={"id": "\d+"})
     */
    public function password(Request $request, Participant $participant, UserPasswordEncoderInterface $encoder): Response
    {
        $form = $this->createForm(ModifPasswordType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hashed = $encoder->encodePassword($participant, $participant->getPassword());
            $participant->setPassword($hashed);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Votre nouveau mot de passe a été enregistré');
            return $this->redirectToRoute('participant');
        }

        return $this->render('participant/update.html.twig', [
            'participant' => $participant,
            'Registerform' => $form->createView(),
        ]);
    }

    /**
     * @Route("/participant/liste",
     *     name="liste")
     */
    public function liste(ParticipantRepository $partRepo)
    {
        $participant = $partRepo->findAll();

        return $this->render('participant/liste.html.twig', [
            'participant' => $participant
        ]);

    }

    /**
     * @Route("/participant/désactif/{id}",
     *     name="participant_desactif",
     *     requirements={"id": "\d+"})
     */
    public function desactive(Participant $participant): Response
    {
        $participant->setActif(0);
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('success', 'Le participant a été désactivé');
        return $this->redirectToRoute('liste');

    }

    /**
     * @Route("/participant/actif/{id}",
     *     name="participant_actif",
     *     requirements={"id": "\d+"})
     */
    public function active(Participant $participant): Response
    {
        $participant->setActif(1);
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('success', 'Le participant a été activé');
        return $this->redirectToRoute('liste');

    }

    /**
     * @Route("/participant/manuel", name="inscription_manuel")
     */
    public function inscriptionManuel(Request $request,
                                      EntityManagerInterface $emi,
                                      UserPasswordEncoderInterface $encoder)
    {
        $user = new Participant();
        $registerForm = $this->createForm(RegisterType::class, $user);

        $registerForm->handleRequest($request);
        if ($registerForm->isSubmitted() && $registerForm->isValid()) {
            $user->setRoles(['ROLE_USER']);
            $user->setActif(1);
            $user->setUpdatedAt(new \DateTime('now'));
            $hashed = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hashed);
            $emi->persist($user);
            $emi->flush();

            $this->addFlash('success', 'Le nouveau participant a été enregistré');
            return $this->redirectToRoute('liste');
        }

        return $this->render('participant/inscriptManuel.html.twig', [
            'registerForm' => $registerForm->createView()
        ]);
    }

    /**
     * @Route("/participant/CSV", name="add_csv")
     */
    public function addByCSV(ParticipantRepository $partRepo, ValidatorInterface $validator, Request $request, UserPasswordEncoderInterface $encoder, EntityManagerInterface $em, CampusRepository $campusRepository)
    {
        $participant = $partRepo->findAll();

        $csvForm = $this->createForm(CSVType::class);
        $csvForm->handleRequest($request);

        if ($csvForm->isSubmitted() && $csvForm->isValid()) {

            $csvFile = $csvForm->get('Cvs')->getData();

            if ($csvFile) {


                $originalCsvName = pathinfo($csvFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeCsv = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalCsvName);
                $newPathFile = $safeCsv . '-' . uniqid() . '.' . $csvFile->getClientOriginalExtension();
                $csv = 'csv';

                if ($csvFile->getClientOriginalExtension() == $csv) {

                    try {
                        $csvFile->move(
                            $this->getParameter('CvsData'),
                            $newPathFile
                        );
                    } catch (FileException $e) {
                        $this->addFlash('danger', 'une erreur c\'est produite lors du chargement du fichier csv');
                    }


                    $filePath = $this->getParameter('CvsData') . '/' . $newPathFile;

                    $reader = Reader::createFromPath($filePath);
                    $records = $reader->getRecords(['pseudo', 'nom', 'prenom', 'tel', 'email', 'password', 'CampusId']);

                    $log_success= array();
                    $log_error =array();

                    foreach ($records as $offset => $record) {

                        $user = new Participant();
                        $hashed = $encoder->encodePassword($user, 'password');
                        $campus = $campusRepository->findOneBy(['id' => $this->getUser()->getCampus()]);


                        $user->setPseudo($record['pseudo']);
                        $user->setNom($record['nom']);
                        $user->setPrenom($record['prenom']);
                        $user->setTelephone($record['tel']);
                        $user->setMail($record['email']);
                        $user->setPassword($hashed);
                        $user->setCampus($campus);
                        $user->setRoles(['ROLE_USER']);
                        $user->setActif(true);
                        $user->setUpdatedAt(new \DateTime('now'));

                        $errors = $validator->validate($user);

                        if (count($errors) > 0) {
                            $log_error[count($log_error)]=$user;
                        } else {
                            $log_success[count($log_success)]=$user;
                            $em->persist($user);
                            $em->flush();

                        }

                        $fs = new Filesystem();
                        $fs->remove($filePath);
                    }

                    $this->addFlash('success', 'il y a eu ' .count($log_success). ' utilisateur(s) inséré avec succés!');
                    $this->addFlash('danger', 'il y a eu ' .count($log_error). ' utilisateur(s) qui n\'ont pas été inséré en base');
                } else {
                    $this->addFlash('warning', 'le format du fichier doit etre ".csv"');

                }

                $participant = $partRepo->findAll();
            }
            return $this->render('participant/liste.html.twig', [
                'participant'=> $participant,
                'controller_name' => 'ParticipantController',
                'csvForm' => $csvForm->createView(),
                'log_success'=>$log_success,
                'log_error'=>$log_error,
                'errors' => $errors,
            ]);


        }

        return $this->render('participant/csv.html.twig', [
            'participant' => $participant,
            'csvForm' => $csvForm->createView(),
        ]);

    }

    /**
     * Supprime un utilisateur
     * sauf si :
     *  - l'utilisateur organise une ou plusieurs sortie à venir
     *  - l'utilisateur est inscrit une ou plusieurs sortie à venir
     * @Route("/participant/supprimer/{id}", requirements={"id"="\d+"}, name="remove_participant")
     */
    public function removeUser($id, EntityManagerInterface $em, Participant $user, SortieRepository $sortieRepository){

        $user = $em->getRepository(Participant::class)->find($id);
        $roles= $user->getRoles();
        $isAdmin = in_array("ROLE_ADMIN", $roles);
        if (!$user) {
            throw $this->createNotFoundException(
                'Aucun utilisateur trouvé avec la référence : '.$id
            );
        } elseif($isAdmin){
            $this->addFlash('danger', 'L\'utilisateur possède des droits administrateur et ne peut pas être supprimé !');
            return $this->redirectToRoute('liste');
        }

        //vérifie si l'utilisateur organise une ou plusieurs sorties
        $isOrganizer = $sortieRepository->findBy(['organisateur' => $user, 'Etat' => 'A venir', 'Etat' => 'En-cours']);
        if (count($isOrganizer) > 0){
            $this->addFlash('danger', 'L\'utilisateur organise une ou plusieurs sorties et ne peut pas être supprimé !');
            return $this->redirectToRoute('liste');
        }

        //vérifie si l'utilisateur est inscrit à une ou plusieurs sorties
        $isRegistered = $sortieRepository->findRegistered($user);
        //dd($isRegistered);
        if (count($isRegistered) > 0){
            $this->addFlash('danger', 'L\'utilisateur est inscrit à une ou plusieurs sorties  et ne peut pas être supprimé !');
            return $this->redirectToRoute('liste');
        }
        $em->remove($user);
        $em->flush();

        $this->addFlash('success', 'L\'utilisateur a bien été supprimé');
        return $this->redirectToRoute('liste');
    }

    /**
     * @Route("/oubli", name="oubli_mdp")
     */
    public function oubli(Request $request)
    {
        $user = new Participant();
        $registerForm = $this->createForm(OubliPasswordType::class, $user);
        $registerForm->handleRequest($request);

        if ($registerForm->isSubmitted() && $registerForm->isValid()) {


            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/oubli.html.twig', [
            'OubliMDPForm' => $registerForm->createView()
        ]);
    }

}
