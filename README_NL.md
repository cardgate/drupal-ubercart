![CardGate](https://cdn.curopayments.net/thumb/200/logos/cardgate.png)

# CardGate module voor Drupal 7 Ubercart 3.x

[![Total Downloads](https://img.shields.io/packagist/dt/cardgate/drupal-ubercart.svg)](https://packagist.org/packages/cardgate/drupal-ubercart)
[![Latest Version](https://img.shields.io/packagist/v/cardgate/drupal-ubercart.svg)](https://github.com/cardgate/drupal-ubercart/releases)
[![Build Status](https://travis-ci.org/cardgate/drupal-ubercart.svg?branch=master)](https://travis-ci.org/cardgate/drupal-ubercart)

## Support

Deze plug-in is geschikt voor Drupal versie **7.x** en maakt gebruik van Ubercart versie **3.x**  

## Voorbereiding

Voor het gebruik van deze module zijn CardGate gegevens nodig.
Bezoek hiervoor [Mijn CardGate](https://my.cardgate.com/) en haal daar je  Site ID and Hash key op,  
of neem contact op met je accountmanager.

## Installatie

1. Download en unzip het **uc_cardgate.zip** bestand op je bureaublad.

2. Upload de **inhoud** van de zipfile naar je **Drupal modules** map, die je hier kunt vinden:  
   **http://mijnwebshop.com/htdocs/sites/all/modules/**  
  (Vervang **http://mijnwebshop.com** met de URL van jouw webshop, zodat de bestanden in de  
  **modules map** terecht komen)


## Configuratie

1. Ga naar het **admin** gedeelte van je webshop en installeer de plug-in via:  
   **Admin, Modules, Install new module**.  
   
2. Ga nu naar **Admin, Modules**.  
   Scroll naar het **Ubercart – Payment** gedeelte.

3. Vink de **Cardgate Payment Gateways** module aan.  
   Scroll naar beneden en klik op **Save configuration**.  
   
4. Ga naar het **admin** gedeelte van je webshop en selecteer **Admin, Store, Payment methods**.

5. Klik op de **CardGate settings** link.

6. Vul de **Site ID** en de **Hash Key (Codeersleutel)** in, deze kun je vinden bij **Sites** op [Mijn CardGate](https://my.cardgate.com/).

7. Vul de **standaard taal** in die je webshop gebruikt, en klik op **Save configuration**.

8. Vink bij **Payment methods** de betaalmethoden aan die je wenst te activeren.  
   **Let op:** Vink de **CardGate** betaalmethode **niet** aan, deze wordt alleen gebruikt voor de instellingen.
   
9. Klik op **Save configuration**.
   
10. Ga naar [Mijn CardGate](https://my.cardgate.com/), kies **Sites** en selecteer de juiste site.

11. Vul bij **Technische koppeling** de **Callback URL** in, bijvoorbeeld:  
    **http://mijnwebshop.com/?q=cart/cgp_response**  
   (Vervang **http://mijnwebshop.com** met de URL van je webshop)  

12. Zorg ervoor dat je na het testen bij de **CardGate settings** omschakelt van **Test Mode** naar **Live mode** en sla het op (**Save**).
    
## Vereisten

Geen verdere vereisten.