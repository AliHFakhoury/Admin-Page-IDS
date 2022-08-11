<?php

namespace AppBundle\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ViewTech_IssuesController extends Controller{
    //TECHNICAL ISSUES
    /**
     * @Route("/Admin/ViewTechIssues/{pageNumber}",name="ViewTechIssues")
     */
    public function ViewTechIssuesAction(Request $request, $pageNumber){
        $session = new Session();
        $user = $this->getUser();

        $PAGE_ID = 7;

        if(!$this->checkRoles($PAGE_ID)){
            die("You can't access this page.");
        }
        
        $language = $session->get('locale');
        
        $repo = $this->getDoctrine()->getRepository('AppBundle:TechnicalIssue');
        $request->setLocale($language);
        
        $userRole = $session->get('userRole');
        
        $countPages = $repo->countPages(10);
        
        $technicalIssues = $repo->findAllByPage(10,$pageNumber);
        
        $systemPagesRepo = $this->getDoctrine()->getRepository('AppBundle:systemPages');
        $systemPages = $systemPagesRepo->getSystemPagesForTable($user->getAdminID());
        
        return $this->render('Admin/TechnicalIssues/ViewTech_Issues.html.twig',[
            'technicalIssues'=>  $technicalIssues,
            'language' => $language,
            'userRole' => $userRole,
            'pageNumber' => $pageNumber,
            'numPages' => $countPages,
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