<?php

namespace MedMessage\Controller;

use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\ClientInterface;
use MedMessage\Entity\MessageComment;
use MedMessage\Forms\MessageType;
use MedMessage\Services\MailerService;
use PrestaShop\PrestaShop\Adapter\Entity\Context;
use PrestaShop\PrestaShop\Adapter\Entity\Db;
use PrestaShop\PrestaShop\Adapter\Entity\Tools;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class MessageController extends FrameworkBundleAdminController
{
  //Fil discussion
    public function threadMessage(int $idthread, Request $request)
    { 
        //récupérer id franchise
        $shopId = Context::getContext()->shop->id;

        $sql = 'SELECT mc.id_message_comment, mc.id_employe, mc.id_customer,
        mc.title, mc.id_thread, mc.message, mc.date, mc.file, mc.id_state_comment, mc.id_shop,
        UCASE(LEFT(c.firstname, 1)) AS customer_firstname, c.lastname AS customer_lastname, 
        c.email AS customer_email, UCASE(LEFT(e.firstname, 1)) AS employee_firstname, 
        e.lastname AS employee_lastname,
        s.id_state_comment AS etat 
        FROM ' . _DB_PREFIX_ . 'message_comment mc
        LEFT JOIN ' . _DB_PREFIX_ . 'customer c ON mc.id_customer = c.id_customer
        LEFT JOIN ' . _DB_PREFIX_ . 'employee e ON mc.id_employe = e.id_employee
        LEFT JOIN ' . _DB_PREFIX_ . 'state_comment s ON mc.id_state_comment = s.id_state_comment 
        WHERE mc.id_thread =' .(int)$idthread.
        ' AND mc.id_shop ='.$shopId. 
        ' ORDER BY mc.date DESC';
        $customers = Db::getInstance()->getRow($sql);
        $data = Db::getInstance()->executeS($sql);
        $form = $this->createForm(MessageType::class);
        $form->handleRequest($request);
        

        // logic of form handling
        if ($form->isSubmitted() && $form->isValid()) {
            // logic to store the data to the DB
            $em = $this->getDoctrine()->getManager();

            

            //prepare the object that will be saved to the DB
            $messageComment = new MessageComment();
            $file = $form->get('files')->getData();
            $destination = $this->getParameter('kernel.project_dir') . '/img/p';


            if ($file) {

                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $file->guessExtension();
                // Move the file to the directory where brochures are stored
                try {

                    $file->move($destination, $newFilename);
                } catch (FileException $e) {
                    $e->getMessage('error');
                }
                $messageComment->setFile($newFilename);
            }   
            
            foreach ($data as $raw){
        
                $customerId = $raw['id_customer'];
                $messageComment->setCustomerId($customerId);
                $title = $raw['title'];
                $messageComment->setTitle($title);
                $message = $form->get('message')->getData();
                $messageComment->setMessage($message);
                $idthread = $raw['id_thread'];
                $messageComment->setThreadId($idthread);
                $messageComment->setDate(new \DateTime());
                $messageComment->setEmployeId($this->getUser()->getId());
                $messageComment->setStateCommentId(1);
                $messageComment->setShopId($shopId);
            } 
            if (!empty($message)){   
                $em->persist($messageComment);
                $em->flush();

                //send mail to the customer

                $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'customer
                WHERE id_customer =' .$customerId;

                $result = Db::getInstance()->executeS($sql);
                
                $mailer = new MailerService();
                
                foreach ($result as $raw){
                    
                    $msg = $messageComment->getMessage();
                    $email = $raw['email'];

                    $subject = $messageComment->getTitle();
                    // $fileId = $newFilename;
                    
                    $firstName = $raw['firstname'];
                    
                    // $msg = "Vous trouverez ci-joint votre Devis";
                    // $email = "toto@gmail.com";
                    // $subject = "Votre devis";
                    // $file_id = '111';
                    $mailer->sendEmail($shopId, $customerId, $objet, $talkId);
                
                    //alert message 
                    $this->addFlash(
                        'success',
                        'Votre message a bien été envoyé à ' . $email
                    );
                }
                return $this->redirectToRoute('Message-thread', ['idthread'=> $idthread]);
            }else{
                $this->addFlash(
                    'error',
                    'Le champ Message ne doit pas être vide'
                );
            }
            
        
        }
        return $this->render('@Modules/med_message/templates/admin/thread.html.twig', [
            'shopId' => $shopId,
            'data' => $data,
            'customers' => $customers,
            'form' => $form->createView(),
        ]);
    }
    //message list
    public function listMessage(Request $request)
    {
        //Récupérer id franchise
        $shopId = Context::getContext()->shop->id;
        // $do= Context::getContext();
        // dump($do);
       
        //Select messages par id franchise de bpt-api
        // $mailer = new MailerService();
        // $mailShop = 'franchiseloire@biopooltech.com';
        // $idShop = $shopId;
        // $apiKey = '9a9c3c3e-3583-4cca-ab63-df000fcb8e29';

        // $dataApi = $mailer->getEmail($idShop, $apiKey, $mailShop);
        // dump($dataApi);

        // foreach ($dataApi as $emailData) {
        //     $talkId = $emailData['talk_id'];
        //     $object = $emailData['object'];
        //     $message = $emailData['message'];
        //     $createdDate = $emailData['created_date'];
        //     $customerIdApi = $emailData['customer_id'];
        
        // //Select clients par id franchise id customer
        // $dbca = 'SELECT * FROM ' . _DB_PREFIX_ . 'customer 
        // WHERE id_shop ='.$shopId.' 
        // AND id_customer ='.$customerIdApi;   
        // $customersApi = Db::getInstance()->executeS($dbca);
        // dump($customersApi);
        // }
        //Select clients par id franchise
        $db = 'SELECT * FROM ' . _DB_PREFIX_ . 'customer 
        WHERE id_shop ='.$shopId.' ORDER BY firstname';   
        $customers = Db::getInstance()->executeS($db);
        
        //Select employers par id franchise
        $dbe = 'SELECT * FROM ' . _DB_PREFIX_ . 'employee e 
        LEFT JOIN ' . _DB_PREFIX_ . 'employee_shop es ON e.id_employee = es.id_employee
        WHERE es.id_shop ='.$shopId;   
        $employees = Db::getInstance()->executeS($dbe);
        
        //Select Status messages
        $dbs = 'SELECT * FROM ' . _DB_PREFIX_ . 'state_comment';  
        $states = Db::getInstance()->executeS($dbs);

        $customerId = $request->get('id_customer');
        $customerName = $request->get('name_customer');
        $keyTitle = $request->get('title_key');
        $employeeName = $request->get('name_employee');
        $keyMessage = $request->get('message_key');
        $state = $request->get('state');
        $threadId = $request->get('id_thread');
        $dateDebut = $request->get('date_debut');
        $dateDebutC = $dateDebut . ' 00:00:00';
        $dateFin = $request->get('date_fin');
        $dateFinC = $dateFin . ' 23:59:59';
        
        // pagination
        $currentPage = $request->query->getInt('page', 1); // Page actuelle
        $limit = 15; // Nombre de messages à afficher par page
        $dbp = 'SELECT COUNT(*) AS num FROM ' . _DB_PREFIX_ . 'message_comment';  
        $result = Db::getInstance()->executeS($dbp);
        foreach ($result as $raw){
        $totalCount = $raw['num'];
        }
        $premier = ($currentPage * $limit) - $limit;

        //Select messages par id franchise
        $dbc = 'SELECT mc.id_message_comment, mc.id_employe, mc.id_customer,
        mc.title, mc.id_thread, mc.message, mc.date, mc.file, mc.id_state_comment, mc.id_shop,
        c.firstname AS customer_firstname, c.lastname AS customer_lastname, 
        c.email AS customer_email, UCASE(LEFT(e.firstname, 1)) AS employee_firstname, 
        e.lastname AS employee_lastname,
        s.id_state_comment AS etat 
        FROM ' . _DB_PREFIX_ . 'message_comment mc
        LEFT JOIN ' . _DB_PREFIX_ . 'customer c ON mc.id_customer = c.id_customer
        LEFT JOIN ' . _DB_PREFIX_ . 'employee e ON mc.id_employe = e.id_employee
        LEFT JOIN ' . _DB_PREFIX_ . 'state_comment s ON mc.id_state_comment = s.id_state_comment
        WHERE mc.id_shop ='.$shopId;
        // Ajouter le filtrage
        if (!empty($customerName)){
            $dbc .=' AND mc.id_customer ='.$customerName;
        }
        if (!empty($customerId)){
            $dbc .=' AND mc.id_customer ='.$customerId;
        }
        if (!empty($keyTitle)){
            $dbc .=' AND mc.title LIKE "%'.$keyTitle.'%"';
        }
        if (!empty($employeeName)){
            $dbc .=' AND mc.id_employe ='.$employeeName;
        }
        if (!empty($keyMessage)){
            $dbc .=' AND mc.message LIKE "%'.$keyMessage.'%"';
        }
        if (!empty($state)){
            $dbc .=' AND mc.id_state_comment ='.$state;
        }
        if (!empty($threadId)){
            $dbc .=' AND mc.id_thread LIKE "%'.$threadId.'%"';
        }
        if (!empty($dateDebut)){
            $dbc .=' AND mc.date >= "'.$dateDebutC.'"';
        }
        if (!empty($dateFin)){
            $dbc .=' AND mc.date <= "'.$dateFinC.'"';
        }
        $dbc .=' ORDER BY mc.date DESC LIMIT '.$premier.', '.$limit;
        
        $data = Db::getInstance()->executeS($dbc);

        
        return $this->render(
            '@Modules/med_message/templates/admin/list.html.twig',
            [
                  
                'customers' => $customers,
                'employees' => $employees,
                'keyTitle' => $keyTitle,
                'keyMessage'=> $keyMessage,
                'states' => $states,
                'state' => $state,
                'threadId'=> $threadId,
                'data' => $data,
                'customerId' => $customerId,
                'customerName' => $customerName,
                'employeeName' => $employeeName,
                'dateDebut' => $dateDebut,
                'dateFin' => $dateFin,
                'currentPage' => $currentPage,
                'pages' => (int) ceil($totalCount / $limit),
                // 'talkId' => $talkId,
                // 'object' => $object,
                // 'message' => $message,
                // 'createdDate' => $createdDate,
                // 'customersApi' => $customersApi,
                // 'dataApi' => $dataApi



            ]
        );
         
        // dump($idShop);
        // $em = $this->getDoctrine()->getManager();
        // $data = $em->getRepository(MessageComment::class)->findBy(array("employeId" => $this->getUser()->getId()));

        
    }
    //Autocomplition customers name search
    public function autocompletionCustomerName(Request $request): JsonResponse
    {
        $shopId = Context::getContext()->shop->id;

        $term = $request->request->get('term');
        // $term = Tools::getValue('term');

        $db = "SELECT id_customer, CONCAT(c.firstname, ' ', c.lastname) AS value
        FROM " . _DB_PREFIX_ . "customer c
        WHERE CONCAT(c.firstname, ' ', c.lastname) LIKE '%" . pSQL($term) . "%' AND id_shop =".$shopId;
        $customers = Db::getInstance()->executeS($db);

        // Formater les résultats pour l'autocomplétion
        $results = [];
        foreach ($customers as $customer) {
            $results[] = [
                'label' => $customer['value'],
                'value' => $customer['id_customer']
            ];
        }

        return new JsonResponse($results);

    }

    //Autocomplition customers mail search
    public function autocompletionCustomerMail(Request $request): JsonResponse
    {
        $shopId = Context::getContext()->shop->id;

        $term = $request->request->get('term');
        // $term = Tools::getValue('term');

        $db = "SELECT id_customer, email
        FROM " . _DB_PREFIX_ . "customer 
        WHERE email LIKE '%" . pSQL($term) . "%' AND id_shop =".$shopId;
        $customers = Db::getInstance()->executeS($db);

        // Formater les résultats pour l'autocomplétion
        $results = [];
        foreach ($customers as $customer) {
            $results[] = [
                'label' => $customer['email'],
                'value' => $customer['id_customer']
            ];
        }

        return new JsonResponse($results);

    }

    //Autocomplition customers form
    public function autocompletionCustomer(Request $request): JsonResponse
    {
        $shopId = Context::getContext()->shop->id;

        $term = $request->request->get('term');
        // $term = Tools::getValue('term');

        $db = "SELECT id_customer, CONCAT(c.firstname, ' ', c.lastname, ' - ', c.email) AS value
        FROM " . _DB_PREFIX_ . "customer c
        WHERE CONCAT(c.firstname, ' ', c.lastname, ' - ', c.email) LIKE '%" . pSQL($term) . "%' AND id_shop =".$shopId;
        $customers = Db::getInstance()->executeS($db);

        // Formater les résultats pour l'autocomplétion
        $results = [];
        foreach ($customers as $customer) {
            $results[] = [
                'label' => $customer['value'],
                'value' => $customer['id_customer']
            ];
        }

        return new JsonResponse($results);

    }
    //send message
    public function createMessage(Request $request): Response
    {
        $shopId = Context::getContext()->shop->id;

        $db = 'SELECT * FROM ' . _DB_PREFIX_ . 'customer 
        WHERE id_shop ='.$shopId.' ORDER BY firstname';
   
        $customers = Db::getInstance()->executeS($db);
        $form = $this->createForm(MessageType::class);
        $form->handleRequest($request);


        // logic of form handling
        if ($form->isSubmitted() && $form->isValid()) {
            // logic to store the data to the DB
            $em = $this->getDoctrine()->getManager();

            

            //prepare the object that will be saved to the DB
            $messageComment = new MessageComment();
            // Upload Files
            $file = $form->get('files')->getData();
            $destination = $this->getParameter('kernel.project_dir') . '/img/p';


            if ($file) {

                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $file->guessExtension();
                // Move the file to the directory where brochures are stored
                try {

                    $file->move($destination, $newFilename);
                } catch (FileException $e) {
                    $e->getMessage('error');
                }
                $messageComment->setFile($newFilename);
            }    
            $customerId = $request->get('id_customer');
            $messageComment->setCustomerId($customerId);
            $title = $form->get('title')->getData();
            $messageComment->setTitle($title);
            $message = $form->get('message')->getData();
            $messageComment->setMessage($message);
            $messageComment->setThreadId($customerId.random_int(9, 999999));
            $messageComment->setDate(new \DateTime());
            $messageComment->setEmployeId($this->getUser()->getId());
            $messageComment->setStateCommentId(1);
            $messageComment->setShopId($shopId);
            if (!empty($customerId) && !empty($title) && !empty($message)){
                //persist the data
                $em->persist($messageComment);
                $em->flush();
                // echo json_encode($messageComment);
            
                //send mail to the customer

                $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'customer c 
                LEFT JOIN ' . _DB_PREFIX_ . 'shop s ON c.id_shop = s.id_shop
                WHERE id_customer =' .$customerId;

                $result = Db::getInstance()->executeS($sql);
                
                $mailer = new MailerService();
                
                foreach ($result as $raw){
                
                    $msg = $messageComment->getMessage();
                    $email = $raw['email'];

                    $objet = $messageComment->getTitle();
                    // $fileId = $newFilename;
                    
                    $shopName = $raw['name'];
                    $talkId = $messageComment->getThreadId();

                    $mailer->sendEmail($shopId, $customerId, $objet, $talkId);
                
                    //alert message 
                    $this->addFlash(
                        'success',
                        'Votre message a bien été envoyé à ' . $email
                    );
                }
                return $this->redirectToRoute('Message-list');
            }else{
                $this->addFlash(
                    'error',
                    'Les champs Client, Objet et Message ne doivent pas être vides'
                );
            }
        }

        return $this->render(
            '@Modules/med_message/templates/admin/create.html.twig',
            [
                'form' => $form->createView(),
                'customers' => $customers
            ]
        );
    }

    //delete
    public function deleteMessage(int $id)
    {
        $em = $this->getDoctrine()->getManager();
        $messageComment = $em->getRepository(MessageComment::class)->findOneBy(['id' => $id]);
        if ($messageComment) {
            $em->remove($messageComment);
            $em->flush();

            $this->addFlash(
                'success',
                'Message effacé avec succès'
            );
        }
        return $this->redirectToRoute('Message-list');
    }
}
