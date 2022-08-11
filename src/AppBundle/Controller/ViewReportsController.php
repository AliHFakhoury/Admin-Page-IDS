<?php

namespace AppBundle\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ViewReportsController extends Controller
{

    /**
     * @Route("/Admin/ViewReports/{pageNumber}",name="ViewReports")
     */
    public function ViewReportsAction(Request $request, $pageNumber){
        $session = new Session();

        $PAGE_ID = 13;
        $user = $this->getUser();
        
        if(!$this->checkRoles($PAGE_ID)){
            die("You can't access this page.");
        }

        $repo = $this->getDoctrine()->getRepository('AppBundle:Report');
        $viewReports = $repo->findAllByPage(10, $pageNumber);
        $numberOfPages = $repo->countPages(10);
    
        $systemPagesRepo = $this->getDoctrine()->getRepository('AppBundle:systemPages');
        $systemPages = $systemPagesRepo->getSystemPagesForTable($user->getAdminID());
    
        $language = $session->get('locale');
        $request->setLocale($language);
        $userRole = $session->get('userRole');

        return $this->render('Admin/ManageReports/ViewReports.html.twig',[
            'language' => $language,
            'ViewReports'=> $viewReports,
            'userRole' => $userRole,
            'numPages' => $numberOfPages,
            'pageNumber' => $pageNumber,
            'userPages' => $systemPages,
        ]);
    }

    public function checkRoles($pageId){
        $repository = $this->getDoctrine()->getRepository('AppBundle:systemPages');
        $repositoryRoles = $this->getDoctrine()->getRepository('AppBundle:UsersRoles');
        $repositoryPermissions = $this->getDoctrine()->getRepository('AppBundle:Permission');

        $userId = $this->getUser()->getAdminId();

        $roleOfPage = $repository->findRolesByPageId($pageId);
        $rolesOfUser = $repositoryRoles->getRolesById($userId);

        return in_array($roleOfPage['role_id'], $rolesOfUser, false) && $repositoryPermissions->findByUserRoleId($userId, $roleOfPage);
    }
}