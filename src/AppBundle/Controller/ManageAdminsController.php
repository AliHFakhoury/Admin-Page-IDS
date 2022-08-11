<?php

namespace AppBundle\Controller;

namespace AppBundle\Controller;
use AppBundle\Entity\Categories;
use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use AppBundle\Entity\admin;
use AppBundle\Form\Type\AddAdminType;
use AppBundle\Form\Type\AdminType;
use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class ManageAdminsController extends Controller{
    /**
     * @Route("/Admin/ManageAdmins/{pageNumber}",name="ManageAdmins")
     */
    public function ManageAdminAction(Request $request, $pageNumber){
        $session = new Session();
        $PAGE_ID = 1;

        $user = $this->getUser();

        $permissionRepo = $this->getDoctrine()->getRepository('AppBundle:Permission');
        $permissions = $permissionRepo->findByUserId($user->getAdminID())[0];

        //checking for role
        if(!$this->checkRoles($PAGE_ID, $permissions)){
            die("You can't access this page.");
        }

        $session->set('data', null);

        $canDelete = $permissions->getCanDelete();
        $canEdit = $permissions->getCanEdit();
        $canView = $permissions->getCanView();
        $canAdd = $permissions->getCanAdd();

        $categories = $session->get('categories');

        $request->setLocale($session->get('locale'));
        $language = $session->get('locale');
        
        $form=$this->formCreate($categories);
        $repo = $this->getDoctrine()->getRepository('AppBundle:admin');

        $form->handleRequest($request);

        $values = $session->get('data');
        $admins = $repo->findAllCriteria($values, $pageNumber, 10);
    
        $systemPagesRepo = $this->getDoctrine()->getRepository('AppBundle:systemPages');
        $systemPages = $systemPagesRepo->getSystemPagesForTable($user->getAdminID());
    
        if($form->isSubmitted() && $form->isValid()){
            $values = $form->getData(); //Form data from the user
            $admins = $repo->findAllCriteria($values,$pageNumber, 10);  //cannot find number of after search.
            $session->set('data', $values);

            if($form->get('reset_form')->isClicked()){
                $session->set('data', null);
                return  $this->redirectToRoute('ManageAdmins',array(
                    'pageNumber'=>$pageNumber,
                    'language'=>$language,
                    'canAdd' => $canAdd,
                    'canView' => $canView,
                    'canDelete' => $canDelete,
                    'canEdit' => $canEdit,
                ));
            }
        }

        $countPages = $repo->countPages($values, 10);

        return $this->render('Admin/ManageAdmins/ManageAdmins.html.twig',[
            'form'=> $form->createView(),
            'numPages' =>$countPages,
            'pageNumber'=>$pageNumber,
            'Admins'=>$admins,
            'language' => $language,
            'canAdd' => $canAdd,
            'canView' => $canView,
            'canDelete' => $canDelete,
            'canEdit' => $canEdit,
            'userPages' => $systemPages,
        ]);
    }

    /**
     * @Route("/Admin/ManageAdmins/Update/{id}/{pageNumber}",name="UpdateAdminStatus")
     */
    public function UpdateEnabled(Request $request, $pageNumber, $id){
        $user = $this->getUser();

        $permissionRepo = $this->getDoctrine()->getRepository('AppBundle:Permission');
        $permissions = $permissionRepo->findByUserId($user->getAdminID())[0];

        if($permissions->getCanEdit() == 0){
            die("You cannot enter this page.");
        }

        $em =$this->getDoctrine()->getManager();

        $admin = $em->getRepository('AppBundle:admin')->find($id);
        
        if(!$admin){
            $technicalIssuesRepo = $this->getDoctrine()->getRepository('AppBundle:TechnicalIssue');
            $technicalIssuesRepo->addTechnicalIssue($user->getAdminId(), 'multiple', 'events', "Admin not found.", $this->getIpAddress());
            return $this->redirectToRoute('ManageAdmins');
        }
        /** @var $post User */
        if($admin->getEnabled()== 0){
            $admin->setEnabled(1);
        }
        else if($admin->getEnabled()==1){
            $admin->setEnabled(0);
        }
        $em->flush();
    
        $systemLogRepo = $this->getDoctrine()->getRepository('AppBundle:SystemLog');
        $systemLogRepo->addSystemLog($user->getAdminId(), $id, 'admin_users', 'Update',$this->getIpAddress());
    
        return $this->redirectToRoute('ManageAdmins',array(
           'pageNumber'=>$pageNumber,
            'categoryID' => 12,
        ));
    }

    /**
     * @return \DateTime
     */
    public function getDaate()
    {
        return new \DateTime('now', (new \DateTimeZone('Asia/Beirut')));
    }
    
    public function formCreate($data){
        $repositoryCategory = $this->getDoctrine()->getManager()->getRepository(Categories::class);
        $repositoryRole = $this->getDoctrine()->getManager()->getRepository(Role::class);

        $categories = $repositoryCategory->getParentCategories();
        $roles = $repositoryRole->findAll();

        $categoryArray = array();
        $roleArray = array();

        foreach($categories as $value){
            $categoryArray[$value->getCategoryName()] = $value->getCategoryID();
        }

        foreach($roles as $value){
            $roleArray[$value->getRoleName()] = $value->getRoleId();
        }

        $session = new Session();

        if($session->get('locale') === 'ar'){
            $labelButton = "إخلاء";
        }else{
            $labelButton = "Reset";
        }

        $form = $this->createFormBuilder()
            ->add('firstName', TextType::class, [
                'required'  => false,
            ])
            ->add('lastName', TextType::class, [
                'required'  => false,
            ])
            ->add('role', ChoiceType::class, [
                'required'  => false,
                'choices' => $roleArray,
            ])
            ->add('category', ChoiceType::class, [
                'required'  => false,
                'choices' => $categoryArray,
            ])
            ->add('timestamp', DateType::class,  [
                'required'  => false,
                'widget' => 'single_text',
                'html5' => false,
                'attr' =>['class' => 'js-datepicker']
            ])
            ->add('Search', SubmitType::class)
            ->add('reset_form',SubmitType::class, [
                'label' => $labelButton,
            ])
            ->getForm();

        return $form;
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
