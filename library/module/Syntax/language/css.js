/**
 * CSS patterns
 *
 * @author Craig Campbell
 * @version 1.0.6
 */
Rainbow.extend('css', [
    {
        'name': 'comment',
        'pattern': /\/\*[\s\S]*?\*\//gm
    },
    {
        'name': 'constant.color',
        'pattern': /rgba?\([\d,\. ]+\)(?=;|,|\s)/gi
    },
    {
        'name': 'constant.color.hex-color',
        'pattern': /#([a-f0-9]{3}|[a-f0-9]{6})(?=;|,|\s)/gi
    },
    {
        'matches': {
            1: 'constant.numeric',
            2: 'keyword.unit'
        },
        'pattern': /(\d+)(px|cm|s|%)?/g
    },
    {
        'name': 'string',
        'pattern': /('|")(.*?)\1/g
    },
    {
        'name': 'support.css-property',
        'matches': {
            1: 'support.css-property.vendor-prefix'
        },
        'pattern': /(-o-|-moz-|-webkit-|-ms-)?[\w-]+(?=\s?:)(?!.*\{)/g
    },
    {
        'matches': {
            1: [
                {
                    'name': 'entity.name.sass',
                    'pattern': /&amp;/g
                },
                {
                    'name': 'direct-descendant',
                    'pattern': /&gt;/g
                },
                {
                    'name': 'entity.name.class',
                    'pattern': /\.[\w\-_]+/g
                },
                {
                    'name': 'entity.name.id',
                    'pattern': /\#[\w\-_]+/g
                },
                {
                    'name': 'entity.name.pseudo',
                    'pattern': /:[\w\-\(\)]+/g
                },
                {
                    'name': 'entity.name.tag',
                    'pattern': /\w+/g
                }
            ]
        },
        'pattern': /([\w\ ,:\.\#\&\;\-_\s]+)(?=.*\{)/gm
    },
    {
        'matches': {
            2: 'support.css-value.vendor-prefix',
            3: 'support.css-value'
        },
        'pattern': /(:|,)\s?(-o-|-moz-|-webkit-|-ms-)?([a-zA-Z-]*)(?=\b)(?!.*\{)/g
    },
    {
    	'name': 'support.important',
       	'pattern': /\!important(?=;|,|\}|\s)/g
    }
], true);
