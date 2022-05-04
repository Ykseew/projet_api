# Test API REST Link Part

API Rest sécurisé pour la société DURAND SAS 

## Installation
```bash
composer install
php bin/console doctrine:database:create
php bin/console make:migration
php bin/console doctrine:migrations:migrate
php bin/console lexik:jwt:generate-keypair
php bin/console server:run
```

- Créer un utilisateur avec la requête /register sur le localhost avec le body suivant en remplaçant les données email et password
```json
{
    "email":"email",
    "password":"password"
}
```
- Lancer postman executer la requête /login_check sur le localhost pour récupérer le token avec le body
```json
{
    "username":"email",
    "password":"password"
}
```
- Mettre le token dans l'onglet "Authorization" avec le type "Bearer Token"

## Utilisation



- Pour créer une machine :
```json
POST /machines 
```
avec le body; en remplaçant "machine" et "description" par le nom et la description de la machine
```json
{
    "name":"machine",
    "description":"description"
}
```

- Pour modifier une machine qui appartient à l'utilisateur
```json
PATCH /machines 
```
avec le body, en remplaçant "machine" et "description" par le nom et la description de la machine
```json
{
    "name":"machine",
    "description":"description"
}
```
- Pour récupérer toutes les machines de l'utilisateur
```json
GET /machines 
```

- Pour récupérer une machine de l'utilisateur (si celle-ci lui appartient)
 ```json
GET /machines/{id}
```
- Pour supprimer une machine de l'utilisateur
```json
DELETE /machines/{id}
```

