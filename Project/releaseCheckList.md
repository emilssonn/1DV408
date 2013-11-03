1. Kontrollera att servern har PHP 5.3 eller högre.
2. Skapa en MYSQL databas med tabeller för projektet, använda InnoDB för alla tabeller.
3. Databasen ska ha referensintegritet mellan ID:s med DELETE CASCADE och UPDATE NO ACTION
4. Ladda upp filerna
5. Sätt filrättigheter 755 på alla filer
6. Fyll i databasuppgifter i filen projectConfig.php
7. Denna fil ska ligga i en mapp över webbroten. Om denna map inte är tillgänglig:
	Lägg filen i projektets root mapp. Ändra sökenvägen till filen i index.php:
		dirname(__FILE__)."/../projectConfig.php" till "./projectConfig.php"
8. Ändra session_set_cookie_params domän parameter till rätt domän.