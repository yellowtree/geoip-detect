import Record from "../models/record"

// From Maxmind City Lite
export function getTestRecord(locales) {
    return new Record({
            "country": {
                "iso_code": "DE",
                "iso_code3": "DEU",
                "geoname_id": 2921044,
                "names": {
                    "en": "Germany",
                    "de": "Deutschland",
                    "it": "Germania",
                    "es": "Alemania",
                    "fr": "Allemagne",
                    "ja": "ドイツ連邦共和国",
                    "pt-BR": "Alemanha",
                    "ru": "Германия",
                    "zh-CN": "德国"
                }
            },
            "continent": {
                "code": "EU",
                "names": {
                    "en": "Europe",
                    "de": "Europa",
                    "it": "Europa",
                    "es": "Europa",
                    "fr": "Europe",
                    "ja": "ヨーロッパ",
                    "pt-BR": "Europa",
                    "ru": "Европа",
                    "zh-CN": "欧洲"
                },
                "geoname_id": 6255148
            },
            "location": {
                "latitude": 50.1333,
                "longitude": 8.55,
                "time_zone": "Europe/Berlin"
            },
            "extra": {
                "currency_code": "EUR",
                "source": "manual",
                "cached": 0,
                "error": "",
                "country_iso_code3": "DEU",
                "flag": "🇩🇪",
                "tel": "+49"
            },
            "city": {
                "geoname_id": 2929134,
                "names": {
                    "en": "Eschborn",
                    "ja": "エシュボルン",
                    "ru": "Эшборн",
                    "zh-CN": "埃施博尔恩"
                }
            },
            "registered_country": {
                "geoname_id": 2921044,
                "iso_code": "DE",
                "names": {
                    "de": "Deutschland",
                    "en": "Germany",
                    "es": "Alemania",
                    "fr": "Allemagne",
                    "ja": "ドイツ連邦共和国",
                    "pt-BR": "Alemanha",
                    "ru": "Германия",
                    "zh-CN": "德国"
                }
            },
            "subdivisions": [
                {
                    "geoname_id": 2905330,
                    "iso_code": "HE",
                    "names": {
                        "de": "Hessen",
                        "en": "Hesse",
                        "es": "Hessen",
                        "fr": "Hesse"
                    }
                }
            ],
            "traits": {
                "ip_address": "88.64.140.1",
                "prefix_len": 24
            },
            "is_empty": false,
            "most_specific_subdivision": {
                "geoname_id": 2905330,
                "iso_code": "HE",
                "names": {
                    "de": "Hessen",
                    "en": "Hesse",
                    "es": "Hessen",
                    "fr": "Hesse"
                }
            }
        }, locales);
}

export function getTestRecordError() {
    return new Record({
            "traits": {
                "ip_address": "127.0.0.1"
            },
            "is_empty": true,
            "extra": {
                "source": "manual",
                "cached": 0,
                "error": "No reader was found. Check if the configuration is complete and correct."
            }
        });
}