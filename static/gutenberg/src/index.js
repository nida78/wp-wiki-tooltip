import { applyFormat, registerFormatType, toggleFormat, useAnchor } from '@wordpress/rich-text';
import { Popover, SelectControl, TextControl, ToolbarGroup, ToolbarButton } from '@wordpress/components';
import { BlockControls } from '@wordpress/block-editor';
import { __, _x } from "@wordpress/i18n";

import './editor.scss';

const WikiFormatName = 'wp-wiki-tooltip/tooltip-edit';
const WikiFormatClass = 'wiki-tooltip-has-data';
const WikiFormatTag = 'wiki';

let BaseList = [];
BaseList.push( { value: 'standard', label: __('Standard base', 'wp-wiki-tooltip') } )
BaseList.push( { value: '', label: '---', disabled: true } )
for( let elem in wp_wiki_tooltip_mce.wiki_urls.data ) {
    if( elem !== '###NEWID###' ) {
        let value = wp_wiki_tooltip_mce.wiki_urls.data[ elem ][ 'id' ];
        BaseList.push( { value: value, label: value } );
    }
}

const WikiTooltipEdit = ( props ) => {

    const settings = {
        name: WikiFormatName,
        tagName: WikiFormatTag,
        className: WikiFormatClass,
    };

    const { contentRef, isActive, value } = props;

    const getAnchor = () => {
        let newAnchor = useAnchor( { editableContentElement: contentRef.current, value: value, settings: settings } );

        if( isActive && ! ( newAnchor instanceof HTMLUnknownElement )  ) {
            // try to get text selection
            const selection = document.defaultView.getSelection();
            newAnchor = selection.rangeCount > 0 ? selection.getRangeAt(0) : null;
        }

        return newAnchor;
    }

    const anchorRef = getAnchor();

    const tooltipValue = {
        title: ( props.activeAttributes.title === undefined ) ? '' : props.activeAttributes.title,
        section: ( props.activeAttributes.section === undefined ) ? '' : props.activeAttributes.section,
        base: ( props.activeAttributes.base === undefined || props.activeAttributes.base === '' ) ? 'standard' : props.activeAttributes.base,
        thumbnail: ( props.activeAttributes.thumbnail === undefined || props.activeAttributes.thumbnail === '' ) ? 'standard' : props.activeAttributes.thumbnail,
    };

    const onChangeTitle = ( value ) => {
        onChangeTooltip( 'title', value );
    }

    const onChangeSection = ( value ) => {
        onChangeTooltip( 'section', value );
    }

    const onChangeBase = ( value ) => {
        onChangeTooltip( 'base', value );
    }

    const onChangeThumbnail = ( value ) => {
        onChangeTooltip( 'thumbnail', value );
    }

    const onChangeTooltip = ( attribute, newValue ) => {
        const newTooltipValues = {
            title: '',
            section: '',
            base: '',
            thumbnail: '',
        };

        // read current format data
        if ( props.activeAttributes ) {
            if ( props.activeAttributes.title ) {
                newTooltipValues.title = props.activeAttributes.title;
            }
            if ( props.activeAttributes.section ) {
                newTooltipValues.section = props.activeAttributes.section;
            }
            if ( props.activeAttributes.base ) {
                newTooltipValues.base = props.activeAttributes.base;
            }
            if ( props.activeAttributes.thumbnail ) {
                newTooltipValues.thumbnail = props.activeAttributes.thumbnail;
            }
        }

        // update changed values
        if ( 'title' === attribute ) {
            newTooltipValues.title = newValue;
        } else if ( 'section' === attribute ) {
            newTooltipValues.section = newValue;
        } else if ( 'base' === attribute ) {
            newTooltipValues.base = newValue;
        } else if ( 'thumbnail' === attribute ) {
            newTooltipValues.thumbnail = newValue;
        }

        if( newTooltipValues.title === '' ) {
            delete newTooltipValues.title;
        }
        if( newTooltipValues.section === '' ) {
            delete newTooltipValues.section;
        }
        if( newTooltipValues.base === '' || newTooltipValues.base === 'standard' ) {
            delete newTooltipValues.base;
        }
        if( newTooltipValues.thumbnail === '' || newTooltipValues.thumbnail === 'standard' ) {
            delete newTooltipValues.thumbnail;
        }

        props.onChange( applyFormat(
            props.value,
            {
                type: WikiFormatName,
                attributes: newTooltipValues,
            }
        ) );
    }

    const onClickToolbarButton = () => {
        props.onChange( toggleFormat(
            props.value,
            {
                type: WikiFormatName
            }
        ) );
    }

    return (
        <>
            { ! isActive && (
                <BlockControls>
                    <ToolbarGroup>
                        <ToolbarButton
                            icon={ 'admin-comments' }
                            title={ _x( 'add Wiki Tooltip', 'editor popup', 'wp-wiki-tooltip' ) }
                            onClick={ onClickToolbarButton }
                            isActive={ isActive }
                        />
                    </ToolbarGroup>
                </BlockControls>
            ) }
            { isActive && (
                <>
                    <BlockControls>
                        <ToolbarGroup>
                            <ToolbarButton
                                icon={ 'welcome-comments' }
                                title={ _x( 'remove Wiki Tooltip', 'editor popup', 'wp-wiki-tooltip' ) }
                                onClick={ onClickToolbarButton }
                                isActive={ isActive }
                            />
                        </ToolbarGroup>
                    </BlockControls>
                    <Popover
                        headerTitle={ _x('WP Wiki Tooltip', 'editor popup', 'wp-wiki-tooltip' ) }
                        className={ 'wiki-tooltip-data-popover' }
                        anchor={ anchorRef }
                        placement={ 'bottom-center' }
                        noArrow={ false }
                        offset={ 5 }
                    >
                        { ! ( anchorRef instanceof HTMLUnknownElement ) && (
                            <>
                                <p className={ 'wiki-tooltip-head' }>{ _x( 'New tooltip has been created. Click element again to modify its settings.', 'editor popup', 'wp-wiki-tooltip' ) }</p>
                            </>
                        ) }
                        { ( anchorRef instanceof HTMLUnknownElement ) && (
                            <>
                                <p className={ 'wiki-tooltip-head' }>{ _x( 'Change tooltip settings here. Changes are stored immediately.', 'editor popup', 'wp-wiki-tooltip' ) }</p>
                                <TextControl
                                    label={ _x('Different Wiki page title', 'editor popup', 'wp-wiki-tooltip' ) }
                                    help={ _x('Enter the title of the requested Wiki page if it differs from the selected text.', 'editor popup', 'wp-wiki-tooltip' ) }
                                    className={ 'wiki-tooltip-input-title' }
                                    value={ tooltipValue.title }
                                    onChange={ onChangeTitle }
                                />
                                <TextControl
                                    label={ _x('Section title', 'editor popup', 'wp-wiki-tooltip' ) }
                                    help={ _x('Enter the title (anchor) of the requested section in Wiki page.', 'editor popup', 'wp-wiki-tooltip' ) }
                                    className={ 'wiki-tooltip-input-section' }
                                    value={ tooltipValue.section }
                                    onChange={ onChangeSection }
                                />
                                <SelectControl
                                    label={ _x('Wiki base', 'editor popup', 'wp-wiki-tooltip' ) }
                                    help={ _x('Select one of the defined Wiki bases. Visit the settings page to create a new one.', 'editor popup', 'wp-wiki-tooltip' ) }
                                    className={ 'wiki-tooltip-input-base' }
                                    value={ tooltipValue.base }
                                    onChange={ onChangeBase }
                                    options={ BaseList }

                                />
                                <SelectControl
                                    label={ _x('Show thumbnail', 'editor popup', 'wp-wiki-tooltip' ) }
                                    help={ _x('Show a thumbnail in the tooltip?','editor popup', 'wp-wiki-tooltip' ) }
                                    className={ 'wiki-tooltip-input-thumbnail' }
                                    value={ tooltipValue.thumbnail }
                                    onChange={ onChangeThumbnail }
                                    options={ [
                                        { value: 'standard', label: _x('use plugin default value', 'editor popup', 'wp-wiki-tooltip' ) },
                                        { value: '', label: '---', disabled: true },
                                        { value: 'on', label: _x('yes', 'editor popup', 'wp-wiki-tooltip' ) },
                                        { value: 'off', label: _x('no', 'editor popup', 'wp-wiki-tooltip' ) },
                                    ] }

                                />
                            </>
                        ) }
                    </Popover>
                </>
            ) }
        </>
    );
};

registerFormatType(
    WikiFormatName,
    {
        attributes: {
            title: 'title',
            section: 'section',
            base: 'base',
            thumbnail: 'thumbnail',
        },

        title: 'WP Wiki Tooltip',
        tagName: WikiFormatTag,
        className: WikiFormatClass,
        edit: WikiTooltipEdit,
    }
);
