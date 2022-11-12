<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;



class RegisterController extends AbstractController
{
    #[Route('/inscription', name: 'register')]
    public function index(Request $request,EntityManagerInterface $entityManager,UserPasswordHasherInterface $encode): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class,$user);
        $form->handleRequest($request);
        $user = $form->getData();
        $plaintextPassword = $user->getPassword();


        if ($form->isSubmitted() && $form->isValid())
        {
            //Hash le mot de passe avant l'entrée en base de données
            $hashedPassword = $encode->hashPassword(
                $user,
                $plaintextPassword
            );

            $user->setPassword($hashedPassword);
            $entityManager->persist($user);
            $entityManager->flush();
        }


        return $this->render('register/index.html.twig', [
            'form'            => $form->createView(),
        ]);
    }


}
