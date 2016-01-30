( function() {
    tinymce.create( 'tinymce.plugins.wp_wiki_tooltip', {

            init : function( editor, url ) {
            editor.addButton( 'wp_wiki_tooltip', {
                icon: 'wp_wiki_tooltip',
                title : editor.getLang( 'wp_wiki_tooltip.title' ),
                cmd : 'wp_wiki_tooltip',
                image : url + '/../images/wp-wiki-tooltip-mce-icon.jpg'
            });

            editor.addCommand( 'wp_wiki_tooltip', function( ui, val ) {
                var link = editor.selection.getContent({ format: 'text' });

                var bases = new Array();
                bases.push({text: editor.getLang( 'wp_wiki_tooltip.base_standard' ), value: '0'});
                bases.push({text: '---', value: '-'});
                for( elem in wp_wiki_tooltip_mce.wiki_urls.data ) {
                    if( elem != '###NEWID###' ) {
                        var value = wp_wiki_tooltip_mce.wiki_urls.data[elem]['id'];
                        bases.push({text: value, value: value});
                    }
                }

                editor.windowManager.open({
                    title: editor.getLang( 'wp_wiki_tooltip.title' ),
                    body: [
                        {//add input field for link text
                            type: 'textbox',
                            name: 'link',
                            label: editor.getLang( 'wp_wiki_tooltip.link_label' ),
                            value: link,
                            tooltip: editor.getLang( 'wp_wiki_tooltip.link_tooltip' )
                        },
                        {//add input field for Wiki page title
                            type: 'textbox',
                            name: 'title',
                            label: editor.getLang( 'wp_wiki_tooltip.title_label' ),
                            value: link,
                            tooltip: editor.getLang( 'wp_wiki_tooltip.title_tooltip' )
                        },
                        {//add select field for Wiki base
                            type: 'listbox',
                            name: 'base',
                            label: editor.getLang( 'wp_wiki_tooltip.base_label' ),
                            values: bases,
                            tooltip: editor.getLang( 'wp_wiki_tooltip.base_tooltip' )
                        },
                        {//add checkbox for thumbnail
                            type: 'listbox',
                            name: 'thumb',
                            label: editor.getLang( 'wp_wiki_tooltip.thumb_label' ),
                            values: [
                                {text: editor.getLang( 'wp_wiki_tooltip.thumb_default' ), value: '0'},
                                {text: '---', value: '-'},
                                {text: editor.getLang( 'wp_wiki_tooltip.thumb_yes' ), value: 'on'},
                                {text: editor.getLang( 'wp_wiki_tooltip.thumb_no' ), value: 'off'}
                            ],
                            tooltip: editor.getLang( 'wp_wiki_tooltip.thumb_tooltip' )
                        }
                    ],
                    onsubmit: function (e) { //when the ok button is clicked
                        var shortcode = '[wiki';

                        if( typeof e.data.title != 'undefined' && e.data.title != e.data.link )
                            shortcode += ' title="' + e.data.title + '"';

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
                authorurl : 'http://n1da.net',
                infourl : 'http://n1da.net/specials/wp-wiki-tooltip/',
                version : "1.0"
            };
        }
    });

    // Register plugin
    tinymce.PluginManager.add( 'wp_wiki_tooltip', tinymce.plugins.wp_wiki_tooltip );
})();