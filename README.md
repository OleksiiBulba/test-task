# Test task

This is a solution for a test task v21.0.5.

## Get started
### Requirements
* [Compose V2](https://docs.docker.com/compose/reference/) (docker compose CLI plugin, not a docker-compose V1);
* [make](https://www.gnu.org/software/make/)
* [yarn](https://classic.yarnpkg.com/lang/en/docs/install/#windows-stable) (optionally)

### Installation

Clone this repository

```shell
git clone git@github.com:OleksiiBulba/test-task.git .
```

Run make commands to build and start containers:
```shell
make build # can take ~5-15 minutes
make up
```

When the containers are built, you need to run composer install:
```shell
make install # or
make vendor
```
then yarn install and build js. You can use `yarn` on your system:
```shell
yarn install
yarn build # production
yarn dev # development
```
or use node container:
```shell
make yarn c='install'
make yarn c='build' # production
make yarn c='dev' # development
# -- or simpler --
make build-front # production
```

Also, you have to create `.env.local` file in the root of the project and copy there your rapid API key:
```dotenv
# .env.local
RAPID_API_KEY=<your_key_here>
```
 Refresh containers and/or cache:
```shell
make restart # stops & ups containers
make cc # symfony clear:cache command
```

After everything is built, installed and up, you can go to https://localhost/ and see the application running.
You also will see a link in the navigation menu to a maildev to see outgoing emails.

### Testing

To run tests:
```shell
make test
```
This command will run:
* Composer normalize command;
* Composer validate with strict command;
* Statics tests:
  * phpstan;
  * php-cs-fixer with dry run option;
* Unit and functional tests:
  * Clear test env cache;
  * phpunit with coverage option;

After phpunit coverage test you will find `html-coverage` folder, you can open `html-coverage/index.html` to check the project test coverage: its 100%.

To stop the project, run
```shell
make down
```
### Troubleshooting

To see what containers are running, run:
```shell
docker compose ps -a
```

In case of troubleshooting, you can see container logs:
```shell
docker compose logs <container>
```
Also all available commands are shown by 
```shell
make help
```

Task item list:

- [x] Display a form with elements: symbol, start date, end date and email;
- [x] Upon submit, validate the form on frontend and display validation error messages;
- [x] Upon submit, validate the form on backend and display validation error messages;
- [x] After the submitting, display on screen the historical quotes for the submitted Company Symbol in the given date range in the table format;
- [x] Table should have: Date | Open | High | Low | Close | Volume;
- [x] Based on the Historical data retrieved, display on screen a chart of the Open and Close prices;
- [x] Send to the submitted Email an email message, using as: Subject: the submitted company’s name, Body: Start Date and End Date;
- [x] Framework used is Symfony 6.2.*;
- [x] Tests present;
- [x] The user can select date range using similar to jQuery datepicker element: native `input[type="date"]`;
- [x] The chart library [anychart](https://www.anychart.com/) is used;
- [x] The project does not require to make changes to `/etc/hosts` or `C:\Windows\System32\drivers\etc\hosts` file, you just need to make sure nothing is running on port 443 and start the project, it should be available at https://localhost/ (you might need to [accept the auto-generated TLS certificate](https://stackoverflow.com/questions/7580508/getting-chrome-to-accept-self-signed-localhost-certificate/15076602#15076602) to open the page). Maildev should be available at http://localhost:8081/;
- [x] The project uses docker and compose for running (Symfony docker setup is used: https://github.com/dunglas/symfony-docker);
- [x] 100% phpunit tests coverage;
- [x] Heavily used DI so many parts can be decorated, adapted, replaced;
---
```text
Start: 2023-04-24 09:50AM (UTC+4)
End: 2023-04-26 01:47AM (UTC+4)
Duration: 39h 57m
```

# Author
Oleksii Bulba [oleksii_bulba@epam.com](mailto:oleksii_bulba@epam.com)