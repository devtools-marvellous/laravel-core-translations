# Translations Api

- [x] [Back to the docs root](/readme.md)
- [x] [Get List](#get-list)
- [x] [Store one](#get-list)
- [x] [Update one](#get-list)
- [x] [Destroy one](#get-list)

## Get List

### Request pattern

```bash
curl --request GET \
    --url {backend_url}/translations
```

### Body fields

Empty

### Possible tips

You can use several scopes to filtrate a result:
- `searchByTranslationsInAnyLocale` - found any translations compare to search string. Example:
    - ?scopes=[{"name":"searchByTranslationsInAnyLocale","parameters":[{YOUR_SEARCH_STRING}]}]

## Store one

### Request pattern

```bash
curl --request POST \
    --url {backend_url}/translations
```

### Body fields

| Name | Possible values| Validation tips | Comments
| :--- | :------------- | :---- | :----      
| group | - | **required**, **string** | Group name, used in translation key generation, if equals to `*`, then group ignored inside trans key generation. Can't contain dots.
| key | - | **required**, **string** | Key name for trans key generation, can contain dots. Key should be unique inside same group.
| text | {"en": "some trans"} | **required**, **string** | Object of translations for given group and key.
| translatable_id | - | **nullable**, **sometimes**, **string** | ID of the connected model. Should be marked as urgent for changes. User, who changing this field should know what he doing.
| translatable_type | - | **nullable**, **sometimes**, **string** | Class name of connected model. Should be marked as urgent for changes. User, who changing this field should know what he doing.

Last two fields should be present in backend under checkbox - Do you want to change model connection?. If user tap on it then additional fields will shown.

For example `group=api` and `key=my.test`, then backend code will be generated into - `db-trans::api.my.test`

### Possible tips

Empty

## Update one

### Request pattern

```bash
curl --request PUT \
    --url {backend_url}/translations/{ID}
```

### Body fields

| Name | Possible values| Validation tips | Comments
| :--- | :------------- | :---- | :----      
| group | - | **required**, **string** | Group name, used in translation key generation, if equals to `*`, then group ignored inside trans key generation. Can't contain dots.
| key | - | **required**, **string** | Key name for trans key generation, can contain dots. Key should be unique inside same group.
| text | {"en": "some trans"} | **required**, **string** | Object of translations for given group and key.
| translatable_id | - | **nullable**, **sometimes**, **string** | ID of the connected model. Should be marked as urgent for changes. User, who changing this field should know what he doing.
| translatable_type | - | **nullable**, **sometimes**, **string** | Class name of connected model. Should be marked as urgent for changes. User, who changing this field should know what he doing.

Last two fields should be present in backend under checkbox - Do you want to change model connection?. If user tap on it then additional fields will shown.

For example `group=api` and `key=my.test`, then backend code will be generated into - `db-trans::api.my.test`

### Possible tips

Empty

## Destroy one

> Client side Should open confirmation form before request sending. If user delete model assigned translation, then that will be a problem.

### Request pattern

```bash
curl --request DELETE \
    --url {backend_url}/translations/{ID}
```

### Body fields

Empty

### Possible tips

Empty
