<?php

namespace AppBundle\Controller;
use AppBundle\Entity\Categories;
use AppBundle\Entity\EventStatus;
use AppBundle\Entity\User;
use Doctrine\DBAL\DBALException;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
class ManageEventsController extends Controller{
    //EVENTS

    /**
     * @Route("/Admin/Events/{pageNumber}",name="Events")
     */
    public function EventAction(Request $request,$pageNumber){
        $session = new Session();
        $PAGE_ID = 3;

        $user = $this->getUser();
        $userID = $user->getAdminID();

        $permissionRepo = $this->getDoctrine()->getRepository('AppBundle:Permission');
        $permissions = $permissionRepo->findByUserId($user->getAdminID())[0];

        if(!$this->checkRoles($PAGE_ID, $userID)){
            die("You can't access this page.");
        }
        
        $session->set('data', null);

        $canDelete = $permissions->getCanDelete();
        $canEdit = $permissions->getCanEdit();
        $canView = $permissions->getCanView();
        $canAdd = $permissions->getCanAdd();

        $language = $session->get('locale');

        $request->setLocale($session->get('locale'));
        $categories = $session->get('categories');

        $form=$this->formCreate($request->getLocale(), $categories);

        $repo = $this->getDoctrine()->getRepository('AppBundle:Event');

        $form->handleRequest($request);
        $values = $session->get('data');
    
        $systemPagesRepo = $this->getDoctrine()->getRepository('AppBundle:systemPages');
        $systemPages = $systemPagesRepo->getSystemPagesForTable($user->getAdminID());
    
        if($form->isSubmitted() && $form->isValid()){
            $values = $form->getData(); //Form data from the user

            if($values["category"] == null){
                $values["parent"] = $categories;
            }

            $session->set('data', $values);

            $events = $repo->findAllCriteria($values, $pageNumber, 10);
            $events = $events[0];

            if($form->get('reset_form')->isClicked()){
                $session->set('data', null);
                return  $this->redirectToRoute('Events',array(
                    'pageNumber'=>$pageNumber,
                    'language'=>$language,
                    'canAdd' => $canAdd,
                    'canView' => $canView,
                    'canDelete' => $canDelete,
                    'canEdit' => $canEdit,
                    'events' => $events,
                ));
            }
        }else{
           $values["parent"] = $categories;

            $events = $repo->findAllCriteria($values, $pageNumber, 10);
            $events = $events[0];
        }

        $countPages = $repo->countPages($values,10);

        return $this->render('Admin/ManageEvents/Events.html.twig',[
            'form' => $form->createView(),
            'events' => $events,
            'numPages' => $countPages,
            'pageNumber'=>$pageNumber,
            'language' =>$language,
            'canAdd' => $canAdd,
            'canView' => $canView,
            'canDelete' => $canDelete,
            'canEdit' => $canEdit,
            'userPages' => $systemPages,
        ]);
    }

    /**
     *
     * @return \DateTime
     */
    public function getDaate()
    {
        return new \DateTime('now', (new \DateTimeZone('Asia/Beirut')));
    }

    /**
     * @Route("/Admin/ManageEvents/delete/{id}/{pageNumber}" , name="EventDelete")
     */
    public function DeleteEvent(Request $request, $id,$pageNumber)
    {

        $user = $this->getUser();
        $permissionRepo = $this->getDoctrine()->getRepository('AppBundle:Permission');
        $permissions = $permissionRepo->findByUserId($user->getAdminID())[0];

        if($permissions->getCanDelete() == 0){
            die("You cannot enter this page.");
        }

        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository('AppBundle:Event')->find($id);

        if (!$event) {
            $technicalIssuesRepo = $this->getDoctrine()->getRepository('AppBundle:TechnicalIssue');
            $technicalIssuesRepo->addTechnicalIssue($user->getAdminId(), 'unknown', 'events', "Event not found.", $this->getIpAddress());
            return $this->redirectToRoute('Events', [
                'pageNumber' => 1,
            ]);
        }
        
        $event->setIsDeleted(1);
        $em->flush();
    
        $systemLogRepo = $this->getDoctrine()->getRepository('AppBundle:SystemLog');
        $systemLogRepo->addSystemLog($user->getAdminId(), $id, 'Events', 'Delete',$this->getIpAddress());
    
        return $this->redirectToRoute('Events', [
            'pageNumber' => 1,
        ]);
    }

    /**
     * @Route("/Admin/ManageEvents/update/{status}/{id}/{pageNumber}" , name="EventUpdate")
     */
    public function UpdatePending($id,$pageNumber,$status){
        $user = $this->getUser();

        $permissionRepo = $this->getDoctrine()->getRepository('AppBundle:Permission');
        $permissions = $permissionRepo->findByUserId($user->getAdminID())[0];

        if($permissions->getCanEdit() == 0){
            die("You cannot enter this page.");
        }

        $em = $this->getDoctrine()->getManager();
        $EventPending=$em->getRepository('AppBundle:Event')->find($id);
        if(!$EventPending){
    
            $technicalIssuesRepo = $this->getDoctrine()->getRepository('AppBundle:TechnicalIssue');
            $technicalIssuesRepo->addTechnicalIssue($user->getAdminId(), 'multiple', 'events', "Event not found.", $this->getIpAddress());
            
            return $this->redirectToRoute('Events',[
                'pageNumber'=>$pageNumber,
            ]);
        }
    
        /** @var $post User */
        $EventPending->setEventStatusID($status);
        
        $em->flush();
        $systemLogRepo = $this->getDoctrine()->getRepository('AppBundle:SystemLog');
        $systemLogRepo->addSystemLog($user->getAdminId(), $id, 'events', 'Update Status to '.$status,$this->getIpAddress());
    
        return $this->redirectToRoute('Events',[
                'pageNumber'=>$pageNumber,
            ]
        );
    }
    public function formCreate($language, $data){
        $repositoryCategories = $this->getDoctrine()->getManager()->getRepository(Categories::class);
        $repositoryStatus = $this->getDoctrine()->getManager()->getRepository(EventStatus::class);


        $categories = $repositoryCategories->findUsersCategory($data);
        $status = $repositoryStatus->findAll();

        $categoryArray = array();
        $statusArray = array();

        foreach($categories as $value){
            $categoryArray[$value->getCategoryName()] = $value->getCategoryID();
        }

        foreach($status as $value){
            $statusArray[$value->getEventStatusName()] = $value->getEventStatusId();
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
            ->add('eventName', TextType::class, [
                'required'  => false,
            ])
            ->add('holder_name', TextType::class, [
                'required'  => false,
            ])
            ->add('status', ChoiceType::class, [
                'required' => false,
                'choices' => $statusArray,
            ])
            ->add('category', ChoiceType::class, [
                'required'  => false,
                'choices' => $categoryArray,

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

    public function checkRoles($pageId, $userID){
        $repository = $this->getDoctrine()->getRepository('AppBundle:systemPages');
        $repositoryRoles = $this->getDoctrine()->getRepository('AppBundle:UsersRoles');
        $repositoryPermissions = $this->getDoctrine()->getRepository('AppBundle:Permission');

        $roleOfPage = $repository->findRolesByPageId($pageId);

        $rolesOfUser = $repositoryRoles->getRolesById($userID);

        return in_array($roleOfPage['role_id'], $rolesOfUser, false) && $repositoryPermissions->findByUserRoleId($userID, $roleOfPage);
    }


}