<?php

namespace MedMessage\Services;

use GuzzleHttp\Client;
use PSpell\Dictionary;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;



class MailerService
{
    // public function __construct(ClientInterface $client, string $apiKey)
    // {
    //     $this->client = $client;
    //     $this->apiKey = $apiKey;
    // }


    // public function sendEmail($msg, $email, $titre, $pdf): void
    // {

    //     $email = (new Email())
    //         ->from('Service@biopoolsafe.com')
    //         ->to($email)
    //         ->subject($titre)
    //         ->text($msg)
    //         ->attachFromPath('127.0.0.1:8882/img/files/' . $pdf);

    //     // $dsn = 'smtp://809bd9983aaea5:44c190e9a5efcc@sandbox.smtp.mailtrap.io:2525?encryption=tls&auth_mode=login';
    //     $dsn = 'smtp://biopooltech:biopooltech@maildev:25?encryption=tls&auth_mode=login';
    //     $transport = Transport::fromDsn($dsn);
    //     $mailer = new Mailer($transport);
    //     $mailer->send($email);
    // }

    public function sendEmail($shopId, $customerId, $objet, $talkId): void
    { 
        // $apiKey = '600b830d-0d7b-4be5-9ef2-9ea8b55f2daf';
        // $shopId = 7;
        // $customerId = 199;
        // $talkId = 33656;
        // $objet = 'test moh test';
    
        $client= new Client();
        $response = $client->post('http://bpt-api:8000/send_mail',[
        'headers' => [
            'accept' => 'application/json',
            'Content-Type' => 'application/json',
            // 'bpt-api-key-global' => $apiKey,
        ],
        'json' => [  
            'shop_id' => $shopId,
            'customer_id' => $customerId,
            'talk_id' => $talkId,
            'subject' => $objet
        ],

        ]);
        
        $body = $response->getBody();
    }
/**
     * Adds current Customer as a new Object to the database mongoDB*/
    public function createCustomer($customerId, $email, $firstName, $lastName, $shopId): void
    {     
        $apiKey = '600b830d-0d7b-4be5-9ef2-9ea8b55f2daf'; 

        $client= new Client();
        $response = $client->post('http://bpt-api:8000/customer/create',[
        'headers' => [
            'accept' => 'application/json',
            'Content-Type' => 'application/json',
            'bpt-api-key-global' => $apiKey,
        ],
        'json' => [  
            'customer_id' => $customerId,
            'customer_mail' => $email,
            'customer_firstname' => $firstName,
            'customer_lastname' => $lastName,
            'shop_id' => $shopId,
            
        ],

        ]);
        
        $body = $response->getBody();
    }

    /**
     * Adds current Shop as a new Object to the database mongoDB*/
    public function createShop($shopId, $lastName): void
    {     
        $apiKey = '600b830d-0d7b-4be5-9ef2-9ea8b55f2daf';
        $client= new Client();
        $response = $client->post('http://bpt-api:8000/shop/create',[
        'headers' => [
            'accept' =>  'application/json',
            'Content-Type' => 'application/json',
            'bpt-api-key-global' => $apiKey,
        ],
        'json' => [  
            'shop_id' => $shopId,
            'shop_lastname' => $lastName          
        ],

        ]);
        
        $body = $response->getBody();
    }

     /**
     * adds mail franchise in current Shop to the database mongoDB*/
    public function updateMailShop($shopId, $email): void
    {     
        $apiKey = '600b830d-0d7b-4be5-9ef2-9ea8b55f2daf';
        $client= new Client();
        $response = $client->post('http://bpt-api:8000/shop/update',[
        'headers' => [
            'accept' =>  'application/json',
            'Content-Type' => 'application/json',
            'bpt-api-key-global' => $apiKey,
        ],
        'json' => [  
            'shop_id' => $shopId,
            'shop_mail' => $email          
        ],

        ]);
        
        $body = $response->getBody();
    }

    public function getEmail($idShop, $apiKey, $mailShop)
    {
        // $mailShop = 'franchisealsace@biopooltech.com';
        // $idShop = 15;
        // $apiKey = '5cd88ac3-7b81-4ac3-af08-8abf673da108';
        // Créez une instance de Guzzle Client
        $client = new Client();

        // Définissez l'URL complète en utilisant le shop_id fourni
        $url = 'http://bpt-api:8000/' . $idShop . '/talks';

    
            // Effectuez la requête GET en incluant les paramètres d'authentification
            $response = $client->get($url, [
                'headers' => [
                    'accept' =>  'application/json',
                    'shop-mail' => $mailShop, // Filtrez par adresse e-mail du shop
                    'api-key-user' => $apiKey, // Utilisez l'API key pour l'authentification
                ],
                // 'json' => [
                //     'shop_id' => $idShop,
                //     'shop-mail' => $mailShop, // Filtrez par adresse e-mail du shop
                //     'api-key-user' => $apiKey, // Utilisez l'API key pour l'authentification
                // ],
            ]);

            // Vérifiez si la réponse a réussi (code de réponse 200)
            if ($response->getStatusCode() == 200) {
                // Récupérez le contenu de la réponse (suppose que c'est au format JSON)
                $apiData = json_decode($response->getBody(), true);

                // Traitez les données de réponse comme nécessaire
                return $apiData;
            } else {
                // La requête a échoué, vous pouvez gérer cette erreur ici
                $hi= 'bye';
                return $hi;
            }
        
    }
}
