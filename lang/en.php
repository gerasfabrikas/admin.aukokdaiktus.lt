<?php

// please note: we can use unencoded characters like ö, é etc here as we use the html5 doctype with utf8 encoding
// in the application's header (in views/_templates/header.php)

// TODO: can someone please fix these ugly array keys ? they should look like "database_error", not "Database error"

$phplogin_lang = array(

// Login & Registration classes
'Database error'			=> 'Duomenų bazės klaida.',
'Empty username'			=> 'Paskyros vardo laukelis tuščias',
'Username exist'			=> 'Paskyros vardas užimtas. Pasirinkite kitą',
'Invalid username'			=> 'Paskyros vardas neatitinka taisyklių: tik a-Z ir skaičiai, nuo 2 iki 64 simbolių',
'Empty password'			=> 'Slaptažodžio laukelis tuščias',
'Bad confirm password'		=> 'Slaptažodis ir pakartotinai įvestas slaptažodis nesutampa',
'Password too short'		=> 'Slaptažodį turi sudaryti mažiausiai 6 simboliai',
'Email exist'				=> 'Šis el. pašto adresas jau užregistruotas',
'Invalid email'				=> 'Blogas el. pašto adreso formatas',

// Registration class
'Wrong captcha'				=> 'Apsaugos nuo robotų klaida',
'Username bad length'		=> 'Paskyros vadas turi būti ne trumpesnis kaip 2 simboliai ir ne ilgesnis kaip 64',
'Empty email'				=> 'El. pašto laukelis turi būti užpildytas',
'Email too long'			=> 'El. pašto adresas negali būti ilgesnis kaip 64 simboliai',
'Verification mail error'	=> 'Negalėjome išsiųsti aktyvavimo nuorodos. Paskyra nesukurta. Atsiprašome',
'Verification mail sent'	=> 'Paskyra sukurta, o aktyvavimo nuoroda išsiųsta jūsų nurodytu paštu. Paspauskite šią nuorodą, kad aktyvuotumėte paskyrą',
'Verification mail not sent'=> 'Aktyvavimo nuoroda neišsiųsta! Klaida: ',
'Registration failed'		=> 'Registracija nepavyko. Pabandykite dar kartą',
'Activation successful'		=> 'Paskyra aktyvuota. Galite prisijungti',
'Activation error'			=> 'Paskyros aktyvavimo klaida',

// Login class
'Invalid cookie'			=> 'Blogas slapukas',
'User not exist'			=> 'Prisijungti nepavyko',
'Wrong password'			=> 'Prisijungti nepavyko',
'Account not activated'		=> 'Paskyra neaktyvuota. Paspauskite aktyvavimo nuorodą, kurią gavote el. paštu',
'Logged out'				=> 'Atsijungėte nuo sistemos.',
'Same username'				=> 'Paskyros vardas yra toks pats, kaip ir dabartinis. Pasirinkite kitą',
'Same email'				=> 'El. pašto adresas yra toks pats, kaip ir dabartinis. Pasirinkite kitą',
'Username changed'			=> 'Paskyros vardas pakeistas. Dabar jis yra ',
'Username change failed'	=> 'Paskyros vardo pakeitimas nepavyko. Atsiprašome',
'Email changed'				=> 'El. pašto adresas pakeistas. Dabar jis yra ',
'Email change failed'		=> 'El. pašto adreso pakeitimas nepavyko. Atsiprašome',
'Password changed'			=> 'Slaptažodis pakeistas',
'Password changed failed'	=> 'Slaptažodžio pakeitimas nepavyko. Atsiprašome',
'Wrong old password'		=> 'Blogas senasis slaptažodis.',
'Password mail sent'		=> 'Slaptažodžio atkūrimo nuoroda išsiųsta',
'Password mail not sent'	=> 'Slaptažodžio atkūrimo nuoroda neišsiųsta. Klaida: ',
'Reset link has expired'	=> 'Nuoroda pasenusi. Slaptažodžio atkūrimo nuorodą panaudokite per vieną valandą.',
'Empty link parameter'		=> 'Empty link parameter data.',

// Login form
'Username'					=> 'Username',
'Password'					=> 'Password',
'Remember me'				=> 'Keep me logged in (for 2 weeks)',
'Log in'					=> 'Log in',
'Register new account'		=> 'Register new account',
'I forgot my password'		=> 'I forgot my password',

// Register form
'Register username'			=> 'Username (only letters and numbers, 2 to 64 characters)',
'Register email'			=> 'User\'s email (please provide a real email address, you\'ll get a verification mail with an activation link)',
'Register password'			=> 'Password (min. 6 characters!)',
'Register password repeat'	=> 'Password repeat',
'Register captcha'			=> 'Please enter those characters',
'Register'					=> 'Register',
'Back to login'				=> 'Back to Login Page',

// Password reset request
'Password reset request'	=> 'Request a password reset. Enter your username and you\'ll get a mail with instructions:',
'Reset my password'			=> 'Reset my password',
'New password'				=> 'Naujas slaptažodis',
'Repeat new password'		=> 'Pakartokite slaptažodį',
'Submit new password'		=> 'Keisti',

// Edit account
'Edit title'				=> 'You are logged in and can edit your credentials here',
'Old password'				=> 'Senas slaptažodis',
'New username'				=> 'New username (username cannot be empty and must be azAZ09 and 2-64 characters)',
'New email'					=> 'New email',
'currently'					=> 'currently',
'Change username'			=> 'Change username',
'Change email'				=> 'Change email',
'Change password'			=> 'Keisti',

// Logged in
'You are logged in as'		=> 'You are logged in as ',
'Logout'					=> 'Logout',
'Edit user data'			=> 'Edit user data',
'Profile picture'			=> 'Your profile picture (from gravatar):'

);