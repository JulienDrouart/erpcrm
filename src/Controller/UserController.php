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

    /**
     * @Route(name="app_user_index", methods={"GET"})
     *
     * Displays the user index page.
     *
     * This method renders the user index page, which includes the current user
     * and a list of all users retrieved from the UserRepository.
     *
     * @param UserRepository $userRepository The repository to fetch user data.
     *
     * @return Response The response object containing the rendered view.
     */
    #[Route(name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/_home.html.twig', [
            'user' => $this->getUser(),
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/list", name="app_user_list", methods={"GET"})
     *
     * Displays a list of users.
     *
     * This method checks if the current user has the "USER_CONSULT" permission.
     * If the user does not have the permission, they are redirected to the index page.
     * If the user has the permission, it renders the user list page.
     *
     * @param UserRepository $userRepository The repository to fetch user data.
     * @return Response The HTTP response object.
     */
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

    /**
     * Creates a new User entity.
     *
     * This method handles the creation of a new User entity. It checks if the 
     * current user has the necessary permissions to create or update a user. 
     * If not, it redirects to the index page. Otherwise, it creates a form 
     * for the User entity, handles the request, and if the form is submitted 
     * and valid, it persists the new User entity to the database and redirects 
     * to the user index page.
     *
     * @param Request $request The current request instance.
     * @param EntityManagerInterface $entityManager The entity manager instance.
     *
     * @return Response The response instance.
     *
     * @Route("/new", name="app_user_new", methods={"GET", "POST"})
     */
    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if($this->userService->isAllowedToGo($this->getUser(),"USER_CREATE_UPDATE") == false)
        {
            return $this->redirectToRoute('app_index');
        }
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

    /**
     * Displays a user.
     *
     * This route is accessed via a GET request to /{id}.
     * If the current user does not have permission to consult users,
     * they will be redirected to the index page.
     *
     * @param User $user The user entity to display.
     * 
     * @return Response The response object containing the rendered user details or a redirection.
     */
    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        if($this->userService->isAllowedToGo($this->getUser(),"USER_CONSULT") == false)
        {
            return $this->redirectToRoute('app_index');
        }

        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * Edit an existing user.
     *
     * This method handles the editing of a user entity. It checks if the current user has the necessary permissions
     * to perform the edit operation. If the user does not have the required permissions, they are redirected to the index page.
     * If the user has the required permissions, a form is created and handled. If the form is submitted and valid,
     * the changes are flushed to the database and the user is redirected to the user index page.
     *
     * @Route("/{id}/edit", name="app_user_edit", methods={"GET", "POST"})
     *
     * @param Request $request The current request instance.
     * @param User $user The user entity to be edited.
     * @param EntityManagerInterface $entityManager The entity manager to handle database operations.
     *
     * @return Response The response instance.
     */
    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if($this->userService->isAllowedToGo($this->getUser(),"USER_CREATE_UPDATE") == false)
        {
            return $this->redirectToRoute('app_index');
        }

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

    /**
     * Deletes a user entity.
     *
     * This route is accessed via a POST request to the URL pattern '/{id}'.
     * The route name is 'app_user_delete'.
     *
     * @param Request $request The HTTP request object.
     * @param User $user The user entity to be deleted.
     * @param EntityManagerInterface $entityManager The entity manager for handling database operations.
     *
     * @return Response A redirect response to the user index page or the index page if the user is not allowed to delete.
     *
     * @Route("/{id}", name="app_user_delete", methods={"POST"})
     */
    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if($this->userService->isAllowedToGo($this->getUser(),"USER_DELETE") == false)
        {
            return $this->redirectToRoute('app_index');
        }

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
    /**
     * Handles the hierarchical view for users.
     *
     * This route is accessed via a POST request to '/hierarchical_view'.
     * It checks if the current user has permission to consult user data.
     * If not, it redirects to the index page.
     * If the user has permission, it redirects to the user index page.
     *
     * @param Request $request The current request object.
     * @param User $user The user entity.
     * @param EntityManagerInterface $entityManager The entity manager interface.
     *
     * @return Response The HTTP response object.
     */

    #[Route('/hierarchical_view', name: 'app_user_hierarchical_view', methods: ['POST'])]
    public function hierarchicalView(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if($this->userService->isAllowedToGo($this->getUser(),"USER_CONSULT") == false)
        {
            return $this->redirectToRoute('app_index');
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}/permissions", name="app_user_permissions", methods={"GET", "POST"})
     *
     * Handles the user permissions page.
     *
     * This method checks if the current user has the necessary permissions to access the user permissions page.
     * If the user does not have the required permissions, they are redirected to the index page.
     * If the request method is POST, it updates the user's permissions based on the submitted data.
     *
     * @param Request $request The HTTP request object.
     * @param User $user The user entity whose permissions are being managed.
     * @param EntityManagerInterface $entityManager The entity manager for persisting changes.
     *
     * @return Response The HTTP response object.
     */
    #[Route('/{id}/permissions', name: 'app_user_permissions', methods: ['GET', 'POST'])]
    public function permission(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if($this->userService->isAllowedToGo($this->getUser(),"USER_PERMISSIONS") == false)
        {
            return $this->redirectToRoute('app_index');
        }
        
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
