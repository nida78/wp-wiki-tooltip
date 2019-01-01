function getLangString( editor, key ) {
    if( typeof wwtj_strings === 'undefined' ) {
        return editor.getLang( 'wp_wiki_tooltip.' + key );
    } else {
        return wwtj_strings[ key ];
    }
}

( function() {
    tinymce.create( 'tinymce.plugins.wp_wiki_tooltip', {

        init : function( editor, url ) {
            editor.addButton( 'wp_wiki_tooltip', {
                icon: 'wp_wiki_tooltip',
                title : getLangString( editor, 'title' ),
                cmd : 'wp_wiki_tooltip',
                image : url + '/../images/wp-wiki-tooltip-mce-icon.jpg'
            });

            editor.addCommand( 'wp_wiki_tooltip', function( ui, val ) {
                var link = editor.selection.getContent({ format: 'text' });

                var bases = new Array();
                bases.push( { text: getLangString( editor, 'base_standard' ), value: '0' } );
                bases.push( { text: '---', value: '-' } );
                for( elem in wp_wiki_tooltip_mce.wiki_urls.data ) {
                    if( elem != '###NEWID###' ) {
                        var value = wp_wiki_tooltip_mce.wiki_urls.data[elem]['id'];
                        bases.push( { text: value, value: value } );
                    }
                }

                editor.windowManager.open({
                    title: getLangString( editor, 'title' ),
                    body: [
                        {//add input field for link text
                            type: 'textbox',
                            name: 'link',
                            label: getLangString( editor, 'link_label' ),
                            value: link,
                            tooltip: getLangString( editor, 'link_tooltip' )
                        },
                        {//add input field for Wiki page title
                            type: 'textbox',
                            name: 'title',
                            label: getLangString( editor, 'title_label' ),
                            value: link,
                            tooltip: getLangString( editor, 'title_tooltip' )
                        },
                        {//add input field for section title
                            type: 'textbox',
                            name: 'section',
                            label: getLangString( editor, 'section_label' ),
                            value: '',
                            tooltip: getLangString( editor, 'section_tooltip' )
                        },
                        {//add select field for Wiki base
                            type: 'listbox',
                            name: 'base',
                            label: getLangString( editor, 'base_label' ),
                            values: bases,
                            tooltip: getLangString( editor, 'base_tooltip' )
                        },
                        {//add checkbox for thumbnail
                            type: 'listbox',
                            name: 'thumb',
                            label: getLangString( editor, 'thumb_label' ),
                            values: [
                                { text: getLangString( editor, 'thumb_default' ), value: '0' },
                                { text: '---', value: '-' },
                                { text: getLangString( editor, 'thumb_yes' ), value: 'on' },
                                { text: getLangString( editor, 'thumb_no' ), value: 'off' }
                            ],
                            tooltip: getLangString( editor, 'thumb_tooltip' )
                        }
                    ],
                    onsubmit: function (e) { //when the ok button is clicked
                        var shortcode = '[wiki';

                        if( typeof e.data.title != 'undefined' && e.data.title != e.data.link )
                            shortcode += ' title="' + e.data.title + '"';

                        if( typeof e.data.section != 'undefined' && e.data.section != '' )
                            shortcode += ' section="' + e.data.section + '"';

                        if( e.data.base != '0' && e.data.base != '-' )
                            shortcode += ' base="' + e.data.base + '"';

                        if( e.data.thumb != '0' && e.data.thumb != '-' )
                            shortcode += ' thumbnail="' + e.data.thumb + '"';

                        shortcode += ']' + e.data.link + '[/wiki]';
                        editor.insertContent( shortcode );
                    }
                });
            });
        },

        getInfo : function() {
            return {
                longname : 'WP Wiki Tooltip Buttons',
                author : 'nida78',
                authorurl : 'https://n1da.net',
                infourl : 'https://n1da.net/specials/wp-wiki-tooltip/',
                version : "1.2"
            };
        }
    });

    // Register plugin
    tinymce.PluginManager.add( 'wp_wiki_tooltip', tinymce.plugins.wp_wiki_tooltip );
})();