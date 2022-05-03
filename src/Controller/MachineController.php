<?php

namespace App\Controller;

use App\Entity\Machine;
use App\Entity\User;
use App\Repository\MachineRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Patch;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Controller\Annotations as Rest;

class MachineController extends AbstractFOSRestController
{
    private $entityManager;
    private $userRepository;
    private $machineRepository;

    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository, MachineRepository $machineRepository)
    {
        $this->entityManager = $entityManager;
        $this->machineRepository = $machineRepository;
        $this->userRepository = $userRepository;
    }
    /**
     * @Post("/machines", name="app_machine_new")
     * @Rest\RequestParam(name="name",description="nom de la machine")
     * @Rest\RequestParam(name="description", description="description machine")
     * @Rest\RequestParam(name="utilisateur", description="utilisateur machine")
     */
    public function postMachine(ParamFetcher $paramFetcher): View
    {
        $machine = new Machine();
        $description = $paramFetcher->get('description');

        $name = $paramFetcher->get('name');
        if (empty($description) || empty($name)) {
            throw new NotFoundHttpException('Certains paramètres sont vides');
        }
        /** @var $user User */
        $user = $this->getUser();

        $machine->setUtilisateur($user);
        $machine->setName($name);
        $machine->setDescription($description);

        $this->entityManager->persist($machine);
        $this->entityManager->flush();

        return $this->view(["Message :"=> "Machine crée"], Response::HTTP_OK);

    }

    /**
     * @Get("/machines/{id}", name="app_machine_show", methods={"GET"})
     */
    public function getMachine(Request $request): View
    {
        $machine = $this->machineRepository->findOneBy(['id'=>$request->get('id')]);

        if (!$machine)
        {
            return $this->view(["Message :"=> "Une erreur est survenue"], Response::HTTP_NOT_FOUND);
        }
        if ($machine->getUtilisateur() !=  $this->getUser())
        {
            return $this->view(["Message :"=> "Vous n'avez pas l'autorisation d'effectuer cette operation"], Response::HTTP_UNAUTHORIZED);
        }
        $view = $this->view(["Machine :"=> $machine], Response::HTTP_OK);
        $view->setContext($view->getContext()->setGroups(['public']));
        return $view;
    }

    /**
     * @Patch("/machines/{id}", name="app_machine_edit")
     * @ParamConverter("machine", options={"mapping": {"id":"id"}})
     * @Rest\RequestParam(name="name",description="nom de la machine")
     * @Rest\RequestParam(name="description", description="description machine")
     * @Rest\RequestParam(name="utilisateur", description="utilisateur machine")
     */
    public function patchMachine(ParamFetcher $paramFetcher, Machine $machine): View
    {
        if (!$machine)
        {
            return $this->view(["Message :"=> "Une erreur est survenue"], Response::HTTP_NOT_FOUND);
        }
        if ($machine->getUtilisateur() != $this->getUser())
        {
            return $this->view(["Message :"=> "Vous n'avez pas l'autorisation d'effectuer cette operation"], Response::HTTP_UNAUTHORIZED);
        }
        /** @var $user User */
        $user = $this->getUser();

        $description = $paramFetcher->get('description');
        $name = $paramFetcher->get('name');
        $newMachine = $this->machineRepository->find($machine);
        $newMachine->setName($name);
        $newMachine->setDescription($description);
        $newMachine->setUtilisateur($user);

        $this->entityManager->flush();
        return $this->view(["Message :"=> "Machine modifiee"], Response::HTTP_OK);
    }

    /**
     * @Delete("/machines/{id}", name="app_machine_delete")
     * @ParamConverter("machine", options={"mapping": {"id":"id"}})
     *
     */
    public function deleteMachine(Request $request, Machine $machine, MachineRepository $machineRepository): View
    {
        if (!$machine)
        {
            return $this->view(["Message :"=> "Une erreur est survenue"], Response::HTTP_NOT_FOUND);
        }
        if ($machine->getUtilisateur() != $this->getUser())
        {
            return $this->view(["Message :"=> "Vous n'avez pas l'autorisation d'effectuer cette operation"], Response::HTTP_UNAUTHORIZED);
        }
        $machineRepository->remove($machine);
        return $this->view(["Message :"=> "Machine supprimee"], Response::HTTP_OK);
    }

    /**
     * @Get("/machines", name="app_machine_getAll")
     */
    public function getMachines(): View
    {
        $machines = $this->machineRepository->findBy(['utilisateur'=>$this->getUser()]);
        $view = $this->view(["Machines :"=> $machines], Response::HTTP_OK);
        $view->setContext($view->getContext()->setGroups(['public']));
        return $view;
    }
}
