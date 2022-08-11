<?php
/**
 * Created by PhpStorm.
 * User: fakho
 * Date: 2/1/2018
 * Time: 7:12 PM
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Questions;
use Doctrine\DBAL\DBALException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Choice;

class AddQuestionController extends Controller{
    /**
     * @Route("/Admin/ManageCategoryQuestions/AddQuestion",name="AddQuestion")
     */
    public function addQuestionAction(Request $request){
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

        $form = $this->createFormQuestion();
        $form->handleRequest($request);
    
        $locale = $session->get('locale');
        $request->setLocale($locale);
        
        $systemPagesRepo = $this->getDoctrine()->getRepository('AppBundle:systemPages');
        $systemPages = $systemPagesRepo->getSystemPagesForTable($user->getAdminID());
    
        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $data = $form->getData();

            $question = new Questions();
            $question->setQuestName($data["questionName"]);
            $question->setQuestion($data["question"]);
            $question->setQuestionType($data["questionType"]);
            $question->setIsDeleted(0);

            $em->persist($question);
    
            try{
                $em->flush();
            }catch ( DBALException  $e ){
                $technicalIssuesRepo = $this->getDoctrine()->getRepository('AppBundle:TechnicalIssue');
                $technicalIssuesRepo->addTechnicalIssue($user->getAdminId(), $question->getQuestID(), 'questions', $e->getMessage(), $this->getIpAddress());
                die($e->getMessage());
            }
    
            $questionId = $question->getQuestID();
            
            $systemLogRepo = $this->getDoctrine()->getRepository('AppBundle:SystemLog');
            $systemLogRepo->addSystemLog($user->getAdminId(), $questionId, 'questions', 'Add',$this->getIpAddress());
    
            return $this->redirectToRoute('ManageCategoryQuestions', [
                'pageNumber' => 1,
                'userPages' => $systemPages,
            ]);
        }

        return $this->render('Admin/ManageCategoryQuestions/AddCategory.html.twig', [
            'form' => $form->createView(),
            'pageNumber' => 1,
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

    public function createFormQuestion(){
        $categoryRepo = $this->getDoctrine()->getRepository('AppBundle:Categories');
        $questionTypeRepo = $this->getDoctrine()->getRepository('AppBundle:typeOfQuestion');
        
        $types = $questionTypeRepo->findAll();
        
        $categoriesDisplay = Array();
        $questionTypes = Array();

        foreach ($types as $type){
            $questionTypes[$type->getQuestionType()] = $type->getTypeQuestionId();
        }
        
        $form = $this->createFormBuilder()
            ->add('questionName', TextType::class, [
                'required'  => true,
            ])
            ->add('question', TextType::class, [
                'required' => true,
            ])
            ->add('questionType', ChoiceType::class, [
                'required' => true,
                'choices'  => $questionTypes,
            ])
            ->add('Submit',SubmitType::class)
            ->add('Clear', ResetType::class)
            ->getForm();
        
        return $form;
    }
}