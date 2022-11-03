HEMIS Universitet tizimi bilan oAuth integratsiya
----------------------

HEMIS Universitet tizimida oAuth server moduli yaratilgan. Buning
yordamida foydalanuvchilar HEMIS Universitet tizimidagi
talaba yoki hodim akkaunti yordamida boshqa tizimlarda registratsiya
yoki avtorizatsiya qilishlari mumkin. oAuth modul [oAuth2](https://oauth.net/2/) 
standartida ishlaydi.

Klient sozlamalari
------------------

Klient sozlamalari HEMIS Universitet tizimi boshqaruv panelidagi **Tizim / oAuth klientlar** sahifasida
yaratiladi. Bunda klient nomi, qaysi web addressdan murojaat qilishi (URL manzil) kiritish 
talab qilinadi. Bir nechta URL manzil vergul orqali kiritiladi. Klent sozlamalari saqlangach
**Klient ID** va **Klient maxfiy kodi** hosil bo'ladi. Ushbu ma'lumotlardan integratsiya
qilinayotgan web ilovadagi oAuth klient yaratishda foydalaniladi.

Eslatma
-------
oAuth avtorizatsiya HEMIS Universitet tizimining hodimlari va talabalari akkauntlari
uchun ishlaydi. Klient sozlamalarida **URL manzil** mos ravishda hodim yoki talaba kabineti yoxud
ikkilasi uchun berilishi lozim. 

Coding
------

Quyida PHPda namunaviy integratsiya keltirilgan:

```
<?php
session_start();
// Autoload files using the Composer autoloader.
require_once __DIR__ . '/../vendor/autoload.php';
$employeeProvider = new \League\OAuth2\Client\Provider\GenericProvider([
    'clientId'                => '8',
    'clientSecret'            => 'Vt5dnZtzK_v3vzs0ycsV2uLzrh7zicZUrz4TEiOI',
    'redirectUri'             => 'http://hemis-oauth-test.lc/index.php',
    'urlAuthorize'            => 'https://univer.hemis.uz/oauth/authorize',
    'urlAccessToken'          => 'https://univer.hemis.uz/oauth/access-token',
    //available fields: id,uuid,university_id,type,firstname,surname,patronymic,login,picture,email,phone,birth_date
    'urlResourceOwnerDetails' => 'https://univer.hemis.uz/oauth/api/user?fields=id,uuid,type,name,login,picture,email,university_id,phone'
]);
/*
$studentProvider = new \League\OAuth2\Client\Provider\GenericProvider([
    'clientId'                => '8',
    'clientSecret'            => 'Vt5dnZtzK_v3vzs0ycsV2uLzrh7zicZUrz4TEiOI',
    'redirectUri'             => 'http://hemis-oauth-test.lc/index.php',
    'urlAuthorize'            => 'https://student.hemis.uz/oauth/authorize',
    'urlAccessToken'          => 'https://student.hemis.uz/oauth/access-token',
    'urlResourceOwnerDetails' => 'https://student.hemis.uz/oauth/api/user?fields=id,uuid,type,name,login,picture,email,university_id,phone'
]);
*/

// If we don't have an authorization code then get one
if (!isset($_GET['code'])) {
    if (isset($_GET['start'])) {
        // Fetch the authorization URL from the provider; this returns the
        // urlAuthorize option and generates and applies any necessary parameters
        // (e.g. state).
        $authorizationUrl = $employeeProvider->getAuthorizationUrl();

        // Get the state generated for you and store it to the session.
        $_SESSION['oauth2state'] = $employeeProvider->getState();

        // Redirect the user to the authorization URL.
        header('Location: ' . $authorizationUrl);
        exit;
    } else {
        echo "<a href='/?start=1'>Authorize with HEMIS</a>";
    }
// Check given state against previously stored one to mitigate CSRF attack
} else if (empty($_GET['state']) || (isset($_SESSION['oauth2state']) && $_GET['state'] !== $_SESSION['oauth2state'])) {

    if (isset($_SESSION['oauth2state'])) {
        unset($_SESSION['oauth2state']);
    }

    exit('Invalid state');

} else {
    try {
        // Try to get an access token using the authorization code grant.
        $accessToken = $employeeProvider->getAccessToken('authorization_code', [
            'code' => $_GET['code']
        ]);

        // We have an access token, which we may use in authenticated
        // requests against the service provider's API.
        echo "<p>Access Token: <b>{$accessToken->getToken()}</b></p>";
        echo "<p>Refresh Token: <b>{$accessToken->getRefreshToken()}</b></p>";
        echo "Expired in: <b>" . date('m/d/Y H:i:s', $accessToken->getExpires()) . "</b></p>";
        echo "Already expired: <b>" . ($accessToken->hasExpired() ? 'expired' : 'not expired') . "</b></p>";

        // Using the access token, we may look up details about the
        // resource owner.
        $resourceOwner = $employeeProvider->getResourceOwner($accessToken);

        foreach ($resourceOwner->toArray() as $key => $value) {
            echo "<p>$key: <b>$value</b></p>";
        }

    } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
        // Failed to get the access token or user details.
        exit($e->getMessage());
    }
}```



