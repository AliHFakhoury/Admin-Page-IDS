<?php

namespace AppBundle\Controller;
use AppBundle\Entity\Categories;
use AppBundle\Entity\Country;
use AppBundle\Entity\systemLogRepository;
use AppBundle\Entity\User;
use AppBundle\Entity\UserStatus;
use AppBundle\Form\Type\ManageUserSearchType;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ManageUsersController extends Controller{

    /**
     * @Route("/Admin/ManageUsers/{language}/{pageNumber}", name="Users")
     */
    public function AdminAction(Request $request, $language, $pageNumber){
        $session =   new Session();
        $PAGE_ID = 4;
        $user = $this->getUser();
        $userID = $user->getAdminID();

        $permissionRepo = $this->getDoctrine()->getRepository('AppBundle:Permission');

        //checking for role
        if(!$this->checkRoles($PAGE_ID, $userID)){
            die("You can't access this page.");
        }

        $userPermissions = $permissionRepo->findByUserId($user->getAdminID());

        $canDelete = $userPermissions[0]->getCanDelete();
        $canEdit = $userPermissions[0]->getCanEdit();
        $canView = $userPermissions[0]->getCanView();
        $canAdd = $userPermissions[0]->getCanAdd();

        $request->setLocale($language);
        $session->set('locale', $language);

        $userCategoriesRepository = $this->getDoctrine()->getRepository('AppBundle:AdminCategories');
        $categories = $userCategoriesRepository->getUserCategories($user->getAdminID());

        $session->set('categories', $categories);

        $systemPagesRepo = $this->getDoctrine()->getRepository('AppBundle:systemPages');
        $systemPagesTable = $systemPagesRepo->getSystemPagesForTable($user->getAdminID());
        
        $repo = $this->getDoctrine()->getRepository('AppBundle:User');

        $form=$this->formCreate($language, $categories);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $values = $form->getData();

            if($values["category"] == null){
                $values["parent"] = $categories;
            }

            $users = $repo->findAllCriteria($values,$pageNumber, 10);
            $session->set('data', $values);

            if($form->get('reset_form')->isClicked()){
                $session->set('data', null);
               return  $this->redirectToRoute('Users',array(
                    'pageNumber'=>$pageNumber,
                    'language'=>$language,
                    'canAdd' => $canAdd,
                    'canView' => $canView,
                    'canDelete' => $canDelete,
                    'canEdit' => $canEdit,
                    'userPages' => $systemPagesTable,
               ));
            }
        }else{
            $values["parent"] = $categories;
            $users = $repo->findAllCriteria($values, $pageNumber, 10);
        }


        $countPages = $repo->countPages($values, 10);

        return $this->render('Admin/ManageUsers/ManageUsers.html.twig',
            [   'form'=> $form->createView(),
                'Users' => $users,
                'numPages' => $countPages,
                'pageNumber'=> $pageNumber,
                'language'=>$language,
                'canAdd' => $canAdd,
                'canView' => $canView,
                'canDelete' => $canDelete,
                'canEdit' => $canEdit,
                'userPages' => $systemPagesTable,
            ]
        );
    }

    /**
     * @Route("/Admin/ManageUsers/display/{id}/{pageNumber}", name="Display")
     */
    public function display(Request $request, $id, $pageNumber){
        $session = new Session();
        $language = $session->get('locale');
        $user = $this->getUser();

        $admin = $this->getUser();

        $permissionRepo = $this->getDoctrine()->getRepository('AppBundle:Permission');
        $permissions = $permissionRepo->findByUserId($admin->getAdminID())[0];

        $systemPagesRepo = $this->getDoctrine()->getRepository('AppBundle:systemPages');
        $systemPages = $systemPagesRepo->getSystemPagesForTable($user->getAdminID());

        if($permissions->getCanView() == 0){
            die("You cannot enter this page.");
        }

        $user = $this->getDoctrine()->getRepository('AppBundle:User')->findByIDView($id);
        
        if (!$user) {
            $technicalIssuesRepo = $this->getDoctrine()->getRepository('AppBundle:TechnicalIssue');
            $technicalIssuesRepo->addTechnicalIssue($user->getAdminId(), 'unknown', 'users', "User not found.", $this->getIpAddress());
        
            return $this->redirectToRoute('Users',array(
                'pageNumber'=>$pageNumber,
            ));
        }
        
        $systemLogRepo = $this->getDoctrine()->getRepository('AppBundle:SystemLog');
        $systemLogRepo->addSystemLog($admin->getAdminID(), $id, 'users', 'Display',$this->getIpAddress());
    
        return $this->render('Admin/ManageUsers/ViewUserInfo.html.twig', [
                'user' => $user,
                'pageNumber'=>$pageNumber,
                'language' => $language,
                'userPages' => $systemPages,
            ]
        );
    }

    /**
     * @Route("/Admin/ManageUsers/update/{id}/{pageNumber}" , name="update")
     */
    public function updateAction(Request $request, $id, $pageNumber){
        $admin = $this->getUser();

        $permissionRepo = $this->getDoctrine()->getRepository('AppBundle:Permission');
        $permissions = $permissionRepo->findByUserId($admin->getAdminID())[0];

        if($permissions->getCanEdit() == 0){
            die("You cannot enter this page.");
        }
        
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->find($id);

        $language = $request->getLocale();
        
        if (!$user) {
            $technicalIssuesRepo = $this->getDoctrine()->getRepository('AppBundle:TechnicalIssue');
            $technicalIssuesRepo->addTechnicalIssue($user->getAdminId(), 'unknown', 'users', "User not found.", $this->getIpAddress());
    
            return $this->redirectToRoute('Users',array(
                'pageNumber'=>$pageNumber,
                'language' => $language,
            ));
        }

        /** @var $post User */
        if ($user->getStatusId() == 0) {
            $user->setStatusId(1);
        } else if ($user->getStatusId() == 1) {
            $user->setStatusId(0);
        }
        $em->flush();
    
        $systemLogRepo = $this->getDoctrine()->getRepository('AppBundle:SystemLog');
        $systemLogRepo->addSystemLog($admin->getAdminId(), $id, 'users', 'Update',$this->getIpAddress());
        
        return $this->redirectToRoute('Users',array(
            'pageNumber'=> $pageNumber,
            'language' => $language,
        ));

    }

    /**
     * @Route("/Admin/ManageUsers/delete/{id}/{pageNumber}" , name="delete")
     */
    public function DeleteAction(Request $request, $id,$pageNumber){
        $user = $this->getUser();
        $permissionRepo = $this->getDoctrine()->getRepository('AppBundle:Permission');
        $permissions = $permissionRepo->findByUserId($user->getAdminID())[0];
       
        if($permissions->getCanDelete() == 0){
            die("You cannot enter this page.");
        }
        
        $language = $request->getLocale();

        $em = $this->getDoctrine()->getManager();

        $userToDelete = $em->getRepository('AppBundle:User')->find($id);
        
        if (!$user) {
            $technicalIssuesRepo = $this->getDoctrine()->getRepository('AppBundle:TechnicalIssue');
            $technicalIssuesRepo->addTechnicalIssue($user->getAdminId(), 'unknown', 'users', "User not found.", $this->getIpAddress());
    
            return $this->redirectToRoute('Users',array(
                'language' => $language,
                'pageNumber'=>$pageNumber,
            ));
        }
        
        $userToDelete->setIsDeleted(1);
        $em->flush();
    
        $systemLogRepo = $this->getDoctrine()->getRepository('AppBundle:SystemLog');
        $systemLogRepo->addSystemLog($user->getAdminId(), $id, 'users', 'Delete',$this->getIpAddress());
    
        return $this->redirectToRoute('Users',array(
            'language' => $language,
            'pageNumber'=>$pageNumber
        ));
    }


    public function formCreate($language, $data){
        $repositoryStatus = $this->getDoctrine()->getManager()->getRepository(UserStatus::class);
        $repositoryCategories = $this->getDoctrine()->getManager()->getRepository(Categories::class);

        $status = $repositoryStatus->findStatus();
        $categories = $repositoryCategories->findUsersCategory($data);

        $categoriesArray = array();
        $statusArray = array();

        foreach($categories as $value){
            $categoriesArray[$value->getCategoryName()] = $value->getCategoryID();
        }

        foreach($status as $value){
            $statusArray[$value->getUserStatusName( )] = $value->getUserStatusId();
        }

        if($language === 'ar'){
            $values = array(
                'year' => "سنة",
                'month' => "شهر",
                'day' => "يوم"
            );

            $labelButton = "إخلاء";

        }else{
            $values = array(
                'year' => "Year",
                'month' => "Month",
                'day' => "Day",
            );

            $labelButton = "Reset";

        }

        $form = $this->createFormBuilder()
            ->add('Name', TextType::class, [
                'required'  => false,
            ])
            ->add('email', TextType::class, [
                'required'  => false,
            ])
            ->add('status', ChoiceType::class, [
                'required' => false,
                'choices' => $statusArray,
            ])
            ->add('category', ChoiceType::class, [
                'required'  => false,
                'choices' => $categoriesArray,
            ])
            ->add('from',DateType::class, [
                'required' => false,
                'placeholder' => $values,
                'widget' => 'single_text',
                'html5' => false,
                'attr' =>['class' => 'js-datepicker']
            ])
            ->add('to', DateType::class, [
                'required'  => false,
                'placeholder' => $values,
                'html5' => false,
                'widget' => 'single_text',
                'attr' =>['class' => 'js-datepicker']
            ])
            ->add('Search', SubmitType::class)
            ->add('reset_form',SubmitType::class, [
                'label'=> $labelButton,
            ])
            ->getForm();

        return $form;
    }

    public function checkRoles($pageId, $userID){
        $repository = $this->getDoctrine()->getRepository('AppBundle:systemPages');
        $repositoryRoles = $this->getDoctrine()->getRepository('AppBundle:UsersRoles');
        $repositoryPermissions = $this->getDoctrine()->getRepository('AppBundle:Permission');

        $roleOfPage = $repository->findRolesByPageId($pageId);
        $rolesOfUser = $repositoryRoles->getRolesById($userID);

        return in_array($roleOfPage['role_id'], $rolesOfUser, false) && $repositoryPermissions->findByUserRoleId($userID, $roleOfPage);
    }


}