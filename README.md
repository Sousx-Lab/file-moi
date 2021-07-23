# File Share

A free project to share files, inspired by zippyshare.com

## Screenshot

![App Screenshot](https://i.postimg.cc/nzkG2GmG/file-share.jpg)

## Deployment

Installing dependencies:

```bash
  composer install
```
##### This project uses Mysql database and MailHog to emulate an smtp server.
You can edit the **docker-compose.yml** file to change the database password and username.
This command also installs **Adminer**, you can remove it if you don't want to use it.
```bach
  docker-compose up
```
### Environment Variables:

setup the environment variables for database connexion, you will need to add the following environment variables to your ***.env*** file.  
if you have a database already installed and configured, change the ```"root:root"```password and username.

#### DATABASE:
`DATABASE_URL="mysql://root:root@127.0.0.1:3306/fileshare?serverVersion=8.0"`

Create database:
  ```bash
   php bin/console doctrine:database:create 
  ```

Migrate the database:
```bash
 php bin/console doctrine:migrations:migrate 
```
#### SMTP Server:
Default port MailHog server ```1025```.  
`MAILER_DSN=smtp://localhost:1025?encryption=null`   


### Symfony Messenger
 File deletion is handled asynchronously with ***Symfony Messenger***   
 run this command and choose ***async*** to start for it :

```bash
  php bin/console messenger:consume -vv 
 ```
