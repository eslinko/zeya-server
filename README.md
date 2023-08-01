# VH Lovestar Service

VH Lovestar is a service for work with Facebook events via [vh-lovestars-telegram-bot](https://github.com/skorikdeveloper/vh-lovestars).

## Setting up
1) Go to config folder.
```bash
cd common/config/
```
2) The vh-lovestars.sql file contains the database structure. Upload it to your hosting.
3) Copy main-local.php.example to main-local.php
```bash
copy cp main-local.php.example main-local.php
```
4) Fill in the required fields with your database connection data in main-local.php file. Fill telegram bot id and chat gpt api key
5) Fill mailer data in main-local.php file to receive mail
6) Fill in the fields in the params.php file with your data

## Work with google api for getting address and coordinates for event from image
1) Create a google console account
2) Add Geocoding API to account and get and copy Api Key
3) Add Cloud Vision API and get json file for use it\'s API
4) Create backend/web/api_keys and add json file to api_keys folder
5) Go to web folder and copy index.php.example to index.php
```bash
cd backend/web && copy cp main-local.php.example main-local.php
```
6) Define variable GoogleMapApiKey with Geocoding API Key and variable GoogleVisionFile with name of json file. And fill another needed api keys


## License

[GPL](https://www.gnu.org/licenses/gpl-3.0.html)