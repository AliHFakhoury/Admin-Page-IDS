<?php
/**
 * Created by PhpStorm.
 * User: fakho
 * Date: 2/6/2018
 * Time: 8:38 PM
 */

namespace AppBundle\Controller;


use Doctrine\DBAL\DBALException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class ManageSystemPages extends Controller {
    /**
     * @Route("Admin/ManageSystemPages/{pageNumber}", name="ManageSystemPages")
     */
    public function manageSystemAction(Request $request){
        $session = new Session();

        $PAGE_ID = 8;
        $user = $this->getUser();

        $permissionRepo = $this->getDoctrine()->getRepository('AppBundle:Permission');
        $permissions = $permissionRepo->findByUserId($user->getAdminID())[0];

        //checking for role
        if(!$this->checkRoles($PAGE_ID)){
            die("You can't access this page.");
        }

        $systemPagesRepo = $this->getDoctrine()->getRepository('AppBundle:systemPages');
        $systemPagesTable = $systemPagesRepo->getSystemPagesForTable($user->getAdminID());

        $canDelete = $permissions->getCanDelete();
        $canEdit = $permissions->getCanEdit();
        $canView = $permissions->getCanView();
        $canAdd = $permissions->getCanAdd();

        $systemPages = $systemPagesTable;

        $request->setLocale($session->get('locale'));
        
        return $this->render('Admin/ManageSystemPages/ManageSystemPages.html.twig', [
            'pageNumber' => 1,
            'numPages' => 1,
            'userPages' => $systemPages,
            'pages' => $systemPagesTable,
            'canAdd' => $canAdd,
            'canView' => $canView,
            'canDelete' => $canDelete,
            'canEdit' => $canEdit,
        ]);
    }

    /**
     * @Route("/Admin/ManageSystemPages/delete/{id}/{pageNumber}" , name="DeleteSystemPage")
     */
    public function DeleteSystemPage(Request $request,$id,$pageNumber){
        $session = new Session();

        $user = $this->getUser();

        $permissionRepo = $this->getDoctrine()->getRepository('AppBundle:Permission');
        $permissions = $permissionRepo->findByUserId($user->getAdminID())[0];

        if($permissions->getCanDelete() == 0){
            die("You cannot enter this page.");
        }

        $em = $this->getDoctrine()->getManager();
        $DeletePage = $em->getRepository('AppBundle:systemPages')->find($id);
        
        if (!$DeletePage) {
            $technicalIssuesRepo = $this->getDoctrine()->getRepository('AppBundle:TechnicalIssue');
            $technicalIssuesRepo->addTechnicalIssue($user->getAdminId(), 'unknown', 'system_pages', "System Page not found.", $this->getIpAddress());
            
            return $this->redirectToRoute('ManageSystemPages',array(
                'pageNumber'=>$pageNumber,
            ));
        }
        
        $DeletePage->setIsDeleted(1);
        $em->flush();
    
        $systemLogRepo = $this->getDoctrine()->getRepository('AppBundle:SystemLog');
        $systemLogRepo->addSystemLog($user->getAdminId(), $id, 'System Pages', 'Delete',$this->getIpAddress());
    
        return $this->redirectToRoute('ManageSystemPages',array(
            'pageNumber'=>$pageNumber,
        ));
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