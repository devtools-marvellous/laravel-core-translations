# Translations Api

- [x] [Back to the docs root](/readme.md)
- [x] [Get List](#get-list)

By default, configurable prefix is equals to - `AttractCores`. 
It can be changed in main settings, so check root documentation file for correct value.

Each response from the server with `core-translations` module will contain next data:
- `AttractCoresLocale` - header contain language code, for example, `en`. You also can send this header to server
in your requests. If this header exists in request, app locale will be changed to received from header, if possible.
- `AttractCoresTranslationLastUpdated` - header contains date and time with offset when `api` translations group was updated last time.
Ignore it, if your project doesn't use backend translations on client side.

> WARNING!!!! Request has throttle rate limiting set up. You can deal maximum 2 requests per minute for translation list.
 

## Get List

### Request pattern

> `PROTECTED_LIMIT` set to 1000. \
> Returned response contains only `api` group translations.

```bash
curl --request GET \
    --url {api_url}/translations
```

### Body fields

Empty

### Possible tips

Empty
