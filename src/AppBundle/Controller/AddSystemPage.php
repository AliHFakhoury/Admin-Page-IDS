<?php
/**
 * Created by PhpStorm.
 * User: fakho
 * Date: 2/7/2018
 * Time: 11:09 PM
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Role;
use AppBundle\Entity\systemPages;
use Doctrine\DBAL\DBALException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class AddSystemPage extends Controller{
    /**
     * @Route("/Admin/ManageSystemPages/AddPage/{pageNumber}",name="AddSystemPage")
     */
    public function AddAdminAction(Request $request, $pageNumber){
        $session = new Session();

        $user = $this->getUser();

        $permissionRepo = $this->getDoctrine()->getRepository('AppBundle:Permission');
        $permissions = $permissionRepo->findByUserId($user->getAdminID())[0];

        $PAGE_ID = 14;

        if(!$this->checkRoles($PAGE_ID)){
            die("You can't access this page.");
        }


        if($permissions->getCanAdd() == 0){
            die("You cannot enter this page.");
        }
    
        $systemPagesRepo = $this->getDoctrine()->getRepository('AppBundle:systemPages');
        $systemPages = $systemPagesRepo->getSystemPagesForTable($user->getAdminID());
    
        $canDelete = $permissions->getCanDelete();
        $canEdit = $permissions->getCanEdit();
        $canView = $permissions->getCanView();
        $canAdd = $permissions->getCanAdd();

        $language = $session->get('locale');
        $request->setLocale($language);

        $form = $this->createFormAdmin();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $systemPage = new systemPages();

            $data = $form->getData();

            $systemPage->setPageName($data["pageName"]);
            $systemPage->setPageUrl($data["pageURL"]);
            $systemPage->setRoleId($data["role"]);
            $systemPage->setIsDeleted(0);

            $em->persist($systemPage);
    
            try{
                $em->flush();
            }catch ( DBALException  $e ){
                $technicalIssuesRepo = $this->getDoctrine()->getRepository('AppBundle:TechnicalIssue');
                $technicalIssuesRepo->addTechnicalIssue($user->getAdminId(), $systemPage->getId(), 'permissions', $e->getMessage(), $this->getIpAddress());
                die($e->getMessage());
            }
    
            $systemPageId = $systemPage->getId();
            
            $systemLogRepo = $this->getDoctrine()->getRepository('AppBundle:SystemLog');
            $systemLogRepo->addSystemLog($user->getAdminId(), $systemPageId, 'system_pages', 'Add',$this->getIpAddress());
    
            return $this->redirectToRoute('ManageSystemPages',[
                'pageNumber'=>$pageNumber,
                'canAdd' => $canAdd,
                'canView' => $canView,
                'canDelete' => $canDelete,
                'canEdit' => $canEdit,
                'userPages' => $systemPages,
            ]);
        }


        return $this->render('Admin/ManageSystemPages/AddSystemPage.html.twig',[
                'form'=> $form->createView(),
                'pageNumber'=>$pageNumber,
                'language'=>$language,
                'canAdd' => $canAdd,
                'canView' => $canView,
                'canDelete' => $canDelete,
                'canEdit' => $canEdit,
                'userPages' => $systemPages,
            ]
        );
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

    public function createFormAdmin(){
        $repositoryRoles = $this->getDoctrine()->getManager()->getRepository(Role::class);

        $roles = $repositoryRoles->findAll();

        $rolesDisplay = Array();

        foreach ($roles as $role){
            $rolesDisplay[$role->getRoleName()] = $role->getRoleId();
        }

        $form = $this->createFormBuilder()
            ->add('pageName',TextType::class)
            ->add('pageURL',TextType::class)
            ->add('role',ChoiceType::class,[
                'choices' => $rolesDisplay,
            ])
            ->add('Submit',SubmitType::class)
            ->getForm();

        return $form;
    }
}