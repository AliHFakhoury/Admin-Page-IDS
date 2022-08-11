<?php

namespace AppBundle\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ViewSystemLogsController extends Controller
{
    /**
     * @Route("/Admin/ViewSystemLog/{pageNumber}",name="ViewSystemLog")
     */
    public function ViewSystemLogAction(Request $request, $pageNumber){
        $session = new Session();
        $user = $this->getUser();

        $PAGE_ID = 6;

        if(!$this->checkRoles($PAGE_ID)){
            die("You can't access this page.");
        }

        $language = $session->get('locale');

        $repo = $this->getDoctrine()->getRepository('AppBundle:SystemLog');
        $numberOfPages = $repo->countPages(10);
        $viewLogs = $repo->findAllByPage(10, $pageNumber);

        $request->setLocale($language);
    
        $systemPagesRepo = $this->getDoctrine()->getRepository('AppBundle:systemPages');
        $systemPages = $systemPagesRepo->getSystemPagesForTable($user->getAdminID());
        dump($viewLogs);
        $userRole = $session->get('userRole');
        return $this->render('Admin/SystemLog/ViewSystemLog.html.twig', [
            'language' => $language,
            'userRole' => $userRole,
            'viewLogs' => $viewLogs,
            'pageNumber' =>$pageNumber,
            'numPages' => $numberOfPages,
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
