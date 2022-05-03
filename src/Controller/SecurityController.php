<?php

namespace App\Controller;

use App\Entity\User;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractFOSRestController
{

    /**
     * @Route(name="api_login", path="/api/login_check")
     * @return JsonResponse
     */
    public function api_login(): JsonResponse
    {
        $user = $this->getUser();

        return new JsonResponse([
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
        ]);
    }

    /**
     * @Route("/register", name="app_register")
     * @Rest\RequestParam(name="email",description="email User")
     * @Rest\RequestParam(name="password", description="mot de passe utilisateur")
     *
     */
    public function register(ParamFetcher $paramFetcher, UserPasswordEncoderInterface $passwordEncoder) : View
    {
        $user = new User();
        $user->setEmail($paramFetcher->get('email'));
        $user->setPassword(
            $passwordEncoder->encodePassword(
                $user,
                $paramFetcher->get('password')
            )
        );
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->view(["Message :"=> "Utilisateur cree"], Response::HTTP_CREATED);
    }
}
