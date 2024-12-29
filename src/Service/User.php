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

    /**
     * Retrieves the list of permissions for the user.
     *
     * @return array An array of permissions associated with the user.
     */
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
            ),
            "Services" => array(
            array(
                "slug" => "SERVICE_CONSULT",
                "label" => "Consulter les services"
            ),
            array(
                "slug" => "SERVICE_CREATE_UPDATE",
                "label" => "Créer/modifier les services"
            ),
            array(
                "slug" => "SERVICE_DELETE",
                "label" => "Supprimer les services"
            ),
            array(
                "slug" => "SERVICE_EXPORT",
                "label" => "Exporter les services"
            )
            ),
            "Tiers" => array(
                array(
                    "slug" => "TIER_CONSULT",
                    "label" => "Consulter les tiers (sociétés) liés à l'utilisateur"
                ),
                array(
                    "slug" => "TIER_CREATE_UPDATE",
                    "label" => "Créer/modifier les tiers (sociétés) liés à l'utilisateur"
                ),
                array(
                    "slug" => "TIER_DELETE",
                    "label" => "Supprimer les tiers (sociétés) liés à l'utilisateur"
                ),
                array(
                    "slug" => "TIER_EXPORT",
                    "label" => "Exporter les tiers (sociétés)"
                ),
                array(
                    "slug" => "CONTACT_CONSULT",
                    "label" => "Consulter les contacts"
                ),
                array(
                    "slug" => "CONTACT_CREATE_UPDATE",
                    "label" => "Créer/modifier les contacts"
                ),
                array(
                    "slug" => "CONTACT_DELETE",
                    "label" => "Supprimer les contacts"
                ),
                array(
                    "slug" => "CONTACT_EXPORT",
                    "label" => "Exporter les contacts"
                )
            )
        ,
        "Commandes" => array(
            array(
                "slug" => "ORDER_CONSULT",
                "label" => "Consulter les commandes clients"
            ),
            array(
                "slug" => "ORDER_CREATE_UPDATE",
                "label" => "Créer/modifier les commandes clients"
            ),
            array(
                "slug" => "ORDER_DELETE",
                "label" => "Supprimer les commandes clients"
            ),
            array(
                "slug" => "ORDER_EXPORT",
                "label" => "Exporter les commandes clients et attributs"
            )
        )
        ,
        "Factures et avoirs" => array(
            array(
                "slug" => "INVOICE_CONSULT",
                "label" => "Lire les factures (et paiements) clients"
            ),
            array(
                "slug" => "INVOICE_CREATE_UPDATE",
                "label" => "Créer/modifier les factures clients"
            ),
            array(
                "slug" => "INVOICE_PAYMENT",
                "label" => "Émettre des paiements sur les factures clients"
            ),
            array(
                "slug" => "INVOICE_DELETE",
                "label" => "Supprimer les factures clients"
            ),
            array(
                "slug" => "INVOICE_EXPORT",
                "label" => "Exporter les factures clients, attributs et règlements"
            )
        )
        );

        return  $array;
    }

    /**
     * Checks if the user has the specified permission or is an admin.
     *
     * @param User $user The user object containing permissions and roles.
     * @param string $permission The permission to check.
     * @return bool Returns true if the user has the specified permission or is an admin, false otherwise.
     */
    public function isAllowedToGo($user,$permission)
    {
        $session = $this->requestStack->getSession();
        $userPermissions = explode(";",$user->getPermissions());
        if(in_array($permission,$userPermissions) || in_array("ROLE_ADMIN",$user->getRoles()))
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