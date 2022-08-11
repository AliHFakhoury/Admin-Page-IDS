<?php
/**
 * Created by PhpStorm.
 * User: fakho
 * Date: 12/31/2017
 * Time: 10:19 PM
 */

namespace AppBundle\Controller;

use AppBundle\Entity\admin;
use AppBundle\Entity\AdminCategories;
use AppBundle\Entity\Categories;
use AppBundle\Entity\Permission;
use AppBundle\Entity\Role;
use Doctrine\DBAL\DBALException;
use AppBundle\Entity\UsersRoles;
use AppBundle\Form\Type\AddAdminType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class AddAdminController extends Controller{
    /**
     * @Route("/Admin/ManageAdmins/AddAdmin/{pageNumber}",name="AddAdmins")
     */
    public function AddAdminAction(Request $request,$pageNumber){
        $session = new Session();

        $user = $this->getUser();

        $permissionRepo = $this->getDoctrine()->getRepository('AppBundle:Permission');
        $permissions = $permissionRepo->findByUserId($user->getAdminID())[0];

        $PAGE_ID = 1;

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

        $repositoryCategory = $this->getDoctrine()->getRepository('AppBundle:Categories');
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $data = $form->getData();

            //admin data
            $admin = new admin();
            $admin->setUsername($data['email']);
            $admin->setEmail($data['email']);
            $admin->setFirstName($data['firstName']);
            $admin->setLastName($data['lastName']);
            $admin->setPlainPassword($data['plainPassword']);
            $password=$this->get('security.password_encoder')->encodePassword($admin, $data['plainPassword']);
            $admin->setPassword($password);
            $admin->setEnabled(1);
            $admin->setIsDeleted(0);
            $admin->setTimeStamp($this->getDate());
            $em->persist($admin);
            
            try{
                $em->flush();
            }catch ( DBALException  $e ){
                $technicalIssuesRepo = $this->getDoctrine()->getRepository('AppBundle:TechnicalIssue');
                $technicalIssuesRepo->addTechnicalIssue($user->getAdminId(), $admin->getAdminID(), 'admin_users', $e->getMessage(), $this->getIpAddress());
                die($e->getMessage());
            }
            
            $adminId = $admin->getAdminID();
    
            $systemLogRepo = $this->getDoctrine()->getRepository('AppBundle:SystemLog');
            $systemLogRepo->addSystemLog($user->getAdminId(), $adminId, 'admin_users', 'Add',$this->getIpAddress());
            
            if(in_array(0, $data['role'])){
                //permission data
                $permission = new Permission();
                $permission->setUserId($adminId);
                $permission->setRoleId(0);
                $permission->setCanAdd($data['canAdd']);
                $permission->setCanEdit($data['canEdit']);
                $permission->setCanView($data['canView']);
                $permission->setCanDelete($data['canDelete']);

                $em->persist($permission);
                try{
                    $em->flush();
                }catch ( DBALException  $e ){
                    $technicalIssuesRepo = $this->getDoctrine()->getRepository('AppBundle:TechnicalIssue');
                    $technicalIssuesRepo->addTechnicalIssue($user->getAdminId(), $admin->getAdminID(), 'permissions', $e->getMessage(), $this->getIpAddress());
                    die($e->getMessage());
                }
    
                $adminRole = new UsersRoles();
                $adminRole->setUserId($adminId);
                $adminRole->setRoleId(0);
                $em->persist($adminRole);
    
                try{
                    $em->flush();
                }catch ( DBALException  $e ){
                    $technicalIssuesRepo = $this->getDoctrine()->getRepository('AppBundle:TechnicalIssue');
                    $technicalIssuesRepo->addTechnicalIssue($user->getAdminId(), $admin->getAdminID(), 'roles', $e->getMessage(), $this->getIpAddress());
                    die($e->getMessage());
                }
    
            }

            if(in_array(1, $data['role'])){
                $permission = new Permission();
                $permission->setRoleId(1);
                $permission->setUserId($adminId);
                $permission->setCanAdd($data['canAdd']);
                $permission->setCanEdit($data['canEdit']);
                $permission->setCanView($data['canView']);
                $permission->setCanDelete($data['canDelete']);
                $em->persist($permission);
    
                try{
                    $em->flush();
                }catch ( DBALException  $e ){
                    $technicalIssuesRepo = $this->getDoctrine()->getRepository('AppBundle:TechnicalIssue');
                    $technicalIssuesRepo->addTechnicalIssue($user->getAdminId(), $admin->getAdminID(), 'permissions', $e->getMessage(), $this->getIpAddress());
                    die($e->getMessage());
                }
    
                $adminRole = new UsersRoles();
                $adminRole->setUserId($adminId);
                $adminRole->setRoleId(1);

                $em->persist($adminRole);
    
                try{
                    $em->flush();
                }catch ( DBALException  $e ){
                    $technicalIssuesRepo = $this->getDoctrine()->getRepository('AppBundle:TechnicalIssue');
                    $technicalIssuesRepo->addTechnicalIssue($user->getAdminId(), $admin->getAdminID(), 'permissions', $e->getMessage(), $this->getIpAddress());
                    die($e->getMessage());
                }
            }
            
            if(in_array(0, $data["role"])){
                $categories = $repositoryCategory->getParentCategoriesForAdmin();
            }else{
                $categories = implode(",", $data['categories']);
            }

            for($i = 0; $i < count($categories); $i++){
                $category = new AdminCategories();
                $category->setUserId($adminId);
                $category->setCategoryId($categories[$i]);
                $em->persist($category);
    
                try{
                    $em->flush();
                }catch ( DBALException  $e ){
                    $technicalIssuesRepo = $this->getDoctrine()->getRepository('AppBundle:TechnicalIssue');
                    $technicalIssuesRepo->addTechnicalIssue($user->getAdminId(), $admin->getAdminID(), 'categories', $e->getMessage(), $this->getIpAddress());
                    die($e->getMessage());
                }
            }

            return $this->redirectToRoute('ManageAdmins',array(
                'language' => $language,
                'pageNumber'=>$pageNumber,
                'canAdd' => $canAdd,
                'canView' => $canView,
                'canDelete' => $canDelete,
                'canEdit' => $canEdit,
                'userPages' => $systemPages,
            ));
        }

        return $this->render('Admin/ManageAdmins/AddAdmin.html.twig',[
                'form'=> $form->createView(),
                'language' => $language,
                'pageNumber'=>$pageNumber,
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
        $repositoryCategory = $this->getDoctrine()->getManager()->getRepository(Categories::class);
        $repoRoles = $this->getDoctrine()->getManager()->getRepository(Role::class);

        $categories = $repositoryCategory->getAllCategoriesAdmin();
        $roles = $repoRoles->findAll();

        $categoriesDisplay = Array();
        $rolesDisplay = Array();

        foreach ($categories as $category){
            $categoriesDisplay[$category["category_name"]] = $category["category_id"];
        }


        foreach ($roles as $role){
            $rolesDisplay[$role->getRoleName()] = $role->getRoleId();
        }

        $form = $this->createFormBuilder()
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('email', EmailType::class)
            ->add('plainPassword', RepeatedType::class,[
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => 'Password',
                ],
                'second_options' => [
                    'label' => 'Repeat Password'
                ]
            ])
            ->add('categories', ChoiceType::class,[
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'choices' => $categoriesDisplay,
                'attr' => [
                    'class' => 'checkbox-group'
                ]
            ])

            ->add('role', ChoiceType::class, [
                'multiple' => true,
                'expanded' => true,
                'choices' => $rolesDisplay,

            ])
            ->add('canEdit', CheckboxType::class, [
                'required' => false
            ])
            ->add('canView', CheckboxType::class, [
                'required' => false,
                'data' => true,
            ])
            ->add('canDelete', CheckboxType::class, [
                'required' => false
            ])
            ->add('canAdd', CheckboxType::class, [
                'required' => false
            ])
            ->add('Submit',SubmitType::class)
            ->add('Clear', ResetType::class)
            ->getForm();

        return $form;
    }

    public function getDate(){
        return new \DateTime('now', (new \DateTimeZone('Asia/Beirut')));
    }
}