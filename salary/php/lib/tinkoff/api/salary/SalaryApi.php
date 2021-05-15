<?php
/**
 * SalaryApi
 * PHP version 7.2
 *
 * @category Class
 * @package  OpenAPI\Client
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 */

/**
 * Tinkoff Business OpenApi
 *
 * # Введение   **Tinkoff Business OpenApi** помогает автоматизировать бизнес-процессы с помощью интеграции ваших сервисов с сервисами Тинькофф: получение выписки со счетов, выставление счета, совершение платежей и других.   Два способа подключения:   [Партнерский сценарий](#section/Partnerskij-scenarij) - чтобы подключить данные из Тинькофф Бизнеса к другим сервисам.   [Селф-Сервис](#section/Scenarij-Self-Servis) - чтобы получить доступ через API к своей компании в Тинькофф Бизнесе.  # Терминология  * **Access Token** — токен, который дает доступ до методов Tinkoff Business OpenApi. Для партнерского сценария, токен будет активен (т.е. по нему можно будет вызывать методы) 30 минут: потом его надо будет переполучить. Для сценария \"Селф-Сервис\" токен будет активен 1 год. * **Refresh Token** — токен, активный 1 год, который дает возможность получить новый Access Token, если он устарел, чтобы восстановить доступ до методов Tinkoff Business OpenApi. * **redirect_uri** — uri для перехода, который вы указываете при регистрации партнера. На него будет произведен редирект для получения Access и Refresh токенов. Подробнее про формат запросов, который должен обрабатывать эндпоинт на этом uri, можно почитать [здесь.](#section/Partnerskij-scenarij/Opisanie-processa-avtorizacii)   * **client_id** - уникальный идентификатор партнера, которые вы получаете при регистрации   * **scope** - механизм, [используемый в OAuth 2.0](https://oauth.net/2/scope/), который ограничивает права доступа вашего приложения к пользовательским данным. Выпущенный в личном кабинете токен будет ограничен выбранными в меню доступами.  # Авторизация в Tinkoff Id  **Tinkoff Id** — единая точка авторизации для всего Тинькофф банка, работающая по протоколам [**OAuth 2.0**]( https://oauth.net/2/) и **[OpenID Connect](https://openid.net/connect/)**. Авторизация для всех методов Tinkoff Business OpenApi работает путем отправки Access Token в заголовке запроса.  # Партнерский сценарий  ## Регистрация   Для начала работы с Tinkoff Business OpenApi в качестве партнера заполните заявку на подключение на [этой странице](https://www.tinkoff.ru/business/open-api/). После рассмотрения вашей заявки вы получите **client_id** и пароль.  ## Описание процесса авторизации  В процессе регистрации вас попросят указать redirect_uri. Вам понадобится создать эндпоинт, доступный по ```redirect_uri```, который заканчивает процесс авторизации, путем обмена кода на Access и Refresh токены. Для удобства, здесь и далее, он будет лежать на  ```https://myintegration.ru/auth/complete```, где ```https://myintegration.ru``` - это ваше приложение.  Описание процесса авторизации:  1. Клиент начинает процесс авторизации.  2. Вам понадобится сгенерировать параметр state и связать его с браузером пользователя для защиты от CSRF-атак.   `state` параметр служит для повышения надежности процесса авторизации. Правильное его использование повышает безопасность вашей интеграции с `Tinkoff Business OpenApi`. Почитать про лучшие практики использования `state` можно [здесь.](https://tools.ietf.org/html/draft-ietf-oauth-security-topics-14#section-4.7) 3. Вызвать метод ```GET https://id.tinkoff.ru/auth/authorize``` со следующими параметрами: * client_id: идентификатор, полученный при регистрации партнера * redirect_uri: ```https://myintegration.ru/auth/complete``` * state: строка из пункта 2 * response_type: ```code``` * scope_parameters: URL закодированный json с ИНН и КПП компании клиента. Эти данные необходимо для того, чтобы уметь однозначно определять к какой именно компании давать доступ клиенту. Если у компании клиента нет КПП, передайте вместо него \"0\". Пример JSON до кодирования:   ``` {      \"inn\" : \"7743180892\",      \"kpp\" : \"773101001\"  }  ```  Пример запроса:  ``` GET https://id.tinkoff.ru/auth/authorize?client_id=%client_id%&redirect_uri=https://myintegration.ru/auth/complete&state=ABCxyz&response_type=code&scope_parameters=%20%7B%20%22inn%22%20:%20%227743180892%22,%20%22kpp%22%20:%20%22773101001%22%20%7D ```  4. Клиенту откроется окно с выбором доступов:  ![](https://business.cdn-tinkoff.ru/static/images/opensme/consents-pop-up.jpg)   Если клиент поставит галочки напротив всех полей в списке и нажмет \"Продолжить\", при следующих авторизациях это окно открываться не будет.  После нажатия кнопки \"Продолжить\", запрос закончится редиректом на ```https://myintegration.ru/auth/complete```.  5. На ```https://myintegration.ru/auth/complete``` придет запрос вида:  ``` https://myintegration.ru/auth/complete?state=ABCxyz&code=c.1aGiAXX3Ni&session_state=hU7GNp0Y3kgs3nx0H3RTj3JzCSrdaqaDhU6lS87eiUw.i4kl6dsEB1SQogzq0Nj0 ```   Далее вам нужно провалидировать параметр state. Если все правильно, то вам надо забрать строку из параметра ```code```  для того, чтобы обменять ее на Access и Refresh токены.  6. Для того, чтобы получить Access и Refresh токены, вам нужно вызвать метод ```POST https://id.tinkoff.ru/auth/token```.  Формат запроса:  * Authorization: Basic, где username и password соответствуют client-id и паролю из пункта 2. [Примеры составления в разных языках](https://gist.github.com/brandonmwest/a2632d0a65088a20c00a) * Content-type: ```application/x-www-form-urlencoded``` * grant_type: ```authorization_code``` * redirect_uri: ```https://myintegration.ru/auth/complete``` * code: код из пункта 5.  Пример запроса: ```  curl -X POST \\      https://id.tinkoff.ru/auth/token \\      -H 'Authorization: Basic ' \\      -H 'Content-Type: application/x-www-form-urlencoded' \\      -d 'grant_type=authorization_code&redirect_uri=https://myintegration.ru/auth/complete&code=c.1aGiAXX3Ni' ``` Пример ответа: ``` {     \"access_token\": \"t.mzucgRIDwalMAQ_at2Y2kmJB6yhNAQlWMNucp3w45xMcKknxWyl_8sWkkp5_3Nq8i_UvddDroJvd3elz-QH5hQ\",     \"token_type\": \"Bearer\",     \"expires_in\": 1791, // Время жизни токена в секундах     \"refresh_token\": \"LShO9uuTkeWqozxO8WP2eGsJpLBQQ-3QBGYUeNzUv4LTtjudU6zPofXbiMwToznuCOLv65tpCJn04fsLvsYH2Q\" } ```  7. После того как вы получили токен, мы рекомендуем вам проверить, что клиент предоставил нужные доступы и что вы правильно передали данные в scope_parameters. Для того, чтобы это сделать, вам нужно вызвать метод ```POST https://id.tinkoff.ru/auth/introspect``` и проверить данные в поле scope в ответе:  Формат запроса:   * Authorization: Basic, где username и password соответствуют client-id и паролю из пункта 2. [Примеры составления в разных языках](https://gist.github.com/brandonmwest/a2632d0a65088a20c00a) * Content-type: ```application/x-www-form-urlencoded``` * token — тело токена, полученного на предыдущем шаге.  Пример запроса: ```  curl -X POST \\      https://id.tinkoff.ru/auth/introspect \\      -H 'Authorization: Basic ' \\      -H 'Content-Type: application/x-www-form-urlencoded' \\      -d 'token=t.mzucgRIDwalMAQ_at2Y2kmJB6yhNAQlWMNucp3w45xMcKknxWyl_8sWkkp5_3Nq8i_UvddDroJvd3elz-QH5hQ' ```  Пример ответа: ``` {    \"active\":true,    \"scope\":[       \"device_id\",       \"opensme/inn/[7743180892]/kpp/[773101001]/payments/draft/create\"       \"opensme\"    ],    \"client_id\":\"opensme\",    \"token_type\":\"access_token\",    \"exp\":1585728196,    \"iat\":1585684996,    \"sub\":\"2f1d967c-8596-4a1d-9bc5-b448a07e15ba\",    \"aud\":[      \"ibsme\",      \"companyInfo\"    ],    \"iss\":\"https://id.tinkoff.ru/\" } ```  В ответе вас интересует поле scope — в нем содержится список доступов, которые предоставил пользователь. В этом поле вам надо найти нужный для вас доступ. Для каждого метода в Tinkoff Business OpenApi в описании вы можете найти шаблон доступа, который должен присутствовать в поле scope, чтобы метод отработал успешно. По токену, который вы получили в предыдущем шаге, можно создавать черновики платежных поручений по компании с инн ```7743180892``` и кпп ```773101001```  (потому что есть доступ ```opensme/inn/[7743180892]/kpp/[773101001]/payments/draft/create```), но по этому токену нельзя посмотреть информацию по компании (так как нет доступа ```opensme/inn/[7743180892]/kpp/[773101001]/company-info/get```). Проверяйте и наличие доступов и правильность переданных данных.  ## SDK **SDK** - готовые реализации API Tinkoff ID для наиболее распространенных платформ. Использование SDK упрощает разработку и сокращает время на интеграцию ваших мобильных приложений.   Подробная информация выложена в соответствующие репозитории по ссылкам ниже:  * [Android SDK](https://github.com/tinkoff-mobile-tech/TinkoffID-Android)  * [iOS SDK](https://github.com/tinkoff-mobile-tech/TinkoffID-iOS)     Для ознакомления со стайлгайдами перейдите по [ссылке](https://www.figma.com/file/TsgXOeAqFEePVIosk0W7kP/Tinkoff-ID?node-id=16%3A723). # Сценарий Селф-Сервис  ## Общие сведения  Селф-сервис в Tinkoff Business OpenApi позволяет вам выпускать долгоживущий (до одного года) Access Token в личное пользование для своей компании. В данный момент, доступ до выпуска токена есть только у директоров компаний, у которых подключен продукт \"Счета и Платежи\". **Внимание: не передавайте данный токен третьим лицам, так как они могут получить доступ ко всей финансовой деятельности вашей компании.**  ## Получение токена  Что нужно для того, чтобы получить токен:  1. Откройте меню. 2. Выберите *Интеграции*:    ![](https://business.cdn-tinkoff.ru/static/images/opensme/menu.png) 3. Нажмите на кнопку *Подключить* в блоке \"Интеграция с Тинькофф по API\":    ![](https://business.cdn-tinkoff.ru/static/images/opensme/integrations.png) 4. Откроется следующий экран:    ![](https://business.cdn-tinkoff.ru/static/images/opensme/token-ip-scope-form.png) 5. Введите IP адреса, с которых вы будете делать запросы в Tinkoff Business OpenApi. Для того чтобы ввести более одного ip адреса,введите их через запятую.  6. В выпадающем списке выберите, к чему можно будет получить доступ по выпущенному токену:    ![](https://business.cdn-tinkoff.ru/static/images/opensme/token-scope-form.png) 7. Нажмите на кнопку \"Выпустить токен\". 8. На экране отобразится выпущенный токен:    ![](https://business.cdn-tinkoff.ru/static/images/opensme/token-revealed.jpg) 9. В этом окне вы сможете скопировать свой Access Token. **Учтите, что этот токен будет работать в контексте той компании, которая была выбрана в разделе \"Интеграции\" на момент выпуска токена**.  ## Отзыв токена  Если вы хотите деактивировать токен, вам понадобится:  1. Выбрать в шапке пункт [\"Личный Кабинет\"](https://business.tinkoff.ru/account). Откроется такая панель:    ![](https://business.cdn-tinkoff.ru/static/images/opensme/control-panel.png) 2. В ней нажать на [\"Настройки профиля\"](https://business.tinkoff.ru/account/settings/security/login) 3. Прокрутить до блока с названием \"Приложения, у которых есть доступ к аккаунту\".      ![](https://business.cdn-tinkoff.ru/static/images/opensme/Token-tab.png) 4. Выпущенные для селф-сервиса токены будут иметь название \"Tinkoff Business OpenApi\". Открыв вкладку с таким токеном, его можно отозвать нажав на кнопку \"Удалить доступ\". # Работа с полученными токенами  ## Работа с Access токеном  Для того, чтобы использовать Access Token, добавьте заголовок Authorization со значением ```Bearer %token%```.  Пример использования: ``` curl -X GET \\   https://business.tinkoff.ru/openapi/api/v1/company \\   -H 'Authorization: Bearer t.L4c14_rCVNbXueBPC2dFJ9fNqk9BnQuRGGz2fztHpwlFGLYxWNfTI0u_CZPEd0dMWCt0kA9P6TUgToC2_BRT7g' \\   -H 'Content-Type: application/json' \\ ```   ## Работа с Refresh токеном  Для того, чтобы, имея Refresh Token, получить новые Access и Refresh токены, нужно вызвать метод ```POST https://id.tinkoff.ru/auth/token```.  Формат запроса:   * Authorization: Basic, где username и password соответствуют client-id, которые были получены после регистрации.  [Примеры составления в разных языках](https://gist.github.com/brandonmwest/a2632d0a65088a20c00a) * Content-type: ```application/x-www-form-urlencoded``` * grant_type: ```refresh_token``` * refresh_token: в это поле передать ваш токен.  Пример запроса: ``` curl -X POST \\   https://id.tinkoff.ru/auth/token \\   -H 'Content-Type: application/x-www-form-urlencoded' \\   -d 'grant_type=refresh_token&refresh_token=LShO9uuTkeWqozxO8WP2eGsJpLBQQ-3QBGYUeNzUv4LTtjudU6zPofXbiMwToznuCOLv65tpCJn04fsLvsYH2Q' ```  Пример ответа: ``` {     \"access_token\": \"t.mzucgRIDwalMAQ_at2Y2kmJB6yhNAQlWMNucp3w45xMcKknxWyl_8sWkkp5_3Nq8i_UvddDroJvd3elz-QH5hQ\",     \"token_type\": \"Bearer\",     \"expires_in\": 1791, //Время жизни токена в секундах     \"refresh_token\": \"WvcsjowaPtv1t8r4KDeTyRk89NsSK0lTczBt8CqUSHyx3Xbh7SaWAsGhNIBEHBwqng8l2UZtBFeJCQL0GQrfoG\" } ```  **Внимание**  Убедитесь, что ваши обращения выполняются с актуальным Refresh token.  Если при обращении с Refresh token получена ошибка ```invalid_grant```, то срок действия токена истек, или он был отозван пользователям.  После получения ошибки ```invalid_grant``` важно не производить дальнейшие вызовы обмена токена, так как данный токен уже инвалидирован.  Для дальнейшей работы пользователю нужно повторно пройти авторизацию.  # Сертификаты В качестве дополнительной защиты на методах, расположенных на  [secured-openapi.business.tinkoff.ru](https://secured-openapi.business.tinkoff.ru),  используется [Mutual TLS](https://en.wikipedia.org/wiki/Mutual_authentication). Эти методы имеют в описании символ 🔒. Для использования Mutual TLS потребуется выпустить сертификат от Тинькофф. Срок действия сертификата - **1 год**.   ## Выпуск сертификата * Для выпуска сертификата потребуется сгенерировать приватный ключ  и [CSR](https://en.wikipedia.org/wiki/Certificate_signing_request). Для этого нужно:   * На компьютере иметь установленный [openssl](https://www.openssl.org/)   * Скачать [конфигурацию для генерации ключа](/openapi/downloads/config.cfg)   * Запустить в консоли `openssl req -new -config config.cfg -newkey rsa:2048 -nodes -keyout private.key -out request.csr`   и ввести ответы на соответствующие вопросы ```bash localhost:~$ openssl req -new -config config.cfg -newkey rsa:2048 -nodes -keyout private.key -out request.csr Generating a 2048 bit RSA private key .. .. writing new private key to 'private.key' You are about to be asked to enter information that will be incorporated into your certificate request. What you are about to enter is what is called a Distinguished Name or a DN. There are quite a few fields but you can leave some blank For some fields there will be a default value, If you enter '.', the field will be left blank. Country Name (RU) []:RU State or Province - субъект федерации, где зарегистрирована организация (например, MOSKVA  или KRASNOYARSKIJ KRAJ) []:KRASNOYARSKIJ KRAJ Locality Name - город, населенный пункт или муниципальное образование (например, MOSKVA или KRASNOYARSK) []:MOSKVA Organization Name - cокращенное наименование организации в соответствии с Выпиской из ЕГРЮЛ []:IP IVANOV IVAN IVANOVICH Organization Unit Name - наименование отдела (может отсутствовать) []: Сокращенное наименование организации в соответствии с Выпиской из ЕГРЮЛ (ФИО для ИП) []:IVANOV IVAN IVANOVICH Email - адрес электронной почты уполномоченного лица []:ivanov@email.com   Please enter the following 'extra' attributes to be sent with your certificate request A challenge password []: ```      <details>       <summary markdown=\"span\">         Для ввода ответов на вопросы используйте следующие правила транслитерации:       </summary>      <sup>    |А|Б|В|Г|Д|Е|Е|Ж|З|И|Й|К|Л|М|Н|О| |---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---| |A|B|V|G|D|E|E|ZH|Z|I|I|K|L|M|N|O|     |П|Р|С|Т|У|Ф|Х|Ц|Ч|Ш|Щ|Ы|Ъ|Э|Ю|Я| |---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---| |P|R|S|T|U|F|KH|TS|CH|SH|SHCH|Y|IE|E|IU|IA|        </sup>     </details>   ---    * В папке, в которой запускали генерацию, появятся файлы `request.csr` и    `private.key`. `private.key`<a name=\"secret\"></a> - секрет, с помощью которого будут подписываться запросы.   `request.csr` потребуется в заявлении на выпуск сертификата (далее) * Далее требуется заполнить [заявку на выпуск сертификата](/openapi/downloads/Заявка%20на%20SSL%20сертификат.docx)   * Заполнение должно соответствовать выпущенному `CSR`, сгенерированному шагом выше   * В поле `CSR-запрос на сертификат` вставить содержимое файла `request.csr` (можно открыть обычным блокнотом)   * Заявка должна уместиться на одну страницу * Cкан распечатанной и подписанной заявки, Word-файл с Заявкой, а также CSR-запрос (`request.csr`) нужно отправить  письмом на электронную почту openapi@tinkoff.ru. Письмо должно прийти с email директора компании, который был указан при регистрации в Тинькофф Бизнес.  В течение двух рабочих дней ответным письмом придет файл сертификата <a name=\"certificate\"></a>.  В случае ошибок в заявке или CSR-запросе ответным письмом придет письмо с описанием ошибок.  ## Использование сертификата  Полученный сертификат [`open-api-cert.pem`](#certificate) дает возможность использовать методы защищенные  [Mutual TLS](https://en.wikipedia.org/wiki/Mutual_authentication), которые расположены на [secured-openapi.business.tinkoff.ru](https://secured-openapi.business.tinkoff.ru). Чтобы сделать такой запрос Вам понадобится, помимо сертификата, сгенерированный ранее [секрет](#secret).  Сделать запрос можно разными способами. Например: * Через curl. Для этого используя [полученный файл](#certificate) и [секрет](#secret) выполните запрос:   `curl --cert open-api-cert.pem --key private.key https://secured-openapi.business.tinkoff.ru/...` * Через HTTP-клиент. Чтобы сделать такой запрос воспользуйтесь документацией используемого клиента.  Ниже приведено несколько примеров:   * Java - [Async Http Client](https://asynchttpclient.github.io/async-http-client/ssl.html)   * PHP - Guzzle 6 ([cert](http://docs.guzzlephp.org/en/stable/request-options.html#cert) и    [ssl-key](http://docs.guzzlephp.org/en/stable/request-options.html#ssl-key))   * Python - [Requests](https://requests.readthedocs.io/en/master/user/advanced/#client-side-certificates)   * JavaScript - пример на [Axios](https://github.com/axios/axios):   ```js   const fs = require('fs');   const https = require('https');   const axios = require('axios');   // ...   const httpsAgent = new https.Agent({       cert: fs.readFileSync('open-api-cert.pem'),       key: fs.readFileSync('private.key')   });   // ...   const result = await axios.get('https://secured-openapi.business.tinkoff.ru/...', { httpsAgent });   ``` # Трейсинг в Tinkoff Business OpenApi  В ответе каждого запроса к Tinkoff Business OpenApi будет лежать заголовок ```X-Request-Id``` — уникальный идентификатор запроса. Для диагностики работы вашего приложения, этот идентификатор с вашей стороны стоит сохранять или логировать. # Песочница  [Песочница](/openapi/sandbox/docs) предоставляет возможность воспользоваться методами openApi в ознакомительном режиме.  Действия с песочницей не воздействуют на реальные данные. В данной версии песочницы вам не нужно получать токен.  Функциональность песочницы находится в доработке, поэтому возможны достаточно частые изменения ее домена и префиксов путей. # Обратная связь  Если во время работы с Tinkoff Business OpenApi у вас возникли  вопросы, смело задавайте их путем отправки письма на openapi@tinkoff.ru. Если вы хотите предложить улучшения, создавайте issue в нашем [гитхаб репозитории](https://github.com/TinkoffCreditSystems/business-openapi). Информация по релизам будет в [телеграм канале](https://t.me/TinkoffBusinessOpenApi). # Release Notes  ## 26 апреля 2021 ### Компании #### GET /api/v1/company * [Смена юридического адреса Тинькофф Банка](https://www.tinkoff.ru/about/news/19032021-tinkoff-notifies-change-legal-address/).  ## 22 апреля 2021 ### Платежи #### POST /api/v1/payment/ruble-transfer/pay * Метод создания платежа поддерживает налоговые платежи и налоговые платежи за третьих лиц.  ## 5 апреля 2021 ### Бизнес-карты * Добавлены [методы для перевыпуска виртуальных карт](#operation/postApiV1CardVirtualReissue):   * POST /api/v1/card/virtual/reissue   * GET  /api/v1/card/virtual/reissue/result  ## 2 апреля 2021 ### Счета и выписки #### GET /api/v1/bank-statement * В ответ метода добавлено новое поле `operationId`, которое содержит уникальный идентификатор операции  ### OpenApi * Добавлена [песочница](https://business.tinkoff.ru/openapi/sandbox/docs)  ## 25 марта 2021 ### Сценарий Селф-Сервис * Перенесли возможность выпуска токена для сценария селф-сервис в раздел \"Интеграции\".  (См. [\"Получение токена\"](#section/Scenarij-Self-Servis/Poluchenie-tokena).)  ## 18 марта 2021 ### OpenApi * Добавлена спецификация ошибки при большой нагрузке на сервис  ### Зарплатный проект * Добавлены [методы для реестровых выплат сотрудникам](#tag/Zarplatnyj-proekt):   * POST /api/v1/salary/payment-registry/submit   * GET  /api/v1/salary/payment-registry/submit/result    ## 15 марта 2021 ### Счета и выписки #### GET /api/v3/bank-accounts * Добавлен новый метод для получения расчетных счетов. Отличия от старого метода:   * Добавлены спец.счета и счета бизнес-копилки #### GET /api/v2/bank-accounts * Метод теперь считается устаревшим  ## 25 февраля 2021 ### SDK * Добавлены SDK для партнерской авторизации   ## 19 февраля 2021 ### Самозанятые #### POST /api/v1/self-employed/payment-registry/create * Добавлен флаг для автоматического удержания налога `taxHolding` #### POST /api/v1/self-employed/payment-registry/create * Добавлен выбор способа создания платежного реестра `registryCreateType`  ### Зарплатный проект #### POST /api/v1/salary/payment-registry/create * Добавлен выбор способа создания платежного реестра `registryCreateType`  ### Платежи #### POST /api/v1/salary/payment-registry/create * В запрос добавилось опциональное строковое поле `revenueTypeCode`. Оно может принимать значения \"1\", \"2\" или \"3\". Подробнее смотри [тут](https://www.audit-it.ru/news/account/1013406.html). * В запрос добавилось опциональное числовое поле `collectionAmount` - yдержанная сумма в рублях. Актуально только при платежах в пользу физлиц.  ## 8 февраля 2021 ### Платежи #### POST /api/v1/payment/create * Изменён пример налогового платежа  ## 28 января 2021 ### Платежи #### POST /api/v1/payment/sbp/pay * Отчество получателя `to.middleName`, содержащееся в запросе, стало опциональным.  ## 21 января 2021 ### Платежи #### POST /api/v1/payment/create * В тело запроса добавлено необязательное поле: корреспондентский счет получателя `recipientCorrAccountNumber` (В связи с [нововведением от 2021 года](https://roskazna.gov.ru/dokumenty/sistema-kaznacheyskikh-platezhey/kaznacheyskie-scheta/), данное поле будет обязательным для налоговых платежей)  ### Счета и выписки #### GET /api/v2/bank-accounts * Исправлены неточности в документации  ## 18 января 2021 ### Торговый эквайринг #### GET /api/v1/tacq/operations/terminal/{terminalKey} * Добавлен [метод для получения операций Торгового эквайринга по терминалу](#tag/Torgovyj-ekvajring)   * GET /api/v1/tacq/operations/terminal/{terminalKey}  ### Платежи #### POST /api/v1/payment/sbp/pay *  Добавлен [метод для совершения выплаты с рублевых счетов компании на счета физлиц через систему быстрых платежей](#tag/Platezhi):     * POST /api/v1/payment/sbp/pay  ## 12 января 2021 ### Самозанятые #### GET /api/v1/self-employed/payment-registry/receipts/result * Добавлен идентификатор самозанятого в `SelfEmployedReceipt.selfEmployedInfo`  ### Спецификация OpenApi * Методы, закрытые [MTLS сертификатом](#section/Sertifikaty), имеют в описании символ 🔒  ### Счета и выписки #### GET /api/v1/bank-statement * Исправлено описание и добавлена валидация на поле `operationType`  ## 10 декабря 2020 ### Информация о пользователе * Добавлен [метод для получения информации об активных дебетовых счетах клиента](#tag/Informaciya-o-polzovatele)   * GET /api/v1/individual/accounts/debit   ## 8 декабря 2020 ### Самозанятые #### GET /api/v1/self-employed/payment-registry/ * Добавлены айди самозанятого в `selfEmployedInfo`  ### Зарплатный проект #### GET /api/v1/salary/payment-registry/ * Добавлены айди сотрудника в `employeeInfo`  ### Счета и выписки #### GET /api/v2/bank-accounts * Добавлен новый метод для получения расчетных счетов. Отличия от старого метода:   * Информация о транзитном счете возвращается вместе с основным счетом   * Возвращается информация о наименовании и типе счета, бике банка получателя  ## 3 декабря 2020 ### Партнерская доставка * Добавлен [метод, позволяющий загрузить документ в задание на партнерскую доставку](#tag/Partnerskaya-dostavka)   * POST /api/v1/delivery/documents * Добавлен [метод, позволяющий выгрузить документ по его идентификатору](#tag/Partnerskaya-dostavka)   * GET /api/v1/delivery/documents/{id}  ### Информация о пользователе Добавлен [метод для получения информации о статусе самозанятого](#tag/Informaciya-o-polzovatele): * GET /api/v1/individual/self-employed/status  ## 26 ноября 2020 ### Спецификация OpenApi * Скорректирована спецификация ошибки 400: ошибка отдается в виде json * Исправлен формат вывода ошибок перечисления  ## 16 ноября 2020 ### Бизнес-карты  #### GET /api/v1/card * Метод теперь доступен без сертификата mTLS * Убрали поле expiryDate из ответа метода  #### GET /api/v1/card/{ucid}  * Метод теперь доступен без сертификата mTLS * Убрали поле expiryDate из ответа метода  ### Спецификация OpenApi * Обновлена спецификация ошибки 403: добавлена информация о причинах ошибки  ## 9 ноября 2020 ### Выставление счета #### POST api/v1/invoice/send * В запрос добавлено опциональное поле `accountNumber`. Теперь отправитель счета может сам выбирать счет на который  он хочет получить оплату. * Поля плательщика `payer.inn`, `payer.name` стали опциональными, как и само поле `payer`, чтобы  пользователь мог выставлять счета физическим лицам не указывая лишних данных, если в них нет необходимости. * Добавлено новое опциональное поле `kpp` для плательщика. Пользователи по желанию смогут передать дополнительную  информацию о плательщике в счет.  ### Информация о пользователе * Добавлено описание [метода получения учётных данных](#section/Poluchenie-uchyotnyh-dannyh)  ### Партнерская доставка * Все методы [партнерской доставки](#tag/Partnerskaya-dostavka) закрыты mTLS  ### Бизнес-карты #### GET /api/v1/card * Добавлен новый метод для получения данных о картах компании * В запросе есть опциональный строковый параметр `agreementNumber`. Он передается, если нужен список с данными карт компании, привязанных к заданному расчетному счету. * В запросе есть опциональный числовой параметр `offset` - количество карт, которые необходимо пропустить. По умолчанию offset = 0. * В запросе есть опциональный числовой параметр `limit` - количество карт, которые необходимо вывести. По умолчанию limit = 1000. Значение для этого параметра не может быть больше 1000. #### GET /api/v1/card/{ucid} * Добавлен метод для получения данных об одной карте по UCID  ## 2 ноября 2020 ### Спецификация OpenApi #### GET /openapi/docs/openapi.yaml * Скорректирован формат загружаемого файла спецификации с .json на .yaml  ### Бизнес-карты * Изменено название раздела \"Корпоративные карты\" на \"Бизнес-карты\"  ### Платежи #### POST /api/v1/payment/create * Добавлена валидация номера документа при создании платежей. Строковое поле должно содержать не более 6 цифр.  ### Информация о пользователе Добавлены [методы для получения информации о пользователе](#tag/Informaciya-o-polzovatele):   * GET /api/v1/individual/documents/passport   * GET /api/v1/individual/documents/driver-licenses   * GET /api/v1/individual/documents/inn   * GET /api/v1/individual/documents/snils   * GET /api/v1/individual/addresses   * GET /api/v1/individual/identification/status       ### Партнерская доставка Добавлены [методы, предоставлющие функционал доставки для партнеров](#tag/Partnerskaya-dostavka)   * GET   /api/v1/delivery/tasks/{id}   * PUT   /api/v1/delivery/tasks/{id}   * POST  /api/v1/delivery/tasks   * POST  /api/v1/delivery/tasks/{id}/cancel   * POST  /api/v1/delivery/meetings   * POST  /api/v1/delivery/intervals  ### Сертификаты * Указано правило транслитерации  ## 13 октября 2020 ### Счета и выписки #### GET /api/v1/bank-statement * Поправлена ошибка, возникающая у некоторых клиентов при запросе больших выписок.   ## 5 октября 2020 ### Платежи #### POST /api/v1/payment/create * Поле `inn`, содержащееся в запросе, теперь может иметь значение \"0\". Такое допустимо, если платеж выполняется в пользу физического лица и  его ИНН неизвестен. * В документации обновлены описания полей `inn` и `kpp`, содержащиеся в запросах.  ## 24 сентября 2020 ### Платежи #### POST /api/v1/payment/ruble-transfer/pay * В документацию добавлено описание лимитов, установленных по умолчанию, на выполнение платежей:     * максимальная сумма одного платежа - 100000 рублей     * максимальная сумма платеже в день - 100000 рублей     * максимальная сумма платежей в месяц - 1000000 рублей     * максимальное количество платежей в день на одного контрагента - 3      ### Селф-сервис * Добавлена возможность выпускать токены с определенными скопами. Для подробностей смотри: Сценарий Селф-Сервис, Получение токена, n.6  ### Самозанятые Добавлены [методы для реестровых выплат самозанятым](#tag/Samozanyatye):   * POST /api/v1/self-employed/recipients/create   * GET /api/v1/self-employed/recipients/create/result   * POST /api/v1/self-employed/recipients/list   * POST /api/v1/self-employed/payment-registry/create   * GET /api/v1/self-employed/payment-registry/create/result   * GET /api/v1/self-employed/payment-registry/{paymentRegistryId}   * POST /api/v1/self-employed/payment-registry/submit   * GET /api/v1/self-employed/payment-registry/submit/result   * POST /api/v1/self-employed/payment-registry/fiscalize   * GET /api/v1/self-employed/payment-registry/fiscalize/result   * POST /api/v1/self-employed/payment-registry/receipts   * GET /api/v1/self-employed/payment-registry/receipts/result  ## 14 сентября 2020  ### Счета и выписки #### GET /api/v1/bank-statement * Поле `recipientInn`, содержащееся в ответе, стало опциональным. Оно может отсутствовать, например, в случае, если идет расчет с контрагентами, так как данное поле не является обязательным для совершения перевода. * Поле `payerAccount`, содержащееся в ответе, стало опциональным. Оно может отсутствовать, например, в случае, если, плательщиком является кредитная организация или филиал кредитной организации.  ### Платежи #### POST /api/v1/payment/create * В запрос добавилось опциональное строковое поле `revenueTypeCode`. Оно может принимать значения \"1\", \"2\" или \"3\". Подробнее смотри [тут](https://www.audit-it.ru/news/account/1013406.html). * В запрос добавилось опциональное числовое поле `collectionAmount` - yдержанная сумма в рублях. Актуально только при платежах в пользу физлиц.  ### Cертификаты * Выпущенные с данного момента сертификаты предоставляются в формате `pem`, что избавляет их владельцев от необходимости конвертации сертификатов перед использованием.
 *
 * The version of the OpenAPI document: 1.0
 * 
 * Generated by: https://openapi-generator.tech
 * OpenAPI Generator version: 5.1.0
 */

/**
 * NOTE: This class is auto generated by OpenAPI Generator (https://openapi-generator.tech).
 * https://openapi-generator.tech
 * Do not edit the class manually.
 */

namespace OpenAPI\Client\tinkoff\api\salary;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use OpenAPI\Client\ApiException;
use OpenAPI\Client\Configuration;
use OpenAPI\Client\HeaderSelector;
use OpenAPI\Client\ObjectSerializer;

/**
 * SalaryApi Class Doc Comment
 *
 * @category Class
 * @package  OpenAPI\Client
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 */
class SalaryApi
{
    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var Configuration
     */
    protected $config;

    /**
     * @var HeaderSelector
     */
    protected $headerSelector;

    /**
     * @var int Host index
     */
    protected $hostIndex;

    /**
     * @param ClientInterface $client
     * @param Configuration   $config
     * @param HeaderSelector  $selector
     * @param int             $hostIndex (Optional) host index to select the list of hosts if defined in the OpenAPI spec
     */
    public function __construct(
        ClientInterface $client = null,
        Configuration $config = null,
        HeaderSelector $selector = null,
        $hostIndex = 0
    ) {
        $this->client = $client ?: new Client();
        $this->config = $config ?: new Configuration();
        $this->headerSelector = $selector ?: new HeaderSelector();
        $this->hostIndex = $hostIndex;
    }

    /**
     * Set the host index
     *
     * @param int $hostIndex Host index (required)
     */
    public function setHostIndex($hostIndex)
    {
        $this->hostIndex = $hostIndex;
    }

    /**
     * Get the host index
     *
     * @return int Host index
     */
    public function getHostIndex()
    {
        return $this->hostIndex;
    }

    /**
     * @return Configuration
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Operation salaryCreateEmployee
     *
     * Создать черновики анкет сотрудников
     *
     * This oepration contains host(s) defined in the OpenAP spec. Use 'hostIndex' to select the host.
     * URL: https://business.tinkoff.ru/openapi
     *
     * @param  \OpenAPI\Client\tinkoff\api\\CreateEmployeesRequest $create_employees_request create_employees_request (required)
     *
     * @throws \OpenAPI\Client\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return \OpenAPI\Client\tinkoff\api\\CreateEmployeesResponse|\OpenAPI\Client\tinkoff\api\\InvalidRequestResponse|\OpenAPI\Client\tinkoff\api\\AuthenticationFailedResponse|\OpenAPI\Client\tinkoff\api\\AuthorizationFailedResponse|\OpenAPI\Client\tinkoff\api\\BusinessErrorResponse|\OpenAPI\Client\tinkoff\api\\TooManyRequestsErrorResponse|\OpenAPI\Client\tinkoff\api\\InternalServerErrorResponse
     */
    public function salaryCreateEmployee($create_employees_request)
    {
        list($response) = $this->salaryCreateEmployeeWithHttpInfo($create_employees_request);
        return $response;
    }

    /**
     * Operation salaryCreateEmployeeWithHttpInfo
     *
     * Создать черновики анкет сотрудников
     *
     * This oepration contains host(s) defined in the OpenAP spec. Use 'hostIndex' to select the host.
     * URL: https://business.tinkoff.ru/openapi
     *
     * @param  \OpenAPI\Client\tinkoff\api\\CreateEmployeesRequest $create_employees_request (required)
     *
     * @throws \OpenAPI\Client\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \OpenAPI\Client\tinkoff\api\\CreateEmployeesResponse|\OpenAPI\Client\tinkoff\api\\InvalidRequestResponse|\OpenAPI\Client\tinkoff\api\\AuthenticationFailedResponse|\OpenAPI\Client\tinkoff\api\\AuthorizationFailedResponse|\OpenAPI\Client\tinkoff\api\\BusinessErrorResponse|\OpenAPI\Client\tinkoff\api\\TooManyRequestsErrorResponse|\OpenAPI\Client\tinkoff\api\\InternalServerErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function salaryCreateEmployeeWithHttpInfo($create_employees_request)
    {
        $request = $this->salaryCreateEmployeeRequest($create_employees_request);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            }

            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    $response->getBody()
                );
            }

            $responseBody = $response->getBody();
            switch($statusCode) {
                case 200:
                    if ('\OpenAPI\Client\tinkoff\api\\CreateEmployeesResponse' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\tinkoff\api\\CreateEmployeesResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 400:
                    if ('\OpenAPI\Client\tinkoff\api\\InvalidRequestResponse' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\tinkoff\api\\InvalidRequestResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 401:
                    if ('\OpenAPI\Client\tinkoff\api\\AuthenticationFailedResponse' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\tinkoff\api\\AuthenticationFailedResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 403:
                    if ('\OpenAPI\Client\tinkoff\api\\AuthorizationFailedResponse' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\tinkoff\api\\AuthorizationFailedResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 422:
                    if ('\OpenAPI\Client\tinkoff\api\\BusinessErrorResponse' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\tinkoff\api\\BusinessErrorResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 429:
                    if ('\OpenAPI\Client\tinkoff\api\\TooManyRequestsErrorResponse' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\tinkoff\api\\TooManyRequestsErrorResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 500:
                    if ('\OpenAPI\Client\tinkoff\api\\InternalServerErrorResponse' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\tinkoff\api\\InternalServerErrorResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            $returnType = '\OpenAPI\Client\tinkoff\api\\CreateEmployeesResponse';
            $responseBody = $response->getBody();
            if ($returnType === '\SplFileObject') {
                $content = $responseBody; //stream goes to serializer
            } else {
                $content = (string) $responseBody;
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];

        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\tinkoff\api\\CreateEmployeesResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\tinkoff\api\\InvalidRequestResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\tinkoff\api\\AuthenticationFailedResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\tinkoff\api\\AuthorizationFailedResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\tinkoff\api\\BusinessErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 429:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\tinkoff\api\\TooManyRequestsErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\tinkoff\api\\InternalServerErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation salaryCreateEmployeeAsync
     *
     * Создать черновики анкет сотрудников
     *
     * This oepration contains host(s) defined in the OpenAP spec. Use 'hostIndex' to select the host.
     * URL: https://business.tinkoff.ru/openapi
     *
     * @param  \OpenAPI\Client\tinkoff\api\\CreateEmployeesRequest $create_employees_request (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function salaryCreateEmployeeAsync($create_employees_request)
    {
        return $this->salaryCreateEmployeeAsyncWithHttpInfo($create_employees_request)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation salaryCreateEmployeeAsyncWithHttpInfo
     *
     * Создать черновики анкет сотрудников
     *
     * This oepration contains host(s) defined in the OpenAP spec. Use 'hostIndex' to select the host.
     * URL: https://business.tinkoff.ru/openapi
     *
     * @param  \OpenAPI\Client\tinkoff\api\\CreateEmployeesRequest $create_employees_request (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function salaryCreateEmployeeAsyncWithHttpInfo($create_employees_request)
    {
        $returnType = '\OpenAPI\Client\tinkoff\api\\CreateEmployeesResponse';
        $request = $this->salaryCreateEmployeeRequest($create_employees_request);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    $responseBody = $response->getBody();
                    if ($returnType === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'salaryCreateEmployee'
     *
     * This oepration contains host(s) defined in the OpenAP spec. Use 'hostIndex' to select the host.
     * URL: https://business.tinkoff.ru/openapi
     *
     * @param  \OpenAPI\Client\tinkoff\api\\CreateEmployeesRequest $create_employees_request (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function salaryCreateEmployeeRequest($create_employees_request)
    {
        // verify the required parameter 'create_employees_request' is set
        if ($create_employees_request === null || (is_array($create_employees_request) && count($create_employees_request) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $create_employees_request when calling salaryCreateEmployee'
            );
        }

        $resourcePath = '/api/v1/salary/employees/create';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;





        if ($multipart) {
            $headers = $this->headerSelector->selectHeadersForMultipart(
                ['application/json']
            );
        } else {
            $headers = $this->headerSelector->selectHeaders(
                ['application/json'],
                ['application/json']
            );
        }

        // for model (json/xml)
        if (isset($create_employees_request)) {
            if ($headers['Content-Type'] === 'application/json') {
                $httpBody = \GuzzleHttp\json_encode(ObjectSerializer::sanitizeForSerialization($create_employees_request));
            } else {
                $httpBody = $create_employees_request;
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif ($headers['Content-Type'] === 'application/json') {
                $httpBody = \GuzzleHttp\json_encode($formParams);

            } else {
                // for HTTP post (form)
                $httpBody = \GuzzleHttp\Psr7\build_query($formParams);
            }
        }

        // this endpoint requires Bearer authentication (access token)
        if ($this->config->getAccessToken() !== null) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHosts = ["https://business.tinkoff.ru/openapi"];
        if ($this->hostIndex < 0 || $this->hostIndex >= sizeof($operationHosts)) {
            throw new \InvalidArgumentException("Invalid index {$this->hostIndex} when selecting the host. Must be less than ".sizeof($operationHosts));
        }
        $operationHost = $operationHosts[$this->hostIndex];

        $query = \GuzzleHttp\Psr7\build_query($queryParams);
        return new Request(
            'POST',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation salaryCreatePaymentRegistry
     *
     * Создание черновика платежного реестра
     *
     * This oepration contains host(s) defined in the OpenAP spec. Use 'hostIndex' to select the host.
     * URL: https://business.tinkoff.ru/openapi
     *
     * @param  \OpenAPI\Client\tinkoff\api\\CreatePaymentRegistryRequest $create_payment_registry_request create_payment_registry_request (required)
     *
     * @throws \OpenAPI\Client\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return \OpenAPI\Client\tinkoff\api\\CreatePaymentRegistryResponse|\OpenAPI\Client\tinkoff\api\\InvalidRequestResponse|\OpenAPI\Client\tinkoff\api\\AuthenticationFailedResponse|\OpenAPI\Client\tinkoff\api\\AuthorizationFailedResponse|\OpenAPI\Client\tinkoff\api\\BusinessErrorResponse|\OpenAPI\Client\tinkoff\api\\TooManyRequestsErrorResponse|\OpenAPI\Client\tinkoff\api\\InternalServerErrorResponse
     */
    public function salaryCreatePaymentRegistry($create_payment_registry_request)
    {
        list($response) = $this->salaryCreatePaymentRegistryWithHttpInfo($create_payment_registry_request);
        return $response;
    }

    /**
     * Operation salaryCreatePaymentRegistryWithHttpInfo
     *
     * Создание черновика платежного реестра
     *
     * This oepration contains host(s) defined in the OpenAP spec. Use 'hostIndex' to select the host.
     * URL: https://business.tinkoff.ru/openapi
     *
     * @param  \OpenAPI\Client\tinkoff\api\\CreatePaymentRegistryRequest $create_payment_registry_request (required)
     *
     * @throws \OpenAPI\Client\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \OpenAPI\Client\tinkoff\api\\CreatePaymentRegistryResponse|\OpenAPI\Client\tinkoff\api\\InvalidRequestResponse|\OpenAPI\Client\tinkoff\api\\AuthenticationFailedResponse|\OpenAPI\Client\tinkoff\api\\AuthorizationFailedResponse|\OpenAPI\Client\tinkoff\api\\BusinessErrorResponse|\OpenAPI\Client\tinkoff\api\\TooManyRequestsErrorResponse|\OpenAPI\Client\tinkoff\api\\InternalServerErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function salaryCreatePaymentRegistryWithHttpInfo($create_payment_registry_request)
    {
        $request = $this->salaryCreatePaymentRegistryRequest($create_payment_registry_request);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            }

            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    $response->getBody()
                );
            }

            $responseBody = $response->getBody();
            switch($statusCode) {
                case 200:
                    if ('\OpenAPI\Client\tinkoff\api\\CreatePaymentRegistryResponse' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\tinkoff\api\\CreatePaymentRegistryResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 400:
                    if ('\OpenAPI\Client\tinkoff\api\\InvalidRequestResponse' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\tinkoff\api\\InvalidRequestResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 401:
                    if ('\OpenAPI\Client\tinkoff\api\\AuthenticationFailedResponse' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\tinkoff\api\\AuthenticationFailedResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 403:
                    if ('\OpenAPI\Client\tinkoff\api\\AuthorizationFailedResponse' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\tinkoff\api\\AuthorizationFailedResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 422:
                    if ('\OpenAPI\Client\tinkoff\api\\BusinessErrorResponse' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\tinkoff\api\\BusinessErrorResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 429:
                    if ('\OpenAPI\Client\tinkoff\api\\TooManyRequestsErrorResponse' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\tinkoff\api\\TooManyRequestsErrorResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 500:
                    if ('\OpenAPI\Client\tinkoff\api\\InternalServerErrorResponse' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\tinkoff\api\\InternalServerErrorResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            $returnType = '\OpenAPI\Client\tinkoff\api\\CreatePaymentRegistryResponse';
            $responseBody = $response->getBody();
            if ($returnType === '\SplFileObject') {
                $content = $responseBody; //stream goes to serializer
            } else {
                $content = (string) $responseBody;
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];

        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\tinkoff\api\\CreatePaymentRegistryResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\tinkoff\api\\InvalidRequestResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\tinkoff\api\\AuthenticationFailedResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\tinkoff\api\\AuthorizationFailedResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\tinkoff\api\\BusinessErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 429:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\tinkoff\api\\TooManyRequestsErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\tinkoff\api\\InternalServerErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation salaryCreatePaymentRegistryAsync
     *
     * Создание черновика платежного реестра
     *
     * This oepration contains host(s) defined in the OpenAP spec. Use 'hostIndex' to select the host.
     * URL: https://business.tinkoff.ru/openapi
     *
     * @param  \OpenAPI\Client\tinkoff\api\\CreatePaymentRegistryRequest $create_payment_registry_request (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function salaryCreatePaymentRegistryAsync($create_payment_registry_request)
    {
        return $this->salaryCreatePaymentRegistryAsyncWithHttpInfo($create_payment_registry_request)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation salaryCreatePaymentRegistryAsyncWithHttpInfo
     *
     * Создание черновика платежного реестра
     *
     * This oepration contains host(s) defined in the OpenAP spec. Use 'hostIndex' to select the host.
     * URL: https://business.tinkoff.ru/openapi
     *
     * @param  \OpenAPI\Client\tinkoff\api\\CreatePaymentRegistryRequest $create_payment_registry_request (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function salaryCreatePaymentRegistryAsyncWithHttpInfo($create_payment_registry_request)
    {
        $returnType = '\OpenAPI\Client\tinkoff\api\\CreatePaymentRegistryResponse';
        $request = $this->salaryCreatePaymentRegistryRequest($create_payment_registry_request);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    $responseBody = $response->getBody();
                    if ($returnType === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'salaryCreatePaymentRegistry'
     *
     * This oepration contains host(s) defined in the OpenAP spec. Use 'hostIndex' to select the host.
     * URL: https://business.tinkoff.ru/openapi
     *
     * @param  \OpenAPI\Client\tinkoff\api\\CreatePaymentRegistryRequest $create_payment_registry_request (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function salaryCreatePaymentRegistryRequest($create_payment_registry_request)
    {
        // verify the required parameter 'create_payment_registry_request' is set
        if ($create_payment_registry_request === null || (is_array($create_payment_registry_request) && count($create_payment_registry_request) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $create_payment_registry_request when calling salaryCreatePaymentRegistry'
            );
        }

        $resourcePath = '/api/v1/salary/payment-registry/create';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;





        if ($multipart) {
            $headers = $this->headerSelector->selectHeadersForMultipart(
                ['application/json']
            );
        } else {
            $headers = $this->headerSelector->selectHeaders(
                ['application/json'],
                ['application/json']
            );
        }

        // for model (json/xml)
        if (isset($create_payment_registry_request)) {
            if ($headers['Content-Type'] === 'application/json') {
                $httpBody = \GuzzleHttp\json_encode(ObjectSerializer::sanitizeForSerialization($create_payment_registry_request));
            } else {
                $httpBody = $create_payment_registry_request;
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif ($headers['Content-Type'] === 'application/json') {
                $httpBody = \GuzzleHttp\json_encode($formParams);

            } else {
                // for HTTP post (form)
                $httpBody = \GuzzleHttp\Psr7\build_query($formParams);
            }
        }

        // this endpoint requires Bearer authentication (access token)
        if ($this->config->getAccessToken() !== null) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHosts = ["https://business.tinkoff.ru/openapi"];
        if ($this->hostIndex < 0 || $this->hostIndex >= sizeof($operationHosts)) {
            throw new \InvalidArgumentException("Invalid index {$this->hostIndex} when selecting the host. Must be less than ".sizeof($operationHosts));
        }
        $operationHost = $operationHosts[$this->hostIndex];

        $query = \GuzzleHttp\Psr7\build_query($queryParams);
        return new Request(
            'POST',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation salaryGetEmployeesCreateResult
     *
     * Получить результат создания черновиков анкет сотрудников
     *
     * This oepration contains host(s) defined in the OpenAP spec. Use 'hostIndex' to select the host.
     * URL: https://business.tinkoff.ru/openapi
     *
     * @param  string $correlation_id Уникальный идентификатор(тип &lt;a href&#x3D;\&quot;https://en.wikipedia.org/wiki/Universally_unique_identifier\&quot;&gt;UUID&lt;/a&gt;), связывающий запрос создания с запросом получения ответа (должен быть сформирован на стороне клиента) (required)
     *
     * @throws \OpenAPI\Client\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return \OpenAPI\Client\tinkoff\api\\CreateEmployeeResultResponse|\OpenAPI\Client\tinkoff\api\\InvalidRequestResponse|\OpenAPI\Client\tinkoff\api\\AuthenticationFailedResponse|\OpenAPI\Client\tinkoff\api\\AuthorizationFailedResponse|\OpenAPI\Client\tinkoff\api\\BusinessErrorResponse|\OpenAPI\Client\tinkoff\api\\TooManyRequestsErrorResponse|\OpenAPI\Client\tinkoff\api\\InternalServerErrorResponse
     */
    public function salaryGetEmployeesCreateResult($correlation_id)
    {
        list($response) = $this->salaryGetEmployeesCreateResultWithHttpInfo($correlation_id);
        return $response;
    }

    /**
     * Operation salaryGetEmployeesCreateResultWithHttpInfo
     *
     * Получить результат создания черновиков анкет сотрудников
     *
     * This oepration contains host(s) defined in the OpenAP spec. Use 'hostIndex' to select the host.
     * URL: https://business.tinkoff.ru/openapi
     *
     * @param  string $correlation_id Уникальный идентификатор(тип &lt;a href&#x3D;\&quot;https://en.wikipedia.org/wiki/Universally_unique_identifier\&quot;&gt;UUID&lt;/a&gt;), связывающий запрос создания с запросом получения ответа (должен быть сформирован на стороне клиента) (required)
     *
     * @throws \OpenAPI\Client\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \OpenAPI\Client\tinkoff\api\\CreateEmployeeResultResponse|\OpenAPI\Client\tinkoff\api\\InvalidRequestResponse|\OpenAPI\Client\tinkoff\api\\AuthenticationFailedResponse|\OpenAPI\Client\tinkoff\api\\AuthorizationFailedResponse|\OpenAPI\Client\tinkoff\api\\BusinessErrorResponse|\OpenAPI\Client\tinkoff\api\\TooManyRequestsErrorResponse|\OpenAPI\Client\tinkoff\api\\InternalServerErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function salaryGetEmployeesCreateResultWithHttpInfo($correlation_id)
    {
        $request = $this->salaryGetEmployeesCreateResultRequest($correlation_id);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            }

            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    $response->getBody()
                );
            }

            $responseBody = $response->getBody();
            switch($statusCode) {
                case 200:
                    if ('\OpenAPI\Client\tinkoff\api\\CreateEmployeeResultResponse' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\tinkoff\api\\CreateEmployeeResultResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 400:
                    if ('\OpenAPI\Client\tinkoff\api\\InvalidRequestResponse' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\tinkoff\api\\InvalidRequestResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 401:
                    if ('\OpenAPI\Client\tinkoff\api\\AuthenticationFailedResponse' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\tinkoff\api\\AuthenticationFailedResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 403:
                    if ('\OpenAPI\Client\tinkoff\api\\AuthorizationFailedResponse' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\tinkoff\api\\AuthorizationFailedResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 422:
                    if ('\OpenAPI\Client\tinkoff\api\\BusinessErrorResponse' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\tinkoff\api\\BusinessErrorResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 429:
                    if ('\OpenAPI\Client\tinkoff\api\\TooManyRequestsErrorResponse' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\tinkoff\api\\TooManyRequestsErrorResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 500:
                    if ('\OpenAPI\Client\tinkoff\api\\InternalServerErrorResponse' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\tinkoff\api\\InternalServerErrorResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            $returnType = '\OpenAPI\Client\tinkoff\api\\CreateEmployeeResultResponse';
            $responseBody = $response->getBody();
            if ($returnType === '\SplFileObject') {
                $content = $responseBody; //stream goes to serializer
            } else {
                $content = (string) $responseBody;
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];

        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\tinkoff\api\\CreateEmployeeResultResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\tinkoff\api\\InvalidRequestResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\tinkoff\api\\AuthenticationFailedResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\tinkoff\api\\AuthorizationFailedResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\tinkoff\api\\BusinessErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 429:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\tinkoff\api\\TooManyRequestsErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\tinkoff\api\\InternalServerErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation salaryGetEmployeesCreateResultAsync
     *
     * Получить результат создания черновиков анкет сотрудников
     *
     * This oepration contains host(s) defined in the OpenAP spec. Use 'hostIndex' to select the host.
     * URL: https://business.tinkoff.ru/openapi
     *
     * @param  string $correlation_id Уникальный идентификатор(тип &lt;a href&#x3D;\&quot;https://en.wikipedia.org/wiki/Universally_unique_identifier\&quot;&gt;UUID&lt;/a&gt;), связывающий запрос создания с запросом получения ответа (должен быть сформирован на стороне клиента) (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function salaryGetEmployeesCreateResultAsync($correlation_id)
    {
        return $this->salaryGetEmployeesCreateResultAsyncWithHttpInfo($correlation_id)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation salaryGetEmployeesCreateResultAsyncWithHttpInfo
     *
     * Получить результат создания черновиков анкет сотрудников
     *
     * This oepration contains host(s) defined in the OpenAP spec. Use 'hostIndex' to select the host.
     * URL: https://business.tinkoff.ru/openapi
     *
     * @param  string $correlation_id Уникальный идентификатор(тип &lt;a href&#x3D;\&quot;https://en.wikipedia.org/wiki/Universally_unique_identifier\&quot;&gt;UUID&lt;/a&gt;), связывающий запрос создания с запросом получения ответа (должен быть сформирован на стороне клиента) (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function salaryGetEmployeesCreateResultAsyncWithHttpInfo($correlation_id)
    {
        $returnType = '\OpenAPI\Client\tinkoff\api\\CreateEmployeeResultResponse';
        $request = $this->salaryGetEmployeesCreateResultRequest($correlation_id);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    $responseBody = $response->getBody();
                    if ($returnType === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'salaryGetEmployeesCreateResult'
     *
     * This oepration contains host(s) defined in the OpenAP spec. Use 'hostIndex' to select the host.
     * URL: https://business.tinkoff.ru/openapi
     *
     * @param  string $correlation_id Уникальный идентификатор(тип &lt;a href&#x3D;\&quot;https://en.wikipedia.org/wiki/Universally_unique_identifier\&quot;&gt;UUID&lt;/a&gt;), связывающий запрос создания с запросом получения ответа (должен быть сформирован на стороне клиента) (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function salaryGetEmployeesCreateResultRequest($correlation_id)
    {
        // verify the required parameter 'correlation_id' is set
        if ($correlation_id === null || (is_array($correlation_id) && count($correlation_id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $correlation_id when calling salaryGetEmployeesCreateResult'
            );
        }

        $resourcePath = '/api/v1/salary/employees/create/result';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        if ($correlation_id !== null) {
            if('form' === 'form' && is_array($correlation_id)) {
                foreach($correlation_id as $key => $value) {
                    $queryParams[$key] = $value;
                }
            }
            else {
                $queryParams['correlationId'] = $correlation_id;
            }
        }




        if ($multipart) {
            $headers = $this->headerSelector->selectHeadersForMultipart(
                ['application/json']
            );
        } else {
            $headers = $this->headerSelector->selectHeaders(
                ['application/json'],
                []
            );
        }

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif ($headers['Content-Type'] === 'application/json') {
                $httpBody = \GuzzleHttp\json_encode($formParams);

            } else {
                // for HTTP post (form)
                $httpBody = \GuzzleHttp\Psr7\build_query($formParams);
            }
        }

        // this endpoint requires Bearer authentication (access token)
        if ($this->config->getAccessToken() !== null) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHosts = ["https://business.tinkoff.ru/openapi"];
        if ($this->hostIndex < 0 || $this->hostIndex >= sizeof($operationHosts)) {
            throw new \InvalidArgumentException("Invalid index {$this->hostIndex} when selecting the host. Must be less than ".sizeof($operationHosts));
        }
        $operationHost = $operationHosts[$this->hostIndex];

        $query = \GuzzleHttp\Psr7\build_query($queryParams);
        return new Request(
            'GET',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation salaryGetEmployeesList
     *
     * Получить информацию по сотрудникам
     *
     * This oepration contains host(s) defined in the OpenAP spec. Use 'hostIndex' to select the host.
     * URL: https://business.tinkoff.ru/openapi
     *
     * @param  \OpenAPI\Client\tinkoff\api\\EmployeesInfoRequest $employees_info_request Список идентификаторов сотрудников (required)
     *
     * @throws \OpenAPI\Client\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return \OpenAPI\Client\tinkoff\api\\EmployeeResponse|\OpenAPI\Client\tinkoff\api\\InvalidRequestResponse|\OpenAPI\Client\tinkoff\api\\AuthenticationFailedResponse|\OpenAPI\Client\tinkoff\api\\AuthorizationFailedResponse|\OpenAPI\Client\tinkoff\api\\BusinessErrorResponse|\OpenAPI\Client\tinkoff\api\\TooManyRequestsErrorResponse|\OpenAPI\Client\tinkoff\api\\InternalServerErrorResponse
     */
    public function salaryGetEmployeesList($employees_info_request)
    {
        list($response) = $this->salaryGetEmployeesListWithHttpInfo($employees_info_request);
        return $response;
    }

    /**
     * Operation salaryGetEmployeesListWithHttpInfo
     *
     * Получить информацию по сотрудникам
     *
     * This oepration contains host(s) defined in the OpenAP spec. Use 'hostIndex' to select the host.
     * URL: https://business.tinkoff.ru/openapi
     *
     * @param  \OpenAPI\Client\tinkoff\api\\EmployeesInfoRequest $employees_info_request Список идентификаторов сотрудников (required)
     *
     * @throws \OpenAPI\Client\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \OpenAPI\Client\tinkoff\api\\EmployeeResponse|\OpenAPI\Client\tinkoff\api\\InvalidRequestResponse|\OpenAPI\Client\tinkoff\api\\AuthenticationFailedResponse|\OpenAPI\Client\tinkoff\api\\AuthorizationFailedResponse|\OpenAPI\Client\tinkoff\api\\BusinessErrorResponse|\OpenAPI\Client\tinkoff\api\\TooManyRequestsErrorResponse|\OpenAPI\Client\tinkoff\api\\InternalServerErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function salaryGetEmployeesListWithHttpInfo($employees_info_request)
    {
        $request = $this->salaryGetEmployeesListRequest($employees_info_request);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            }

            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    $response->getBody()
                );
            }

            $responseBody = $response->getBody();
            switch($statusCode) {
                case 200:
                    if ('\OpenAPI\Client\tinkoff\api\\EmployeeResponse' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\tinkoff\api\\EmployeeResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 400:
                    if ('\OpenAPI\Client\tinkoff\api\\InvalidRequestResponse' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\tinkoff\api\\InvalidRequestResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 401:
                    if ('\OpenAPI\Client\tinkoff\api\\AuthenticationFailedResponse' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\tinkoff\api\\AuthenticationFailedResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 403:
                    if ('\OpenAPI\Client\tinkoff\api\\AuthorizationFailedResponse' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\tinkoff\api\\AuthorizationFailedResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 422:
                    if ('\OpenAPI\Client\tinkoff\api\\BusinessErrorResponse' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\tinkoff\api\\BusinessErrorResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 429:
                    if ('\OpenAPI\Client\tinkoff\api\\TooManyRequestsErrorResponse' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\tinkoff\api\\TooManyRequestsErrorResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 500:
                    if ('\OpenAPI\Client\tinkoff\api\\InternalServerErrorResponse' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\tinkoff\api\\InternalServerErrorResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            $returnType = '\OpenAPI\Client\tinkoff\api\\EmployeeResponse';
            $responseBody = $response->getBody();
            if ($returnType === '\SplFileObject') {
                $content = $responseBody; //stream goes to serializer
            } else {
                $content = (string) $responseBody;
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];

        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\tinkoff\api\\EmployeeResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\tinkoff\api\\InvalidRequestResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\tinkoff\api\\AuthenticationFailedResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\tinkoff\api\\AuthorizationFailedResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\tinkoff\api\\BusinessErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 429:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\tinkoff\api\\TooManyRequestsErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\tinkoff\api\\InternalServerErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation salaryGetEmployeesListAsync
     *
     * Получить информацию по сотрудникам
     *
     * This oepration contains host(s) defined in the OpenAP spec. Use 'hostIndex' to select the host.
     * URL: https://business.tinkoff.ru/openapi
     *
     * @param  \OpenAPI\Client\tinkoff\api\\EmployeesInfoRequest $employees_info_request Список идентификаторов сотрудников (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function salaryGetEmployeesListAsync($employees_info_request)
    {
        return $this->salaryGetEmployeesListAsyncWithHttpInfo($employees_info_request)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation salaryGetEmployeesListAsyncWithHttpInfo
     *
     * Получить информацию по сотрудникам
     *
     * This oepration contains host(s) defined in the OpenAP spec. Use 'hostIndex' to select the host.
     * URL: https://business.tinkoff.ru/openapi
     *
     * @param  \OpenAPI\Client\tinkoff\api\\EmployeesInfoRequest $employees_info_request Список идентификаторов сотрудников (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function salaryGetEmployeesListAsyncWithHttpInfo($employees_info_request)
    {
        $returnType = '\OpenAPI\Client\tinkoff\api\\EmployeeResponse';
        $request = $this->salaryGetEmployeesListRequest($employees_info_request);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    $responseBody = $response->getBody();
                    if ($returnType === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'salaryGetEmployeesList'
     *
     * This oepration contains host(s) defined in the OpenAP spec. Use 'hostIndex' to select the host.
     * URL: https://business.tinkoff.ru/openapi
     *
     * @param  \OpenAPI\Client\tinkoff\api\\EmployeesInfoRequest $employees_info_request Список идентификаторов сотрудников (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function salaryGetEmployeesListRequest($employees_info_request)
    {
        // verify the required parameter 'employees_info_request' is set
        if ($employees_info_request === null || (is_array($employees_info_request) && count($employees_info_request) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $employees_info_request when calling salaryGetEmployeesList'
            );
        }

        $resourcePath = '/api/v1/salary/employees/list';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;





        if ($multipart) {
            $headers = $this->headerSelector->selectHeadersForMultipart(
                ['application/json']
            );
        } else {
            $headers = $this->headerSelector->selectHeaders(
                ['application/json'],
                ['application/json']
            );
        }

        // for model (json/xml)
        if (isset($employees_info_request)) {
            if ($headers['Content-Type'] === 'application/json') {
                $httpBody = \GuzzleHttp\json_encode(ObjectSerializer::sanitizeForSerialization($employees_info_request));
            } else {
                $httpBody = $employees_info_request;
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif ($headers['Content-Type'] === 'application/json') {
                $httpBody = \GuzzleHttp\json_encode($formParams);

            } else {
                // for HTTP post (form)
                $httpBody = \GuzzleHttp\Psr7\build_query($formParams);
            }
        }

        // this endpoint requires Bearer authentication (access token)
        if ($this->config->getAccessToken() !== null) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHosts = ["https://business.tinkoff.ru/openapi"];
        if ($this->hostIndex < 0 || $this->hostIndex >= sizeof($operationHosts)) {
            throw new \InvalidArgumentException("Invalid index {$this->hostIndex} when selecting the host. Must be less than ".sizeof($operationHosts));
        }
        $operationHost = $operationHosts[$this->hostIndex];

        $query = \GuzzleHttp\Psr7\build_query($queryParams);
        return new Request(
            'POST',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation salaryGetPaymentRegistry
     *
     * Получение платежного реестра
     *
     * This oepration contains host(s) defined in the OpenAP spec. Use 'hostIndex' to select the host.
     * URL: https://business.tinkoff.ru/openapi
     *
     * @param  int $payment_registry_id Номер платежного реестра (required)
     *
     * @throws \OpenAPI\Client\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return \OpenAPI\Client\tinkoff\api\\PaymentRegistry|\OpenAPI\Client\tinkoff\api\\InvalidRequestResponse|\OpenAPI\Client\tinkoff\api\\AuthenticationFailedResponse|\OpenAPI\Client\tinkoff\api\\AuthorizationFailedResponse|\OpenAPI\Client\tinkoff\api\\BusinessErrorResponse|\OpenAPI\Client\tinkoff\api\\TooManyRequestsErrorResponse|\OpenAPI\Client\tinkoff\api\\InternalServerErrorResponse
     */
    public function salaryGetPaymentRegistry($payment_registry_id)
    {
        list($response) = $this->salaryGetPaymentRegistryWithHttpInfo($payment_registry_id);
        return $response;
    }

    /**
     * Operation salaryGetPaymentRegistryWithHttpInfo
     *
     * Получение платежного реестра
     *
     * This oepration contains host(s) defined in the OpenAP spec. Use 'hostIndex' to select the host.
     * URL: https://business.tinkoff.ru/openapi
     *
     * @param  int $payment_registry_id Номер платежного реестра (required)
     *
     * @throws \OpenAPI\Client\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \OpenAPI\Client\tinkoff\api\\PaymentRegistry|\OpenAPI\Client\tinkoff\api\\InvalidRequestResponse|\OpenAPI\Client\tinkoff\api\\AuthenticationFailedResponse|\OpenAPI\Client\tinkoff\api\\AuthorizationFailedResponse|\OpenAPI\Client\tinkoff\api\\BusinessErrorResponse|\OpenAPI\Client\tinkoff\api\\TooManyRequestsErrorResponse|\OpenAPI\Client\tinkoff\api\\InternalServerErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function salaryGetPaymentRegistryWithHttpInfo($payment_registry_id)
    {
        $request = $this->salaryGetPaymentRegistryRequest($payment_registry_id);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            }

            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    $response->getBody()
                );
            }

            $responseBody = $response->getBody();
            switch($statusCode) {
                case 200:
                    if ('\OpenAPI\Client\tinkoff\api\\PaymentRegistry' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\tinkoff\api\\PaymentRegistry', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 400:
                    if ('\OpenAPI\Client\tinkoff\api\\InvalidRequestResponse' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\tinkoff\api\\InvalidRequestResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 401:
                    if ('\OpenAPI\Client\tinkoff\api\\AuthenticationFailedResponse' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\tinkoff\api\\AuthenticationFailedResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 403:
                    if ('\OpenAPI\Client\tinkoff\api\\AuthorizationFailedResponse' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\tinkoff\api\\AuthorizationFailedResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 422:
                    if ('\OpenAPI\Client\tinkoff\api\\BusinessErrorResponse' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\tinkoff\api\\BusinessErrorResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 429:
                    if ('\OpenAPI\Client\tinkoff\api\\TooManyRequestsErrorResponse' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\tinkoff\api\\TooManyRequestsErrorResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 500:
                    if ('\OpenAPI\Client\tinkoff\api\\InternalServerErrorResponse' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\tinkoff\api\\InternalServerErrorResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            $returnType = '\OpenAPI\Client\tinkoff\api\\PaymentRegistry';
            $responseBody = $response->getBody();
            if ($returnType === '\SplFileObject') {
                $content = $responseBody; //stream goes to serializer
            } else {
                $content = (string) $responseBody;
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];

        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\tinkoff\api\\PaymentRegistry',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\tinkoff\api\\InvalidRequestResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\tinkoff\api\\AuthenticationFailedResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\tinkoff\api\\AuthorizationFailedResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\tinkoff\api\\BusinessErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 429:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\tinkoff\api\\TooManyRequestsErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\tinkoff\api\\InternalServerErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation salaryGetPaymentRegistryAsync
     *
     * Получение платежного реестра
     *
     * This oepration contains host(s) defined in the OpenAP spec. Use 'hostIndex' to select the host.
     * URL: https://business.tinkoff.ru/openapi
     *
     * @param  int $payment_registry_id Номер платежного реестра (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function salaryGetPaymentRegistryAsync($payment_registry_id)
    {
        return $this->salaryGetPaymentRegistryAsyncWithHttpInfo($payment_registry_id)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation salaryGetPaymentRegistryAsyncWithHttpInfo
     *
     * Получение платежного реестра
     *
     * This oepration contains host(s) defined in the OpenAP spec. Use 'hostIndex' to select the host.
     * URL: https://business.tinkoff.ru/openapi
     *
     * @param  int $payment_registry_id Номер платежного реестра (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function salaryGetPaymentRegistryAsyncWithHttpInfo($payment_registry_id)
    {
        $returnType = '\OpenAPI\Client\tinkoff\api\\PaymentRegistry';
        $request = $this->salaryGetPaymentRegistryRequest($payment_registry_id);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    $responseBody = $response->getBody();
                    if ($returnType === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'salaryGetPaymentRegistry'
     *
     * This oepration contains host(s) defined in the OpenAP spec. Use 'hostIndex' to select the host.
     * URL: https://business.tinkoff.ru/openapi
     *
     * @param  int $payment_registry_id Номер платежного реестра (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function salaryGetPaymentRegistryRequest($payment_registry_id)
    {
        // verify the required parameter 'payment_registry_id' is set
        if ($payment_registry_id === null || (is_array($payment_registry_id) && count($payment_registry_id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $payment_registry_id when calling salaryGetPaymentRegistry'
            );
        }

        $resourcePath = '/api/v1/salary/payment-registry/{paymentRegistryId}';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;



        // path params
        if ($payment_registry_id !== null) {
            $resourcePath = str_replace(
                '{' . 'paymentRegistryId' . '}',
                ObjectSerializer::toPathValue($payment_registry_id),
                $resourcePath
            );
        }


        if ($multipart) {
            $headers = $this->headerSelector->selectHeadersForMultipart(
                ['application/json']
            );
        } else {
            $headers = $this->headerSelector->selectHeaders(
                ['application/json'],
                []
            );
        }

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif ($headers['Content-Type'] === 'application/json') {
                $httpBody = \GuzzleHttp\json_encode($formParams);

            } else {
                // for HTTP post (form)
                $httpBody = \GuzzleHttp\Psr7\build_query($formParams);
            }
        }

        // this endpoint requires Bearer authentication (access token)
        if ($this->config->getAccessToken() !== null) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHosts = ["https://business.tinkoff.ru/openapi"];
        if ($this->hostIndex < 0 || $this->hostIndex >= sizeof($operationHosts)) {
            throw new \InvalidArgumentException("Invalid index {$this->hostIndex} when selecting the host. Must be less than ".sizeof($operationHosts));
        }
        $operationHost = $operationHosts[$this->hostIndex];

        $query = \GuzzleHttp\Psr7\build_query($queryParams);
        return new Request(
            'GET',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation salaryGetPaymentRegistryCreateResult
     *
     * Получить результат создания черновика платежного реестра
     *
     * This oepration contains host(s) defined in the OpenAP spec. Use 'hostIndex' to select the host.
     * URL: https://business.tinkoff.ru/openapi
     *
     * @param  string $correlation_id Идентификатор, связывающий запрос создания с запросом получения ответа (required)
     *
     * @throws \OpenAPI\Client\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return \OpenAPI\Client\tinkoff\api\\CreatePaymentRegistryResultResponse|\OpenAPI\Client\tinkoff\api\\InvalidRequestResponse|\OpenAPI\Client\tinkoff\api\\AuthenticationFailedResponse|\OpenAPI\Client\tinkoff\api\\AuthorizationFailedResponse|\OpenAPI\Client\tinkoff\api\\BusinessErrorResponse|\OpenAPI\Client\tinkoff\api\\TooManyRequestsErrorResponse|\OpenAPI\Client\tinkoff\api\\InternalServerErrorResponse
     */
    public function salaryGetPaymentRegistryCreateResult($correlation_id)
    {
        list($response) = $this->salaryGetPaymentRegistryCreateResultWithHttpInfo($correlation_id);
        return $response;
    }

    /**
     * Operation salaryGetPaymentRegistryCreateResultWithHttpInfo
     *
     * Получить результат создания черновика платежного реестра
     *
     * This oepration contains host(s) defined in the OpenAP spec. Use 'hostIndex' to select the host.
     * URL: https://business.tinkoff.ru/openapi
     *
     * @param  string $correlation_id Идентификатор, связывающий запрос создания с запросом получения ответа (required)
     *
     * @throws \OpenAPI\Client\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \OpenAPI\Client\tinkoff\api\\CreatePaymentRegistryResultResponse|\OpenAPI\Client\tinkoff\api\\InvalidRequestResponse|\OpenAPI\Client\tinkoff\api\\AuthenticationFailedResponse|\OpenAPI\Client\tinkoff\api\\AuthorizationFailedResponse|\OpenAPI\Client\tinkoff\api\\BusinessErrorResponse|\OpenAPI\Client\tinkoff\api\\TooManyRequestsErrorResponse|\OpenAPI\Client\tinkoff\api\\InternalServerErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function salaryGetPaymentRegistryCreateResultWithHttpInfo($correlation_id)
    {
        $request = $this->salaryGetPaymentRegistryCreateResultRequest($correlation_id);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            }

            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    $response->getBody()
                );
            }

            $responseBody = $response->getBody();
            switch($statusCode) {
                case 200:
                    if ('\OpenAPI\Client\tinkoff\api\\CreatePaymentRegistryResultResponse' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\tinkoff\api\\CreatePaymentRegistryResultResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 400:
                    if ('\OpenAPI\Client\tinkoff\api\\InvalidRequestResponse' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\tinkoff\api\\InvalidRequestResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 401:
                    if ('\OpenAPI\Client\tinkoff\api\\AuthenticationFailedResponse' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\tinkoff\api\\AuthenticationFailedResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 403:
                    if ('\OpenAPI\Client\tinkoff\api\\AuthorizationFailedResponse' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\tinkoff\api\\AuthorizationFailedResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 422:
                    if ('\OpenAPI\Client\tinkoff\api\\BusinessErrorResponse' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\tinkoff\api\\BusinessErrorResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 429:
                    if ('\OpenAPI\Client\tinkoff\api\\TooManyRequestsErrorResponse' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\tinkoff\api\\TooManyRequestsErrorResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 500:
                    if ('\OpenAPI\Client\tinkoff\api\\InternalServerErrorResponse' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\tinkoff\api\\InternalServerErrorResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            $returnType = '\OpenAPI\Client\tinkoff\api\\CreatePaymentRegistryResultResponse';
            $responseBody = $response->getBody();
            if ($returnType === '\SplFileObject') {
                $content = $responseBody; //stream goes to serializer
            } else {
                $content = (string) $responseBody;
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];

        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\tinkoff\api\\CreatePaymentRegistryResultResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\tinkoff\api\\InvalidRequestResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\tinkoff\api\\AuthenticationFailedResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\tinkoff\api\\AuthorizationFailedResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\tinkoff\api\\BusinessErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 429:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\tinkoff\api\\TooManyRequestsErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\tinkoff\api\\InternalServerErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation salaryGetPaymentRegistryCreateResultAsync
     *
     * Получить результат создания черновика платежного реестра
     *
     * This oepration contains host(s) defined in the OpenAP spec. Use 'hostIndex' to select the host.
     * URL: https://business.tinkoff.ru/openapi
     *
     * @param  string $correlation_id Идентификатор, связывающий запрос создания с запросом получения ответа (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function salaryGetPaymentRegistryCreateResultAsync($correlation_id)
    {
        return $this->salaryGetPaymentRegistryCreateResultAsyncWithHttpInfo($correlation_id)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation salaryGetPaymentRegistryCreateResultAsyncWithHttpInfo
     *
     * Получить результат создания черновика платежного реестра
     *
     * This oepration contains host(s) defined in the OpenAP spec. Use 'hostIndex' to select the host.
     * URL: https://business.tinkoff.ru/openapi
     *
     * @param  string $correlation_id Идентификатор, связывающий запрос создания с запросом получения ответа (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function salaryGetPaymentRegistryCreateResultAsyncWithHttpInfo($correlation_id)
    {
        $returnType = '\OpenAPI\Client\tinkoff\api\\CreatePaymentRegistryResultResponse';
        $request = $this->salaryGetPaymentRegistryCreateResultRequest($correlation_id);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    $responseBody = $response->getBody();
                    if ($returnType === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'salaryGetPaymentRegistryCreateResult'
     *
     * This oepration contains host(s) defined in the OpenAP spec. Use 'hostIndex' to select the host.
     * URL: https://business.tinkoff.ru/openapi
     *
     * @param  string $correlation_id Идентификатор, связывающий запрос создания с запросом получения ответа (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function salaryGetPaymentRegistryCreateResultRequest($correlation_id)
    {
        // verify the required parameter 'correlation_id' is set
        if ($correlation_id === null || (is_array($correlation_id) && count($correlation_id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $correlation_id when calling salaryGetPaymentRegistryCreateResult'
            );
        }

        $resourcePath = '/api/v1/salary/payment-registry/create/result';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        if ($correlation_id !== null) {
            if('form' === 'form' && is_array($correlation_id)) {
                foreach($correlation_id as $key => $value) {
                    $queryParams[$key] = $value;
                }
            }
            else {
                $queryParams['correlationId'] = $correlation_id;
            }
        }




        if ($multipart) {
            $headers = $this->headerSelector->selectHeadersForMultipart(
                ['application/json']
            );
        } else {
            $headers = $this->headerSelector->selectHeaders(
                ['application/json'],
                []
            );
        }

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif ($headers['Content-Type'] === 'application/json') {
                $httpBody = \GuzzleHttp\json_encode($formParams);

            } else {
                // for HTTP post (form)
                $httpBody = \GuzzleHttp\Psr7\build_query($formParams);
            }
        }

        // this endpoint requires Bearer authentication (access token)
        if ($this->config->getAccessToken() !== null) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHosts = ["https://business.tinkoff.ru/openapi"];
        if ($this->hostIndex < 0 || $this->hostIndex >= sizeof($operationHosts)) {
            throw new \InvalidArgumentException("Invalid index {$this->hostIndex} when selecting the host. Must be less than ".sizeof($operationHosts));
        }
        $operationHost = $operationHosts[$this->hostIndex];

        $query = \GuzzleHttp\Psr7\build_query($queryParams);
        return new Request(
            'GET',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation salaryPaymentRegistrySubmit
     *
     * 🔒 Подписание платёжного реестра сотрудников
     *
     * @param  \OpenAPI\Client\tinkoff\api\\PaymentRegistrySubmitRequest $payment_registry_submit_request payment_registry_submit_request (required)
     *
     * @throws \OpenAPI\Client\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return \OpenAPI\Client\tinkoff\api\\PaymentRegistrySubmitResponse|\OpenAPI\Client\tinkoff\api\\InvalidRequestResponse|\OpenAPI\Client\tinkoff\api\\AuthenticationFailedResponse|\OpenAPI\Client\tinkoff\api\\AuthorizationFailedResponse|\OpenAPI\Client\tinkoff\api\\BusinessErrorResponse|\OpenAPI\Client\tinkoff\api\\TooManyRequestsErrorResponse|\OpenAPI\Client\tinkoff\api\\InternalServerErrorResponse
     */
    public function salaryPaymentRegistrySubmit($payment_registry_submit_request)
    {
        list($response) = $this->salaryPaymentRegistrySubmitWithHttpInfo($payment_registry_submit_request);
        return $response;
    }

    /**
     * Operation salaryPaymentRegistrySubmitWithHttpInfo
     *
     * 🔒 Подписание платёжного реестра сотрудников
     *
     * @param  \OpenAPI\Client\tinkoff\api\\PaymentRegistrySubmitRequest $payment_registry_submit_request (required)
     *
     * @throws \OpenAPI\Client\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \OpenAPI\Client\tinkoff\api\\PaymentRegistrySubmitResponse|\OpenAPI\Client\tinkoff\api\\InvalidRequestResponse|\OpenAPI\Client\tinkoff\api\\AuthenticationFailedResponse|\OpenAPI\Client\tinkoff\api\\AuthorizationFailedResponse|\OpenAPI\Client\tinkoff\api\\BusinessErrorResponse|\OpenAPI\Client\tinkoff\api\\TooManyRequestsErrorResponse|\OpenAPI\Client\tinkoff\api\\InternalServerErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function salaryPaymentRegistrySubmitWithHttpInfo($payment_registry_submit_request)
    {
        $request = $this->salaryPaymentRegistrySubmitRequest($payment_registry_submit_request);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            }

            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    $response->getBody()
                );
            }

            $responseBody = $response->getBody();
            switch($statusCode) {
                case 200:
                    if ('\OpenAPI\Client\tinkoff\api\\PaymentRegistrySubmitResponse' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\tinkoff\api\\PaymentRegistrySubmitResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 400:
                    if ('\OpenAPI\Client\tinkoff\api\\InvalidRequestResponse' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\tinkoff\api\\InvalidRequestResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 401:
                    if ('\OpenAPI\Client\tinkoff\api\\AuthenticationFailedResponse' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\tinkoff\api\\AuthenticationFailedResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 403:
                    if ('\OpenAPI\Client\tinkoff\api\\AuthorizationFailedResponse' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\tinkoff\api\\AuthorizationFailedResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 422:
                    if ('\OpenAPI\Client\tinkoff\api\\BusinessErrorResponse' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\tinkoff\api\\BusinessErrorResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 429:
                    if ('\OpenAPI\Client\tinkoff\api\\TooManyRequestsErrorResponse' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\tinkoff\api\\TooManyRequestsErrorResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 500:
                    if ('\OpenAPI\Client\tinkoff\api\\InternalServerErrorResponse' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\tinkoff\api\\InternalServerErrorResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            $returnType = '\OpenAPI\Client\tinkoff\api\\PaymentRegistrySubmitResponse';
            $responseBody = $response->getBody();
            if ($returnType === '\SplFileObject') {
                $content = $responseBody; //stream goes to serializer
            } else {
                $content = (string) $responseBody;
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];

        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\tinkoff\api\\PaymentRegistrySubmitResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\tinkoff\api\\InvalidRequestResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\tinkoff\api\\AuthenticationFailedResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\tinkoff\api\\AuthorizationFailedResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\tinkoff\api\\BusinessErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 429:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\tinkoff\api\\TooManyRequestsErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\tinkoff\api\\InternalServerErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation salaryPaymentRegistrySubmitAsync
     *
     * 🔒 Подписание платёжного реестра сотрудников
     *
     * @param  \OpenAPI\Client\tinkoff\api\\PaymentRegistrySubmitRequest $payment_registry_submit_request (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function salaryPaymentRegistrySubmitAsync($payment_registry_submit_request)
    {
        return $this->salaryPaymentRegistrySubmitAsyncWithHttpInfo($payment_registry_submit_request)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation salaryPaymentRegistrySubmitAsyncWithHttpInfo
     *
     * 🔒 Подписание платёжного реестра сотрудников
     *
     * @param  \OpenAPI\Client\tinkoff\api\\PaymentRegistrySubmitRequest $payment_registry_submit_request (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function salaryPaymentRegistrySubmitAsyncWithHttpInfo($payment_registry_submit_request)
    {
        $returnType = '\OpenAPI\Client\tinkoff\api\\PaymentRegistrySubmitResponse';
        $request = $this->salaryPaymentRegistrySubmitRequest($payment_registry_submit_request);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    $responseBody = $response->getBody();
                    if ($returnType === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'salaryPaymentRegistrySubmit'
     *
     * @param  \OpenAPI\Client\tinkoff\api\\PaymentRegistrySubmitRequest $payment_registry_submit_request (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function salaryPaymentRegistrySubmitRequest($payment_registry_submit_request)
    {
        // verify the required parameter 'payment_registry_submit_request' is set
        if ($payment_registry_submit_request === null || (is_array($payment_registry_submit_request) && count($payment_registry_submit_request) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $payment_registry_submit_request when calling salaryPaymentRegistrySubmit'
            );
        }

        $resourcePath = '/api/v1/salary/payment-registry/submit';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;





        if ($multipart) {
            $headers = $this->headerSelector->selectHeadersForMultipart(
                ['application/json']
            );
        } else {
            $headers = $this->headerSelector->selectHeaders(
                ['application/json'],
                ['application/json']
            );
        }

        // for model (json/xml)
        if (isset($payment_registry_submit_request)) {
            if ($headers['Content-Type'] === 'application/json') {
                $httpBody = \GuzzleHttp\json_encode(ObjectSerializer::sanitizeForSerialization($payment_registry_submit_request));
            } else {
                $httpBody = $payment_registry_submit_request;
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif ($headers['Content-Type'] === 'application/json') {
                $httpBody = \GuzzleHttp\json_encode($formParams);

            } else {
                // for HTTP post (form)
                $httpBody = \GuzzleHttp\Psr7\build_query($formParams);
            }
        }

        // this endpoint requires Bearer authentication (access token)
        if ($this->config->getAccessToken() !== null) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $query = \GuzzleHttp\Psr7\build_query($queryParams);
        return new Request(
            'POST',
            $this->config->getHost() . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation salaryPaymentRegistrySubmitResult
     *
     * 🔒 Получить результат подписания платёжного реестра сотрудников
     *
     * @param  string $correlation_id Идентификатор, связывающий запрос создания с запросом получения ответа (required)
     *
     * @throws \OpenAPI\Client\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return \OpenAPI\Client\tinkoff\api\\PaymentRegistrySubmitResultResponse|\OpenAPI\Client\tinkoff\api\\InvalidRequestResponse|\OpenAPI\Client\tinkoff\api\\AuthenticationFailedResponse|\OpenAPI\Client\tinkoff\api\\AuthorizationFailedResponse|\OpenAPI\Client\tinkoff\api\\BusinessErrorResponse|\OpenAPI\Client\tinkoff\api\\TooManyRequestsErrorResponse|\OpenAPI\Client\tinkoff\api\\InternalServerErrorResponse
     */
    public function salaryPaymentRegistrySubmitResult($correlation_id)
    {
        list($response) = $this->salaryPaymentRegistrySubmitResultWithHttpInfo($correlation_id);
        return $response;
    }

    /**
     * Operation salaryPaymentRegistrySubmitResultWithHttpInfo
     *
     * 🔒 Получить результат подписания платёжного реестра сотрудников
     *
     * @param  string $correlation_id Идентификатор, связывающий запрос создания с запросом получения ответа (required)
     *
     * @throws \OpenAPI\Client\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \OpenAPI\Client\tinkoff\api\\PaymentRegistrySubmitResultResponse|\OpenAPI\Client\tinkoff\api\\InvalidRequestResponse|\OpenAPI\Client\tinkoff\api\\AuthenticationFailedResponse|\OpenAPI\Client\tinkoff\api\\AuthorizationFailedResponse|\OpenAPI\Client\tinkoff\api\\BusinessErrorResponse|\OpenAPI\Client\tinkoff\api\\TooManyRequestsErrorResponse|\OpenAPI\Client\tinkoff\api\\InternalServerErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function salaryPaymentRegistrySubmitResultWithHttpInfo($correlation_id)
    {
        $request = $this->salaryPaymentRegistrySubmitResultRequest($correlation_id);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            }

            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    $response->getBody()
                );
            }

            $responseBody = $response->getBody();
            switch($statusCode) {
                case 200:
                    if ('\OpenAPI\Client\tinkoff\api\\PaymentRegistrySubmitResultResponse' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\tinkoff\api\\PaymentRegistrySubmitResultResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 400:
                    if ('\OpenAPI\Client\tinkoff\api\\InvalidRequestResponse' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\tinkoff\api\\InvalidRequestResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 401:
                    if ('\OpenAPI\Client\tinkoff\api\\AuthenticationFailedResponse' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\tinkoff\api\\AuthenticationFailedResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 403:
                    if ('\OpenAPI\Client\tinkoff\api\\AuthorizationFailedResponse' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\tinkoff\api\\AuthorizationFailedResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 422:
                    if ('\OpenAPI\Client\tinkoff\api\\BusinessErrorResponse' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\tinkoff\api\\BusinessErrorResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 429:
                    if ('\OpenAPI\Client\tinkoff\api\\TooManyRequestsErrorResponse' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\tinkoff\api\\TooManyRequestsErrorResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 500:
                    if ('\OpenAPI\Client\tinkoff\api\\InternalServerErrorResponse' === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\OpenAPI\Client\tinkoff\api\\InternalServerErrorResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            $returnType = '\OpenAPI\Client\tinkoff\api\\PaymentRegistrySubmitResultResponse';
            $responseBody = $response->getBody();
            if ($returnType === '\SplFileObject') {
                $content = $responseBody; //stream goes to serializer
            } else {
                $content = (string) $responseBody;
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];

        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\tinkoff\api\\PaymentRegistrySubmitResultResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\tinkoff\api\\InvalidRequestResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\tinkoff\api\\AuthenticationFailedResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\tinkoff\api\\AuthorizationFailedResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\tinkoff\api\\BusinessErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 429:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\tinkoff\api\\TooManyRequestsErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\OpenAPI\Client\tinkoff\api\\InternalServerErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation salaryPaymentRegistrySubmitResultAsync
     *
     * 🔒 Получить результат подписания платёжного реестра сотрудников
     *
     * @param  string $correlation_id Идентификатор, связывающий запрос создания с запросом получения ответа (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function salaryPaymentRegistrySubmitResultAsync($correlation_id)
    {
        return $this->salaryPaymentRegistrySubmitResultAsyncWithHttpInfo($correlation_id)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation salaryPaymentRegistrySubmitResultAsyncWithHttpInfo
     *
     * 🔒 Получить результат подписания платёжного реестра сотрудников
     *
     * @param  string $correlation_id Идентификатор, связывающий запрос создания с запросом получения ответа (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function salaryPaymentRegistrySubmitResultAsyncWithHttpInfo($correlation_id)
    {
        $returnType = '\OpenAPI\Client\tinkoff\api\\PaymentRegistrySubmitResultResponse';
        $request = $this->salaryPaymentRegistrySubmitResultRequest($correlation_id);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    $responseBody = $response->getBody();
                    if ($returnType === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = (string) $responseBody;
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'salaryPaymentRegistrySubmitResult'
     *
     * @param  string $correlation_id Идентификатор, связывающий запрос создания с запросом получения ответа (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function salaryPaymentRegistrySubmitResultRequest($correlation_id)
    {
        // verify the required parameter 'correlation_id' is set
        if ($correlation_id === null || (is_array($correlation_id) && count($correlation_id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $correlation_id when calling salaryPaymentRegistrySubmitResult'
            );
        }

        $resourcePath = '/api/v1/salary/payment-registry/submit/result';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        if ($correlation_id !== null) {
            if('form' === 'form' && is_array($correlation_id)) {
                foreach($correlation_id as $key => $value) {
                    $queryParams[$key] = $value;
                }
            }
            else {
                $queryParams['correlationId'] = $correlation_id;
            }
        }




        if ($multipart) {
            $headers = $this->headerSelector->selectHeadersForMultipart(
                ['application/json']
            );
        } else {
            $headers = $this->headerSelector->selectHeaders(
                ['application/json'],
                []
            );
        }

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif ($headers['Content-Type'] === 'application/json') {
                $httpBody = \GuzzleHttp\json_encode($formParams);

            } else {
                // for HTTP post (form)
                $httpBody = \GuzzleHttp\Psr7\build_query($formParams);
            }
        }

        // this endpoint requires Bearer authentication (access token)
        if ($this->config->getAccessToken() !== null) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $query = \GuzzleHttp\Psr7\build_query($queryParams);
        return new Request(
            'GET',
            $this->config->getHost() . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Create http client option
     *
     * @throws \RuntimeException on file opening failure
     * @return array of http client options
     */
    protected function createHttpClientOption()
    {
        $options = [];
        if ($this->config->getDebug()) {
            $options[RequestOptions::DEBUG] = fopen($this->config->getDebugFile(), 'a');
            if (!$options[RequestOptions::DEBUG]) {
                throw new \RuntimeException('Failed to open the debug file: ' . $this->config->getDebugFile());
            }
        }

        return $options;
    }
}
