![CardGate](https://cdn.curopayments.net/thumb/200/logos/cardgate.png)

# CardGate Modul für Drupal 7 Ubercart 3.x

## Support

Dieses Modul is geeignet für Ubercart version **3.x** 

## Vorbereitung

Um dieses Modul zu verwenden sind Zugangsdate zur **CardGate** notwendig.  
Gehen zu [My CardGate](https://my.cardgate.com/) und fragen Sie Ihre Zugangsdaten an, oder kontaktieren Sie Ihren Accountmanager.  

## Installation

1. Downloaden und entpacken Sie den aktuellsten [Source Code](https://github.com/cardgate/drupal-ubercart/releases/) auf Ihrem Desktop.

2. Uploaden Sie den **uc_cardgate** Ordner in Ihren **Drupal Modules** Ordner, welchen Sie hier finden können:  
   **http://mywebshop.com/htdocs/sites/all/modules/**  
   (Ersetzen Sie **http://mywebshop.com/** durch die URL von Ihrem Webshop.)

## Configuration

1. Gehen Sie zu **Admin, Modules**.  
   
2. Scrollen Sie zum **Ubercart-Payment** Bereich.

3. Wählen das **CardGate Payment Gateways Module** aus.  
   Scrollen Sie nach unten und klicken Sie auf **save configuration**. 

4. Gehen Sie zum **Adminbereich** Ihres Webshops und selektieren Sie **Admin, Store, Payment**.   

5. Klicken Sie auf den **CardGate settings** link.

6. Füllen Sie die **site ID** und den **hash key** ein, diesen können sie unter **Sites** auf [My CardGate](https://my.cardgate.com/) finden.

7. Füllen Sie die **Standardsprache** ein, die Sie in Ihrem Webshop verwenden und klicken Sie auf **Save configuration**.

8. Wählen sie bei **Payment methods** alle Zahlungsmittel aus, die aktivieren möchten.  
   Achtung: Wählen Sie das CardGate Zahlungsmittel **nicht** aus, diese wird lediglich für die Einstellungen gebraucht.

9. Klicken Sie auf **speichern**.
   
10. Gehen Sie zu [My CardGate](https://my.cardgate.com/), wählen Sie bei **Sites** die richtige Website aus.

11. Füllen Sie bei **Connection to the website** die **Callback URL** ein, zum Beispiel:  
    **http://mywebshop.com/?q=cart/cgp_response**  
    (Ersetzen Sie **http://mywebshop.com/** durch die URL von Ihrem Webshop.)

12. Sorgen Sie dafür, dass Sie **nach dem Testen** unter CardGate Einstellungen von dem **Test Mode** in den **Live Mode** umschalten und klicken Sie auf **speichern**.
    
## Anforderungen

Keine weiteren Anforderungen.