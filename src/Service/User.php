<?php

namespace App\Service;

use App\Entity\Series;
use App\Form\SeriesType;
use App\Repository\SeriesRepository;
use App\Repository\WeightRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class User
{

    private RequestStack $requestStack;
    private RouterInterface $router;

    public function __construct(RequestStack $requestStack, RouterInterface $router)
    {
        $this->requestStack = $requestStack;
        $this->router = $router;
    }

    public function getPermissionsList()
    {
        $array = array(
            "Utilisateurs" => array(
                array(
                    "slug" => "USER_CONSULT",
                    "label" => "Consulter les utilisateur"
                ),
                array(
                    "slug" => "USER_CREATE_UPDATE",
                    "label" => "Créer / modifier les utilisateurs"
                ),
                array(
                    "slug" => "USER_UPDATE_PWD",
                    "label" => "Modifier les mots de passe"
                ),
                array(
                    "slug" => "USER_DELETE",
                    "label" => "Supprimer un utilisateur"
                ),
                array(
                    "slug" => "USER_EXPORT",
                    "label" => "Exporter un/des utilisateur"
                ),
                array(
                    "slug" => "USER_PERMISSIONS",
                    "label" => "Gérer les permissions"
                )

            )
        );

        return  $array;
    }

    public function isAllowedToGo($user,$permission)
    {
        $session = $this->requestStack->getSession();
        $userPermissions = explode(";",$user->getPermissions());
        if(in_array($permission,$userPermissions))
        {
            return true;
        }
        else
        {
            $session->getFlashBag()->add('danger', 'Accès à la page non autorisé');
            return false;
        }
    }

   
}