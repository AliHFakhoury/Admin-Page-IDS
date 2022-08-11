<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class LoginSecurityController extends Controller
{
    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction(){
        return $this->render('security/login.html.twig');
    }
    /**
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request){
        $authenticationUtils = $this->get('security.authentication_utils');
        $error = $authenticationUtils->getLastAuthenticationError();
        return $this->render('security/login.html.twig',[
            'error'=>$error
        ]);
    }
}
