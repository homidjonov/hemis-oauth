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
    'urlResourceOwnerDetails' => 'https://univer.hemis.uz/oauth/api/user?fields=id,uuid,employee_id_number,type,roles,name,login,email,picture,firstname,surname,patronymic,birth_date,university_id,phone'
]);

/*$studentProvider = new \League\OAuth2\Client\Provider\GenericProvider([
    'clientId'                => '8',
    'clientSecret'            => 'Vt5dnZtzK_v3vzs0ycsV2uLzrh7zicZUrz4TEiOI',
    'redirectUri'             => 'http://hemis-oauth-test.lc/index.php',
    'urlAuthorize'            => 'https://student.hemis.uz/oauth/authorize',
    'urlAccessToken'          => 'https://student.hemis.uz/oauth/access-token',
    'urlResourceOwnerDetails' => 'https://student.hemis.uz/oauth/api/user'
]);*/


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

        echo "<pre>";
        print_r($resourceOwner->toArray());
        echo "</pre>";

    } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
        // Failed to get the access token or user details.
        exit($e->getMessage());
    }
    /* Result for Employee
     * Array
(
    [id] => 1
    [uuid] => aae32ae9-4b58-350b-f901-911fa8e1a6a6
    [employee_id_number] =>
    [type] => employee
    [roles] => Array
        (
            [0] => Array
                (
                    [code] => super_admin
                    [name] => Super Administrator
                )

        )

    [name] => Super Admin
    [login] => admin
    [email] => admin@hemis.uz
    [picture] => https://univer.hemis.uz/static/crop/2/1/120_120_90_2170006031.jpg
    [firstname] =>
    [surname] =>
    [patronymic] =>
    [birth_date] =>
    [university_id] => 999
    [phone] =>
)

    Result for Student

    Array
(
    [id] => 181
    [uuid] => 197a0e1d-da1a-01e3-2cfd-df0840653980
    [student_id_number] => 999211100098
    [type] => student
    [roles] => Array
        (
        )

    [name] => Talaba Test
    [login] => 999211100098
    [email] =>
    [phone] =>
    [picture] => https://univer.hemis.uz/static/crop/2/1/120_120_90_2170006031.jpg
    [firstname] => TALABA
    [surname] => TEST
    [patronymic] => XXX
    [birth_date] => 14-02-2022
    [university_id] => 999
    [groups] => Array
        (
            [0] => Array
                (
                    [id] => 62
                    [name] => Y_D 01 gurux
                    [curriculum] => Array
                        (
                            [id] => 48
                            [name] => Yuridika oquv reja dars uchun
                        )

                    [education_lang] => Array
                        (
                            [code] => 11
                            [name] => O‘zbek
                        )

                    [education_form] => Array
                        (
                            [code] => 11
                            [name] => Kunduzgi
                        )

                    [education_type] => Array
                        (
                            [code] => 11
                            [name] => Bakalavr
                        )

                )

        )

    [data] => Array
        (
            [first_name] => TALABA
            [second_name] => TEST
            [third_name] => XXX
            [full_name] => TEST TALABA XXX
            [short_name] => TEST T. X.
            [student_id_number] => 999211100098
            [image] => https://univer.hemis.uz/static/crop/2/1/320_320_90_2170006031.jpg
            [birth_date] => 1644796800
            [email] =>
            [phone] =>
            [gender] => Array
                (
                    [code] => 11
                    [name] => Erkak
                )

            [university] => HEMIS axborot tizimi universiteti
            [specialty] => Array
                (
                    [code] => 60420100
                    [name] => Yurisprudensiya (faoliyat turlari bo‘yicha)
                )

            [studentStatus] => Array
                (
                    [code] => 14
                    [name] => Bitirgan
                )

            [educationForm] => Array
                (
                    [code] => 11
                    [name] => Kunduzgi
                )

            [educationType] => Array
                (
                    [code] => 11
                    [name] => Bakalavr
                )

            [paymentForm] => Array
                (
                    [code] => 11
                    [name] => Davlat granti
                )

            [group] => Array
                (
                    [id] => 62
                    [name] => Y_D 01 gurux
                    [educationLang] => Array
                        (
                            [code] => 11
                            [name] => O‘zbek
                        )

                )

            [faculty] => Array
                (
                    [id] => 68
                    [name] => fakultet(yuridika dars uchun)
                    [code] => 999-117
                    [structureType] => Array
                        (
                            [code] => 11
                            [name] => Fakultet
                        )

                    [localityType] => Array
                        (
                            [code] => 11
                            [name] => Mahalliy
                        )

                    [parent] =>
                )

            [educationLang] => Array
                (
                    [code] => 11
                    [name] => O‘zbek
                )

            [level] => Array
                (
                    [code] => 11
                    [name] => 1-kurs
                )

            [semester] => Array
                (
                    [id] => 325
                    [code] => 11
                    [name] => 1-semestr
                    [current] =>
                    [education_year] => Array
                        (
                            [code] => 2021
                            [name] => 2021-2022
                            [current] =>
                        )

                )

            [address] => KOGON SHAHRI
            [country] => Array
                (
                    [code] => UZ
                    [name] => O‘zbekiston
                )

            [province] => Array
                (
                    [code] => 1726
                    [name] => Toshkent shahri
                    [_parent] => 1726
                )

            [district] => Array
                (
                    [code] => 1726262
                    [name] => Uchtepa tumani
                    [_parent] => 1726
                )

            [socialCategory] => Array
                (
                    [code] => 10
                    [name] => Boshqa
                )

            [accommodation] => Array
                (
                    [code] => 15
                    [name] => Talabalar turar joyida
                )

            [hash] => 31940425fa1c411af790b2ddb98985e294bab4f50091e2c8bff45e65ad3c572b
        )

)
     */
}
