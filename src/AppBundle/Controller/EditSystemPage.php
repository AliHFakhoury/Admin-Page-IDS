<?php
/**
 * Created by PhpStorm.
 * User: fakho
 * Date: 2/7/2018
 * Time: 6:54 PM
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Role;
use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class EditSystemPage extends  Controller{
    /**
     * @Route("/Admin/ManageSystemPages/EditPage/{id}/{pageNumber}",name="EditSystemPage")
     */
    public function EditSystemPageAction(Request $request,$id, $pageNumber){
        $session = new Session();

        $user = $this->getUser();

        $permissionRepo = $this->getDoctrine()->getRepository('AppBundle:Permission');
        $permissions = $permissionRepo->findByUserId($user->getAdminID())[0];

        $PAGE_ID = 8;

        if(!$this->checkRoles($PAGE_ID)){
            die("You can't access this page.");
        }

        if($permissions->getCanEdit() == 0){
            die("You cannot enter this page.");
        }
        
        $canDelete = $permissions->getCanDelete();
        $canEdit = $permissions->getCanEdit();
        $canView = $permissions->getCanView();
        $canAdd = $permissions->getCanAdd();
    
        $systemPagesRepo = $this->getDoctrine()->getRepository('AppBundle:systemPages');
        $userPages = $systemPagesRepo->getSystemPagesForTable($user->getAdminID());
    
        $language = $session->get('locale');
        $request->setLocale($language);
        
        $form = $this->createFormAdmin($id);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $systemPage = $em->getRepository('AppBundle:systemPages')->find($id);

            $data = $form->getData();

            $systemPage->setPageName($data["pageName"]);
            $systemPage->setPageUrl($data["pageURL"]);
            $systemPage->setRoleId($data["role"]);
    
            try{
                $em->flush();
            }catch ( DBALException  $e ){
                die($e->getMessage());
            }
            
            $systemLogRepo = $this->getDoctrine()->getRepository('AppBundle:SystemLog');
            $systemLogRepo->addSystemLog($user->getAdminId(), $id, 'SystemPages', 'Edit',$this->getIpAddress());
            
            return $this->render('Admin/ManageSystemPages/EditSystemPage.html.twig',[
                'form'=> $form->createView(),
                'pageNumber'=>$pageNumber,
                'canAdd' => $canAdd,
                'canView' => $canView,
                'canDelete' => $canDelete,
                'canEdit' => $canEdit,
                'userPages' => $userPages,
            ]);
        }

        return $this->render('Admin/ManageSystemPages/EditSystemPage.html.twig',[
                'form'=> $form->createView(),
                'pageNumber'=>$pageNumber,
                'language'=>$language,
                'canAdd' => $canAdd,
                'canView' => $canView,
                'canDelete' => $canDelete,
                'canEdit' => $canEdit,
                'userPages' => $userPages,
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

    public function createFormAdmin($id){
        $repositoryRoles = $this->getDoctrine()->getManager()->getRepository(Role::class);

        $roles = $repositoryRoles->findAll();

        $rolesDisplay = Array();

        foreach ($roles as $role){
            $rolesDisplay[$role->getRoleName()] = $role->getRoleId();
        }


        $em = $this->getDoctrine()->getManager();
        $systemPage = $em->getRepository('AppBundle:systemPages')->find($id);


        $form = $this->createFormBuilder()
            ->add('pageName',TextType::class,[
                'data' => $systemPage->getPageName(),
            ])
            ->add('pageURL',TextType::class, [
                'data' => $systemPage->getPageUrl(),
            ])
            ->add('role',ChoiceType::class,[
                'choices' => $rolesDisplay,
                'data' => $systemPage->getRoleId(),
            ])
            ->add('Submit',SubmitType::class)
            ->getForm();

        return $form;
    }
}