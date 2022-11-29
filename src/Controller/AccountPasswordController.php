<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use http\Client\Curl\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AccountPasswordController extends AbstractController
{
    #[Route('/compte/modifier-mon-mot-de-passe', name: 'account_password')]
    public function index(Request $request,EntityManagerInterface $entityManager,UserPasswordHasherInterface $encode): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class,$user);
        $form->handleRequest($request);
        $notification ='';


        if ($form->isSubmitted() && $form->isValid())
        {
            $old_pwd = $form -> get('old_password')->getData();
            if($encode->isPasswordValid($user,$old_pwd))
            {
                $new_pwd = $form ->get('new_password')->getData();
                //Hash le mot de passe avant l'entrée en base de données
                $hashedPassword = $encode->hashPassword($user, $new_pwd );
                $user -> setPassword($hashedPassword);
                $entityManager->flush();
                $notification = 'Votre mot de passe à bien été mis à jour';
            }else{
                $notification = 'Votre mot de passe actuel n\'est pas le bon';
            }


        }




        return $this->render('account/password.html.twig', [
            'controller_name' => 'AccountPasswordController',
            'form' => $form->createView(),
            'notification' => $notification,
        ]);
    }
}
