<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Service\User as UserService;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user')]
final class UserController extends AbstractController
{

    public function __construct(
        private UserService $userService,
    ) {
    }

    #[Route(name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/_home.html.twig', [
            'user' => $this->getUser(),
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/list', name: 'app_user_list', methods: ['GET'])]
    public function list(UserRepository $userRepository): Response
    {
        if($this->userService->isAllowedToGo($this->getUser(),"USER_CONSULT") == false)
        {
            return $this->redirectToRoute('app_index');
        }

        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/hierarchical_view', name: 'app_user_hierarchical_view', methods: ['POST'])]
    public function hierarchicalView(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/permissions', name: 'app_user_permissions', methods: ['GET', 'POST'])]
    public function permission(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        
        $permissions = $this->userService->getPermissionsList($user);
        
        if ($request->isMethod('POST')) {
            $userpermission = explode(";",$user->getPermissions());
            if($request->getPayload()->getString('checked') == "true")
            {
                $userpermission[] = $request->getPayload()->getString('permission');
            }else{
                $permissionToRemove = $request->getPayload()->getString('permission');
                $userpermission = array_filter($userpermission, fn($permission) => $permission !== $permissionToRemove);
            }

            foreach($userpermission as $key => $permission)
            {
                if($permission == "")
                {
                    unset($userpermission[$key]);
                }
            }
            $user->setPermissions(implode(";",$userpermission));
            $entityManager->persist($user);
            $entityManager->flush();
        }

        return $this->render('user/permission.html.twig', [
            'user' => $user,
            'permissions' => $permissions
        ]);
    }
}
