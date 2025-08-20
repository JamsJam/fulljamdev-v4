<?php

namespace App\Service;

use Stripe\StripeClient;
use Stripe\Checkout\Session;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class StripeService
{



    public function __construct(
        private string $stripeSecretKey,
        private UrlGeneratorInterface $urlGenerator,
    ){}

    /**
     * Créer une session Stripe Checkout et retourner l'URL
     *
     * @param array  $products Tableau des produits avec type ('one_time' ou 'subscription')
     * @param string $successUrl url apres succes du paiement
     * @param string $cancelUrl url apres echec du paiement
     * @return array
     */
    public function createCheckoutSession(
            array $products,
            ?string $successUrl = null,
            ?string $cancelUrl = null
        ): array 
    {


        $stripe = new StripeClient($this->stripeSecretKey);

        //? ================= set Default Parameters
            $lineItem = [];
            $mode = 'payment'; // Par défaut, mode pour les paiements uniques
            $sessionInvoce = ['enabled' => true];
            
            
            // default sucess and cancel

            $successRedirect = $successUrl === null ? $this->urlGenerator->generate("app_stripe_success",[],UrlGeneratorInterface::ABSOLUTE_URL): $successUrl;
            $cancelRedirect  = $cancelUrl === null ? $this->urlGenerator->generate("app_stripe_cancel",[],UrlGeneratorInterface::ABSOLUTE_URL) : $cancelUrl;

        foreach ($products as $product) {
            // dd($products);
        //? ================= Error Checking
                    // Vérifier que le tableau de produits n'est pas vide
            if (empty($products)) {
                throw new \InvalidArgumentException('Le tableau de produits ne peut pas être vide.');
            }
            if (!isset(
                $product['productName'], 
                $product['amount'], 
                $product['quantity'], 
                $product['type']
                )) {
                throw new \InvalidArgumentException(
                    'Chaque produit doit contenir "productName", "amount", "quantity", et "type".'
                );
            }
            // dd('test reussi');

        //? ================= Products handle
            if ($product['type'] === 'subscription') {
                $mode = 'subscription';

                
                $lineItem[] = [
                    'price_data' => [
                        'currency' => 'eur',
                        'recurring' => [
                            'interval' => $product['interval'], // Ex : 'month', 'year'
                        ],
                        'product_data' => [
                            'name' => $product['productName'],
                        ],
                        'unit_amount' => $product['amount'], // Montant en centimes
                    ],
                    'quantity' => $product['quantity'],
                ];
            } else { // Si c'est un produit standard (paiement unique)
                $lineItem[] = [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $product['productName'],
                        ],
                        'unit_amount' => $product['amount'], // Montant en centimes
                    ],
                    'quantity' => $product['quantity'],
                ];
            } 
        }


        //? =========== Create  session
            try{
                $checkout_session = $stripe->checkout->sessions->create([
                    'payment_method_types' => ['card'],
                    'line_items' => $lineItem,
                    'mode' => $mode,
                    'success_url' => $successRedirect,
                    'cancel_url' => $cancelRedirect,
                ]);

                if($mode === 'payment'){
                    $checkout_session['invoice_creation'] = ['enabled' => true];
                }
            } catch (\Exception $e) {
                throw new \RuntimeException('Erreur Stripe : ' . $e->getMessage());
            }

            return [
                'id' => $checkout_session->id,
                'url' => $checkout_session->url
            ];
    }


    public function getStripeInvoiceLink(?string $sessionId){
        $stripeClient = new StripeClient($this->stripeSecretKey);
        $invoiceId = $this->getSessionCheckout($sessionId)['invoice'];
        $stripeInvoice = $stripeClient->invoices->retrieve($invoiceId, [])['hosted_invoice_url'];

        
        return $stripeInvoice;
    }

    public function getSessionCheckout(?string $sessionId){
        $stripe = new StripeClient($this->stripeSecretKey);

        $stripeSession = $stripe->checkout->sessions->retrieve($sessionId, []);
        
        return $stripeSession;
    }


}
