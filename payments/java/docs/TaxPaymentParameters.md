

# TaxPaymentParameters

Реквизиты для уплаты налогов

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**docDate** | **String** | Дата налогового документа. Поле платежки 109. Формат — ДД.ММ.ГГГГ или 0. Подробнее :https://www.nalog.ru/rn40/news/tax_doc_news/4604512/ | 
**docNumber** | **String** | Номер налогового документа. Поле платежки 108. Поле может принимать следующие значения: 0 - погашается текущая задолженность по налогам (страховым сборам) или речь идет о добровольной уплате недоимки. ТР - Означает выставленное требование налоговых органов. При обнаружении недоимки инспекция вправе прислать налогоплательщику документ с указанием вида налога или страхового взноса и суммы на перечисление. РС - Погашение задолженности в рассрочку. В соответствии с п. 3 ст. 61 НК РФ налогоплательщики, не имеющие возможность погасить задолженность перед бюджетом вовремя, могут получить рассрочку. ОТ - Номер решения об отсрочке текущего платежа, принятого налоговым органом. В некоторых обстоятельствах субъекты не имеют возможности уплатить сумму начисленного налога полностью в срок. РТ - Указывается номер принятого налоговиками решения о реструктуризации. В случае невозможности погашения организацией задолженности по налогам и пеням она может воспользоваться льготными условиями в соответствии с принятым графиком погашения долговых обязательств. ПБ - Номер дела по вынесенному арбитражным судом решению. Иногда между налогоплательщиками и контролирующими органами возникают споры по поводу правильности начисления и полноты уплаты бюджетных обязательств. ПР - Используется номер решения о приостановлении взыскания при погашении плательщиком этой задолженности. АП - Подразумевается номер акта выездной или камеральной проверки, в результате которой произошло доначисление налогов, пеней и штрафов. АР - Оплата по номеру исполнительного документа, выданного в результате возбужденного дела. ИН - Номер решения о предоставлении инвестиционного налогового кредита — еще одного способа изменения установленного срока уплаты по налогам. Помимо отсрочки исполнения платежа, он несет в себе некоторые признаки бюджетного кредитования с последующей уплатой начисленных процентов и основной суммы долга. ТЛ - Проставляется номер определения арбитражного суда, удовлетворяющего заявление о погашении требований к должнику. Подробнее: https://glavkniga.ru/situations/s503634 | 
**evidence** | **String** | Основание налогового платежа. Поле платежки 106. Поле может принимать следующие значения: ТП – платежи текущего года; ЗД – добровольное погашение задолженности по истекшим налоговым, расчетным (отчетным) периодам при отсутствии требования налогового органа об уплате налогов (сборов); БФ – текущий платеж физического лица – клиента банка (владельца счета), уплачиваемый со своего банковского счета; ТР – погашение задолженности по требованию налогового органа об уплате налогов (сборов); РС – погашение рассроченной задолженности; ОТ – погашение отсроченной задолженности; РТ – погашение реструктурируемой задолженности; ПБ – погашение должником задолженности в ходе процедур, применяемых в деле о банкротстве; ПР – погашение задолженности, приостановленной к взысканию; АП – погашение задолженности по акту проверки; АР – погашение задолженности по исполнительному документу; ИН – погашение инвестиционного налогового кредита; ТЛ - погашение учредителем (участником) должника, собственником имущества должника - унитарного предприятия или третьим лицом задолженности в ходе процедур, применяемых в деле о банкротстве; ЗТ - погашение текущей задолженности в ходе процедур, применяемых в деле о банкротстве. 0 - при незнании основания платежа. Подробнее: https://glavkniga.ru/situations/s503643?mailing_uuid&#x3D;e25da053-42e1-46c5-8559-f7407dc0c8ca&amp;utm_news_date&#x3D;2020-09-22 | 
**kbk** | **String** | Код бюджетной классификации. Поле платежки 104. Подробнее: https://buhguru.com/spravka-info/kbk-2021.html | 
**oktmo** | **String** | Код ОКТМО территории, на которой мобилизуются денежные средства от уплаты налога, сбора и иного платежа. Поле платежки 105.  Подробнее: https://nalog-nalog.ru/uplata_nalogov/rekvizity_dlya_uplaty_nalogov_vznosov/oktmo_v_platezhnom_poruchenii_nyuansy/ | 
**payerStatus** | **String** | Статус составителя расчетного документа. Поле платежки 101. ВАЖНО: При оплате налога за третьих лиц указывается налоговый статус человека, за которого происходит оплата. Поле может принимать следующие значения: 01 — налогоплательщик (плательщик сборов) — юрлицо; 02 — налоговый агент; 08 — плательщик-юрлицо (ИП), осуществляющий уплату страховых взносов и иных платежей в бюджетную систему РФ; 09 — налогоплательщик (плательщик сборов) — ИП; 14 — налогоплательщик, производящий выплаты физическим лицам; 24 — плательщик-физлицо, осуществляющий уплату страховых взносов и иных платежей в бюджетную систему РФ. Подробнее: https://www.nalog.ru/create_business/ip/in_progress/payment_order/step17/ | 
**period** | **String** | Налоговый период. Поле платежки 107.  Формат — ДД.ММ.ГГГГ, первые два символа могут быть буквами или цифрами; а также поле может быть передано 0.  Подробнее: https://www.nalog.ru/rn40/news/tax_doc_news/6181298/ | 
**thirdParty** | [**TaxThirdParty**](TaxThirdParty.md) |  |  [optional]


