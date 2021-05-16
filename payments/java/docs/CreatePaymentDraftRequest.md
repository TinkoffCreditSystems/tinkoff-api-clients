

# CreatePaymentDraftRequest


## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**accountNumber** | **String** | Номер счета плательщика | 
**amount** | **BigDecimal** | Сумма платежа | 
**bankAcnt** | **String** | Номер счета получателя | 
**bankBik** | **String** | БИК банка | 
**collectionAmountNumber** | **BigDecimal** | Удержанная сумма в рублях. Актуально только при платежах в пользу физлиц. |  [optional]
**date** | **OffsetDateTime** | Дата и время исполнения платежа. Может быть в будущем, либо данное поле может быть не передано. **Если поле не передано, подписанный документ будет принят к исполнению немедленно.** |  [optional]
**documentNumber** | **String** | Номер распоряжения, определяемый клиентом. | 
**executionOrder** | **Integer** | Очередность платежа |  [optional]
**inn** | **String** | ИНН получателя. Если платеж выполняется в пользу физ. лица, и его ИНН неизвестен, в поле ИНН необходимо указать значение - \&quot;0\&quot;. Во всех остальных случаях необходимо передавать фактический ИНН получателя. | 
**kbk** | **String** | Код бюджетной классификации. Поле платежки 104. Если платеж не налоговый, в запросе поставьте 0. | 
**kpp** | **String** | КПП. Если у получателя платежа нет КПП (например, это физлицо или ИП), то укажите КПП \&quot;0\&quot; | 
**oktmo** | **String** | Код ОКТМО территории, на которой мобилизуются денежные средства от уплаты налога, сбора и иного платежа. Если платеж не налоговый, в запросе поставьте 0. | 
**paymentPurpose** | **String** | Назначение платежа | 
**recipientCorrAccountNumber** | **String** | Корреспондентский счёт банка получателя. В случае налогового платежа обязательно указывается номер счета банка получателя средств (номер банковского счета, входящего в состав единого казначейского счета (ЕКС)) (подробнее https://www.nalog.ru/rn50/news/activities_fts/10104005/) |  [optional]
**recipientName** | **String** | Получатель | 
**revenueTypeCode** | [**RevenueTypeCodeEnum**](#RevenueTypeCodeEnum) | Код вида выплаты. Обязательно заполняется при платежах в пользу физлиц. Подробнее: https://www.audit-it.ru/news/account/1013406.html |  [optional]
**taxDocDate** | **String** | Дата налогового документа. Поле платежки 109. Формат — ДД.ММ.ГГГГ. Если платеж не налоговый, в запросе поставьте 0. | 
**taxDocNumber** | **String** | Номер налогового документа. Поле платежки 108. Если платеж не налоговый, в запросе поставьте 0. | 
**taxEvidence** | **String** | Основание налогового платежа. Поле платежки 106. Если платеж не налоговый, в запросе поставьте 0. | 
**taxPayerStatus** | **String** | Статус составителя расчетного документа. Поле платежки 101. Если платеж не налоговый, в запросе поставьте 0. | 
**taxPeriod** | **String** | Налоговый период. Поле платежки 107. Формат — ДД.ММ.ГГГГ, первые два символа могут быть буквами. Если платеж не налоговый, в запросе поставьте 0. | 
**uin** | **String** | Уникальный идентификатор платежа. Если платеж не налоговый, в запросе поставьте 0. | 



## Enum: RevenueTypeCodeEnum

Name | Value
---- | -----
_1 | &quot;1&quot;
_2 | &quot;2&quot;
_3 | &quot;3&quot;


